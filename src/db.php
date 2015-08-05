<?php namespace db;

use Exception;
use PDO;

use storage;

/**
 * Database functions
 * 
 * @package mini_framework
 * @require storage
 */

/**
 * Init database
 * 
 * @param array $data
 */
function init (array $data) {
    db($data);
}

/**
 * Database storage
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function db ($key = null, $value = null) {
    static $repo = null;
    
    $repo or $repo = storage\repo();
    
    return $repo($key, $value);
}

/**
 * Connect to a database
 * 
 * @param string
 */
function connect ($group = 'default') {
    $config = db($group);
    
    if (isset($config['connection'])) {
        return;
    }
    
    if (!$config) {
        throw new Exception("Database group '$group' does not exists!");
    }
    
    $db = create_connection($config);
    
    db("$group.connection", $db);
    db('active', $db);
}

/**
 * Create PDO MySQL database connection
 * 
 * @param array $config
 * @return \PDO
 */
function create_connection ($config) {
    $user = array_get($config, 'user');
    $pass = array_get($config, 'password');
    
    $db = new PDO(build_dsn($config), $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    return $db;
}

/**
 * Build PDO DSN constructor string
 * 
 * @param array $config
 * @return string
 */
function build_dsn (array $config) {
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
function prepare_exception (Exception $e, $query, array $data) {
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
function select ($query, array $data = array(), $one = false, PDO $pdo = null) {
    $statement = prepare($query, $data, $pdo);
    
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
function insert ($table, array $data, PDO $pdo = null) {
    if (!$table || empty($data)) {
        return 0;
    }
    
    list($keys, $placeholders) = prepare_insert($data);
    
    $query     = "INSERT INTO $table ($keys) VALUES ($placeholders)";
    $statement = prepare($query, array_values($data), $pdo);
    
    $pdo = $pdo ? $pdo : db('active');
    
    return $statement->rowCount() ? $pdo->lastInsertId() : 0;
}

/**
 * Prepare insert data
 * 
 * @param array $data
 * @return array
 */
function prepare_insert (array $data) {
    $keys = implode(',', array_map('\db\escape', array_keys($data)));
    
    $placeholders = chop(
        str_repeat('?,', count($data)), ','
    );
    
    return array($keys, $placeholders);
}

/**
 * Escape the key
 * 
 * @param string $value
 * @return string
 */
function escape ($value) {
    return "`$value`";
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
function update ($table, array $data, array $where = array(), PDO $pdo = null) {
    if (empty($data)) {
        return false;
    }
    
    $where  = prepare_where($where);
    $values = array_merge(
        array_values($data), 
        array_values($where['data'])
    );
    
    $update = prepare_update($data);
    $query  = "UPDATE $table SET $update {$where['query']}";
    
    return prepare($query, $values, $pdo)->rowCount() > 0;
}

/**
 * Prepare update statement
 * 
 * @param array $data
 * @return string
 */
function prepare_update (array $data) {
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
function delete ($table, array $where = array(), PDO $pdo = null) {
    $query = "DELETE FROM `$table` %s";
    $where = prepare_where($where);
    
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
function prepare_where (array $where = array()) {
    if (empty($where)) {
        return array('query' => '', 'data' => $where);
    }
    
    $query = 'WHERE ';
    
    foreach (array_keys($where) as $field) {
        $query .= prepare_where_field($field);
    }
    
    return array(
        'query' => trim(chop($query, 'AND OR')),
        'data'  => array_values($where)
    );
}

/**
 * Utility function for prepare_where
 * 
 * @param string $field
 * @return string
 */
function prepare_where_field ($field) {
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
        $statement->execute($parameters); 
    }
    catch (PDOException $e) {
        prepare_exception($e, $query, $parameters);
    }
    
    return $statement;
}

/**
 * Execute a SQL query
 * 
 * @param string $query
 * @param \PDO $pdo
 * @return int|bool
 */
function query ($query, PDO $pdo = null) {
    $pdo = $pdo ? $pdo : db('active');
    
    return $pdo->exec($query);
}