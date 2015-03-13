<?php

class Model_User_Profile extends ORM
{
    /**
     * PDO must declare table columns
     */
    protected $_table_columns = array(
        'id' => array('type' => 'int'),
        'name' => array('type' => 'string'),
        'dob' => array('type' => 'string'),
        'avatar' => array('type' => 'string'),
        'bio' => array('type' => 'string'),
        'country' => array('type' => 'string'),
        'province' => array('type' => 'string'),
        'city' => array('type' => 'string'),
    );

    /**
     * explicit db group. Developers could rewrite or change the value
     */
    protected $_db_group = 'default';


}
