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
    try {
        $db = new PDO(db_build_dsn($config), $config['user'], $config['password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return $db;
    }
    catch (PDOException $exception) {
        show_error($exception);
    }
}

/**
 * Build PDO DSN constructor string
 * 
 * @param array $config
 * @return string
 */
function db_build_dsn (array $config) {
    if ($dsn = array_get($config, 'dsn')) {
        return $dsn;
    }
    
    $attributes = array_exclude($config, array('driver', 'user', 'password'));
    $attributes['dbname'] = $attributes['name'];
    
    unset($attributes['name']);
    
    foreach ($attributes as $key => $value) {
        $attributes[$key] = "$key=$value";
    }
    
    $attributes = implode(';', $attributes);
    
    $driver = array_get($config, 'driver', 'mysql');
    
    return "$driver:$attributes";
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
 * @return array
 */
function db_select ($query, array $data = array(), $one = false, PDO $pdo = null) {
    $statement = db_prepare($query, $data, $pdo);
    
    $result = $one ? $statement->fetch() : $statement->fetchAll();
    
    return $result ? $result : array();
}

/**
 * Insert a row in database
 * 
 * @param string $table
 * @param array $data
* @param \PDO $pdo
 * @return int
 */
function db_insert ($table, array $data, PDO $pdo = null) {
    if (!$table || empty($data)) {
        return 0;
    }
    
    $query = 'INSERT INTO %s (%s) VALUES (%s)';
    
    list($keys, $placeholders) = db_prepare_insert($data);
    
    $statement = db_prepare(
        sprintf($query, $table, $keys, $placeholders), 
        array_values($data), 
        $pdo
    );
    
    if (!$pdo) {
        $pdo = db('active');
    }
    
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
function db_update ($table, array $data, array $where = array(), PDO $pdo = null) {
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
    $statement = db_prepare(sprintf($query, $update, $where['query']), $values, $pdo);
    
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
    
    $statement = db_prepare(sprintf($query, $where['query']), $where['data'], $pdo);
    
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
function db_prepare ($query, array $parameters, PDO $pdo = null) {
    $pdo = $pdo ? $pdo : db('active');
    
    try {
        $statement = $pdo->prepare($query);
        $statement->execute($parameters); 
    }
    catch (PDOException $e) {
        db_prepare_exception($e, $query, $parameters);
    }
    
    return $statement;
}