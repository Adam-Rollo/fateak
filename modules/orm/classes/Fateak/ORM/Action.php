<?php

class Fateak_ORM_Action
{
    public static function create_user($return)
    {
        try
        {
            // Profiles
            $profiles = ORM::factory("User_Profile");
            $profiles->id = $return['uid'];
            $profiles->save(); 
        } 
        catch (Exception $e)
        {
            $return['errors'][] = $e->getLine . " " . $e->getMessage();    
        }
    }
}
