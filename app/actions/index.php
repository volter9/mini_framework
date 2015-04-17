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
	layout('pages/index', array(
		'title' => 'Главная страница'
	));
}

/**
 * Page action
 */
function action_page () {
	layout('pages/index', array(
		'title' => 'Доп. страница',
	));
}