<?php

class Fateak_Orm_Action
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
