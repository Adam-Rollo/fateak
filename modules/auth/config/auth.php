<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(

	'driver'       => 'ORM',
	'hash_method'  => 'sha256',
	'hash_key'     => 'partitioniscrazy',
	'lifetime'     => 1209600,
	'session_type' => 'redis',
	'session_key'  => 'auth_user',
	'roles_session_key'  => 'auth_user_roles',
	'permissions_session_key'  => 'auth_user_permissions',

	// Username/password combinations for the Auth File driver
	'users' => array(
		// 'admin' => 'b3154acf3a344170077d11bdb5fff31532f679a1919e716a02',
	),

);
