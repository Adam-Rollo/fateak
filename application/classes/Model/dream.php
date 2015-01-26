<?php

class Model_dream extends ORM
{

    protected $_table_columns = array(
        'id' => array('type' => 'int'),
        'string' => array('type' => 'string'),
        'number' => array('type' => 'int'),
    );
}
