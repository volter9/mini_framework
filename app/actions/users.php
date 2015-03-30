<?php

function users ($user = null) {
	static $repo;
	$repo or $repo = repo([
		'webkill' => [
			'name' => 'Вася',
			'age' => 25
		],
		'striker' => [
			'name' => 'Петя',
			'age' => 21
		]
	], true);
	
	return $repo($user);
}

function actions_init () {
	// Тут мы вернемся позже
}

function action_index () {
	$users = users();
	
	view('main', [
		'title' => 'Пользователи сайта',
		'users' => $users,
		'view' => 'users'
	]);
}

function action_show_user($user = '') {
	if ($user === '' || ($user = users($user)) === false) {
		return false;
	}
	
	view('main', [
		'title' => 'Пользователь ' . $user['name'],
		'user' => $user,
		'view' => 'user'
	]);
}