<?php

class Model_ptest extends ORM
{

    protected $_table_columns = array(
        'id' => array('type' => 'int'),
        'str' => array('type' => 'string'),
        'box' => array('type' => 'int'),
    );
}
