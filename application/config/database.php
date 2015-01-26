<?php

return array
(
    'default' => array
    (
        'type'       => 'PDO',
        'connection' => array(
            'dsn' => 'mysql:dbname=crazy;host=127.0.0.1',
            'username'   => 'root',
            'password'   => '123456',
            'persistent' => FALSE,
            'options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"),
        ),
    ),

);
