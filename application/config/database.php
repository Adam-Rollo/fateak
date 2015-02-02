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

    'redis_default' => array
    (
        'host' => '127.0.0.1',
        'port' => '6379',
    ),

    'redis_broaqi' => array
    (
        'host' => '192.168.1.86',
        'port' => '6379',
    ),

);
