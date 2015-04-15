<?php

function users ($user = null) {
	static $repo;
	$repo or $repo = repo(array(
		'webkill' => array(
			'name' => 'Вася',
			'age' => 25
		),
		'striker' => array(
			'name' => 'Петя',
			'age' => 21
		)
	), true);
	
	return $repo($user);
}

function actions_init () {
	// Тут мы вернемся позже
}

function action_index () {
	$users = users();
	
	view('main', array(
		'title' => 'Пользователи сайта',
		'users' => $users,
		'view' => 'users'
	));
}

function action_show_user($user = '') {
	if (!$user = users($user)) {
		return false;
	}
	
	view('main', array(
		'title' => 'Пользователь ' . $user['name'],
		'user' => $user,
		'view' => 'user'
	));
}