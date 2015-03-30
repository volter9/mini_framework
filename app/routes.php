<?php

/**
 * Your routes.
 */
 
route('GET #index /', 'app/actions/index');
route('GET #page /test/page', 'app/actions/index:page');

route('GET #users /users', 'app/actions/users');
route('GET #show_user /users/:any', 'app/actions/users:show_user');