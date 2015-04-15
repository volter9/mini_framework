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
	view('main', array(
		'title' => 'Главная страница',
		'view' => 'index'
	));
}

/**
 * Page action
 */
function action_page () {
	view('main', array(
		'title' => 'Доп. страница',
		'view' => 'page'
	));
}