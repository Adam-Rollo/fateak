<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User extends Model_Auth_User 
{
    /**
     * PDO must declare table columns
     */
    protected $_table_columns = array(
        'id' => array('type' => 'int'),
        'username' => array('type' => 'string'),
        'email' => array('type' => 'string'),
        'password' => array('type' => 'string'),
        'status' => array('type' => 'int'),
        'timezone' => array('type' => 'string'),
        'language' => array('type' => 'string'),
        'logins' => array('type' => 'int'),
        'last_login' => array('type' => 'int'),
        'ips' => array('type' => 'string'),
    );

    /**
     * explicit db group. Developers could rewrite or change the value
     */
    protected $_db_group = 'default';

} // End User Model
