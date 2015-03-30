<?php

/**
 * Initialize action
 */
function actions_init () {
	// Load everything you need
}

/**
 * Index action
 */
function action_index () {
	view('main', [
		'title' => 'Главная страница',
		'view' => 'index'
	]);
}

/**
 * Page action
 */
function action_page () {
	view('main', [
		'title' => 'Доп. страница',
		'view' => 'page'
	]);
}