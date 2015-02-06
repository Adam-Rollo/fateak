<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Role extends Model_Auth_Role 
{

    /**
     * PDO must declare table columns
     */
    protected $_table_columns = array(
        'id' => array('type' => 'int'),
        'name' => array('type' => 'string'),
        'description' => array('type' => 'string'),
    );

} // End Role Model
