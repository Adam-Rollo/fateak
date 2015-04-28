<?php

class Webservice_Menu extends Webservice
{
    /**
     * Get root menus
     */
    public function get_roots($params)
    {
        return array(0 => array('name' => '游泳圈'), 1 => array('name' => '比基尼'));
        
        return Menu::root_menus();
    }
}
