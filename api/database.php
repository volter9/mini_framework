<?php

/**
 * Query modes
 * 
 * @const int DB_ONE       Select only one row
 * @const int DB_CRUD      Return boolean from CRUD operation
 * @const int DB_INSERT    Return insert id from insert query
 * @const int DB_SIMPLE    Return simple in case of success true
 * @const int DB_AGGREGATE Return first column of query
 */
define('DB_ONE'      , 1);
define('DB_CUD'      , 2);
define('DB_INSERT'   , 4);
define('DB_SIMPLE'   , 8);
define('DB_AGGREGATE', 16);

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
	$host     = $config['host'];
	$user     = $config['user'];
	$name     = $config['name'];
	$password = $config['password'];
	$charset  = $config['charset'];
	
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
 * Perform an SQL query
 * 
 * @param string $query
 * @param array $data
 * @param int $mode
 * @return array|int|bool
 */
function db_query ($query, array $data = [], $mode = 0) {
	$db = db('active');
	
	try {
		$statement = $db->prepare($query);
		$statement->execute($data);	
	}
	catch (PDOException $e) {
		db_prepare_exception($e, $query, $data);
	}
	
	return db_return($mode, $statement, $db);
}

/**
 * Validate query for mode and return appropriate data
 * 
 * @param int $mode
 * @param PDOStatement $statement
 * @param PDO $db
 * @return mixed
 */
function db_return ($mode, PDOStatement $statement, PDO $db) {
	static $modes = null;
	
	$modes or $modes = [DB_ONE, DB_SIMPLE, DB_AGGREGATE, 0];
	$count = $statement->rowCount();
	
	if (in_array($mode, $modes)) {
		if ($count == 0) return false;
	}
	
	switch ($mode) {
		case DB_SIMPLE:
			return true;
		
		case DB_ONE:
			return $statement->fetch(PDO::FETCH_ASSOC);
		
		case DB_CUD:
			return $count >= 0;
		
		case DB_INSERT:
			return $db->lastInsertId();
		
		case DB_AGGREGATE:
			return $statement->fetchColumn();
		
		default:
			return $statement->fetchAll(PDO::FETCH_ASSOC);
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
 * @param bool $one
 * @return array|false
 */
function db_select ($query, array $data = [], $one = false) {
	return db_query($query, $data, (int)$one);
}

/**
 * Insert a row in database
 * 
 * @param string $table
 * @param array $data
 * @return int
 */
function db_insert ($table, array $data) {
	if (!$table || empty($data)) {
		return false;
	}
	
	$query = "INSERT INTO $table (%s) VALUES (%s)";
	
	list($keys, $placeholders) = db_prepare_insert($data);
	
	return db_query(
		sprintf($query, $keys, $placeholders), 
		array_values($data), 
		DB_INSERT
	);
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
	
	return [
		$keys,
		$placeholders
	];
}

/**
 * Update row(s) in database
 * 
 * @param string $table
 * @param array $data
 * @param array $where
 * @return bool
 */
function db_update ($table, array $data, array $where = []) {
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
	
	return db_query(
		sprintf($query, $update, $where['query']), 
		$values, 
		DB_CUD
	);
}

/**
 * Prepare update statement
 * 
 * @param array $data
 * @return string
 */
function db_prepare_update (array $data) {
	$update = [];
	
	foreach ($data as $key => $value) {
		$update[] = "$key = ?";
	}
	
	return implode(',', $update);
}

/**
 * Delete row(s) from database
 * 
 * @param string $table
 * @param array $where
 * @return bool
 */
function db_delete ($table, array $where = []) {
	$query = "DELETE FROM $table %s";
	$where = db_prepare_where($where);
	
	return db_query(
		sprintf($query, $where['query']), 
		$where['data'], 
		DB_CUD
	);
}

/**
 * Prepare where statement
 * Condition by default is 'AND'
 * 
 * @param array $where
 * @return array
 */
function db_prepare_where (array $where = []) {
	if (empty($where)) {
		return ['query' => '', 'data' => $where];
	}
	
	$query = 'WHERE ';
	
	foreach (array_keys($where) as $field) {
		$query .= db_prepare_where_field($field);
	}
	
	return [
		'query' => trim(chop($query, 'AND OR')),
		'data' => array_values($where)
	];
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
	
	return "$field $type ? $condition ";
}