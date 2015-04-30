<?php

class Webservice_App_Auth extends Webservice_App
{
    
    /**
     * Get Config
     */
    public function getconfigs($params)
    {
        $this->check_params($params, 'system');

        $app_config = Kohana::$config->load('app');

        $config = $app_config[$params['system']];

        return $config;
    }

    /**
     * User Login from APP
     */
    public function login($params)
    {
        $this->check_params($params, 'system', 'data');

        $data = JSON::decode($this->rsa_decode($params['data']));

        $auth = Auth::instance();

        $result = array();

        if ($user = $auth->login_by_app($data['account'], $data['password']))
        {
            $result['logined'] = 'Y';
            $result['token'] = $user['token']; 
            $result['userID'] = $user['id'];
            unset($result['token']);
            unset($result['userID']);
            $result['userInfo'] = $user;
        }
        else
        {
            $result['logined'] = 'N';
            $result['message'] = __('Wrong account information.');
        }

        return $result;
    }

    /**
     * User Register
     */
    public function send_code($params)
    {
        $this->frequency(30, 3);

        $this->check_params($params, 'account');

        $account = trim($params['account']);

        $random_code = Text::random('numeric', 6); 

        if (preg_match('/^[0-9]+$/', $account))
        {
            if (strlen($account) <> 11)
            {
                throw new Webservice_Exception('Wrong telephone number format.');
            }

            // Do send message
            return "telephone";
        }
        else
        {
            $redis = FRedis::instance();

            $account_key = "account:vcode:" . $account;

            $redis->set($account_key, $random_code);

            $email_config = Kohana::$config->load('email');
            
            $vars = array('code' => $random_code, 'site_name' => $email_config['email_site_name']);

            Email::send_email($account, __('Please check your validation code.'), 'sendcode', $vars);
 
            return "email";
        }
    }
}
