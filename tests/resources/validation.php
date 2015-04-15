<?php

return array(
	'rules' => array(
		'username' => 'required|max_length:20|alpha_dash',
		'password' => 'required|compare:confirm',
		'confirm' => 'required|alpha_dash|max_length:20',
		'mail' => 'required|valid_mail'
	),
	
	'fields' => array(
		'username' => 'Username',
		'password' => 'Password',
		'confirm' => 'Password Confirmation',
		'mail' => 'Email'
	)
);