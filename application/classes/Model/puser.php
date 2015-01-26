<?php

class Model_puser extends ORM
{

    protected $_table_columns = array(
        'id' => array('type' => 'int'),
        'fname' => array('type' => 'string'),
        'lname' => array('type' => 'string'),
        'signed' => array('type' => 'int'),
    );
}
