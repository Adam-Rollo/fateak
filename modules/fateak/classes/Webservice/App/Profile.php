<?php

class Webservice_App_Profile extends Webservice_App
{
    /**
     * Get User's information
     */ 
    public function get_profiles($params)
    {
        $this->frequency(30, 3);

        $this->check_params($params, 'uid', 'token');
        
        $profile = ORM::factory('User_Profile', $params['uid'])->as_array();

        Module::action('api_get_profiles', $profile);

        return $profile;
    }
}
