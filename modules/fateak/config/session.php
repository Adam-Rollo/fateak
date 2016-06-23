<?php defined('SYSPATH') OR die('No direct script access.');

return array(

    'redis' => array(
        'server' => 'default',
        'lifetime' => 3600, // lifetime in auth is autologin's lt. this lifetime is session lifetime for expires;
    ),

);
