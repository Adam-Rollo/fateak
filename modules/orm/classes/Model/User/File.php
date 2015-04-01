<?php

class Model_User_File extends ORM
{
    /**
     * PDO must declare table columns
     */
    protected $_table_columns = array(
        'id' => array('type' => 'int'),
        'user_id' => array('type' => 'string'),
        'name' => array('type' => 'string'),
        'path' => array('type' => 'string'),
        'description' => array('type' => 'string'),
        'updated' => array('type' => 'int'),
        'privilege' => array('type' => 'int'),
        'parent_id' => array('type' => 'int'),
        'lft' => array('type' => 'int'),
        'rgt' => array('type' => 'int'),
        'lvl' => array('type' => 'int'),
        'scope' => array('type' => 'int'),
    );

    /**
     * explicit db group. Developers could rewrite or change the value
     */
    protected $_db_group = 'default';

}
