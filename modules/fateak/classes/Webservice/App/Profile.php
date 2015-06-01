<?php

class Webservice_App_Profile extends Webservice_App
{
    /**
     * Get User's information
     */ 
    public function get_profiles($params)
    {
        $this->frequency(30, 10);

        $this->check_params($params, 'uid', 'token');

        $this->token_auth($params['uid'], $params['token']);
        
        $profile = ORM::factory('User_Profile', $params['uid'])->as_array();

        Module::action('api_get_profiles', $profile);

        return $profile;
    }

    /**
     * Upload Avatar
     */
    public function avatar($params)
    {
        $this->frequency(30, 10);

        $this->token_auth($params['uid'], $params['token'], false);

        $this->get_permissions($params['uid']);
 
        $profile = ORM::factory('User_Profile', $params['uid']);

        $path = Request::factory('upload')->execute()->body();

        $profile->avatar = JSON::encode(array($path));

        $profile->save();

        return $profile->avatar;
    }
}
