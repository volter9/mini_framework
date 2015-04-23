<?php

/**
 * Database storage
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function db ($key = null, $value = null) {
    static $repo = null;
    $repo or $repo = repo();
    
    return $repo($key, $value);
}

/**
 * Connect to a database
 * 
 * @param string
 */
function db_connect ($group = 'default') {
    if (db("$group.connection")) {
        return;
    }
    
    $config = db($group);
    
    if (!$config) {
        throw new Exception("Group '$group' does not exists");
    }
    
    $db = db_create_connection($config);
    
    db("$group.connection", $db);
    db('active', $db);
}

/**
 * Create PDO MySQL database connection
 * 
 * @param array $config
 * @return \PDO
 */
function db_create_connection ($config) {
    extract($config);
    
    try {
        $db = new PDO("mysql:host=$host;dbname=$name;charset=$charset", $user, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        
        return $db;
    }
    catch (PDOException $exception) {
        show_error($exception);
    }
}

/**
 * Prepare an exception
 * 
 * @param Exception $e
 * @param string $query
 * @param array $data
 */
function db_prepare_exception (Exception $e, $query, array $data) {
    $message = $e->getMessage();
    
    $query = preg_replace('/^\s+/m', '', $query);
    
    ob_start();
    var_dump($data);
    $values = preg_replace(
        '/\<\/?pre[^\>]*\>/i', '', ob_get_clean()
    );
    
    throw new Exception(
        sprintf('
            <p>%s</p>
            <p>SQL: <pre>%s</pre></p>
            <p>Values: <pre>%s</pre></p>
        ', $message, $query, $values)
    );
}

/**
 * Select information from 
 * 
 * @param string $query
 * @param array $data
 * @param bool $one
 * @param \PDO $pdo
 * @return array|false
 */
function db_select ($query, array $data = array(), $one = false, PDO $pdo = null) {
    $statement = prepare($query, $data, $pdo);
    
    if ($statement->rowCount() > 0) {
        return $one ? $statement->fetch() : $statement->fetchAll();
    }
    
    return array();
}

/**
 * Insert a row in database
 * 
 * @param string $table
 * @param array $data
* @param \PDO $pdo
 * @return int
 */
function db_insert ($table, array $data, PDO $pdo) {
    if (!$table || empty($data)) {
        return 0;
    }
    
    $query = 'INSERT INTO %s (%s) VALUES (%s)';
    
    list($keys, $placeholders) = db_prepare_insert($data);
    
    $statement = prepare(
        sprintf($query, $table, $keys, $placeholders), 
        array_values($data), 
        $pdo
    );
    
    return $statement->rowCount() ? $pdo->lastInsertId() : 0;
}

/**
 * Prepare insert data
 * 
 * @param array $data
 * @return array
 */
function db_prepare_insert (array $data) {
    $keys = implode(',', 
        array_map(function ($value) {           
            return "`$value`";
        }, array_keys($data))
    );
    
    $placeholders = chop(
        str_repeat('?,', count($data)), ','
    );
    
    return array(
        $keys,
        $placeholders
    );
}

/**
 * Update row(s) in database
 * 
 * @param string $table
 * @param array $data
 * @param array $where
 * @param \PDO $pdo
 * @return bool
 */
function db_update ($table, array $data, array $where = array(), PDO $pdo) {
    if (empty($data)) {
        return false;
    }
    
    $query = "UPDATE $table SET %s %s";
    $where = db_prepare_where($where);
    
    $values = array_merge(
        array_values($data), 
        array_values($where['data'])
    );
    
    $update = db_prepare_update($data);
    $statement = prepare(sprintf($query, $update, $where['query']), $values, $pdo);
    
    return $statement->rowCount() > 0;
}

/**
 * Prepare update statement
 * 
 * @param array $data
 * @return string
 */
function db_prepare_update (array $data) {
    $update = array();
    
    foreach ($data as $key => $value) {
        $update[] = "`$key` = ?";
    }
    
    return implode(',', $update);
}

/**
 * Delete row(s) from database
 * 
 * @param string $table
 * @param array $where
 * @param \PDO $pdo
 * @return bool
 */
function db_delete ($table, array $where = array(), PDO $pdo = null) {
    $query = "DELETE FROM `$table` %s";
    $where = db_prepare_where($where);
    
    $statement = prepare(sprintf($query, $where['query']), $where['data'], $pdo);
    
    return $statement->rowCount() > 0;
}

/**
 * Prepare where statement
 * Condition by default is 'AND'
 * 
 * @param array $where
 * @return array
 */
function db_prepare_where (array $where = array()) {
    if (empty($where)) {
        return array('query' => '', 'data' => $where);
    }
    
    $query = 'WHERE ';
    
    foreach (array_keys($where) as $field) {
        $query .= db_prepare_where_field($field);
    }
    
    return array(
        'query' => trim(chop($query, 'AND OR')),
        'data' => array_values($where)
    );
}

/**
 * Utility function for db_prepare_where
 * 
 * @param string $field
 * @return string
 */
function db_prepare_where_field ($field) {
    $condition = 'AND';
    
    if (strpos($field, '|') !== false) {
        list($field, $condition) = explode('|', $field);
    }
    
    $fragments = explode('[', $field);
    
    $field = $fragments[0];
    $type = strtoupper(trim($fragments[1], '[]'));
    
    return "`$field` $type ? $condition ";
}

/**
 * Prepare a PDO statement
 * 
 * @param string $query
 * @param array $parameters
 * @param \PDO $pdo
 * @return \PDOStatement
 */
function prepare ($query, array $parameters, PDO $pdo = null) {
    $pdo = $pdo ? $pdo : db('active');
    
    try {
        $statement = $pdo->prepare($query);
        $statement->execute($data); 
    }
    catch (PDOException $e) {
        db_prepare_exception($e, $query, $data);
    }
    
    return $statement;
}