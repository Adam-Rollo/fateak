<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Token extends Model_Auth_User_Token {

    /**
     * PDO must declare table columns
     */
    protected $_table_columns = array(
        'id' => array('type' => 'int'),
        'user_id' => array('type' => 'int'),
        'user_agent' => array('type' => 'string'),
        'token' => array('type' => 'string'),
        'created' => array('type' => 'int'),
        'expires' => array('type' => 'int'),
    );

    /**
     * explicit db group. Developers could rewrite or change the value
     */
    protected $_db_group = 'default';



} // End User Token Model
