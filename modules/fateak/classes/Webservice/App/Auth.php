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
        $this->frequency(30, 3);

        $this->check_params($params, 'system', 'data');

        $data = JSON::decode($this->rsa_decode($params['data']));

        return $this->_login($data['account'], $data['password']);
    }

    /**
     * Login detail
     */
    protected function _login($account, $password)
    {
        $auth = Auth::instance();

        $result = array();

        if ($user = $auth->login_by_app($account, $password))
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
        $this->frequency(3600, 10);

        $this->check_params($params, 'account');

        $account = trim($params['account']);

        $random_code = Text::random('numeric', 6); 

        if (preg_match('/^[0-9]+$/', $account))
        {
            if (! Valid::phone($account, array(11)))
            {
                throw new Webservice_Exception('Wrong telephone number format.');
            }

        }
        else
        {
            if (! Valid::email($account))
            {
                throw new Webservice_Exception('Wrong E-mail format.');
            }

            $email_config = Kohana::$config->load('email');
            
            $vars = array('code' => $random_code, 'site_name' => $email_config['email_site_name']);

            Email::send_email($account, __('Please check your validation code.'), 'sendcode', $vars, true);
        }

        $redis = FRedis::instance();

        $account_key = "account:vcode:" . $account;

        $redis->set($account_key, $random_code);

        $redis->setTimeout($account_key, 1800);
    }

    /**
     * User register
     */
    public function register($params)
    {
        $this->check_params($params, 'system', 'data', 'info');

        $info_array = JSON::decode($this->rsa_decode($params['info']));
        $pass_array = JSON::decode($this->rsa_decode($params['data']));

        $this->_check_password($pass_array);

        $this->frequency(30, 2);

        $this->_check_validation_code($info_array['account'], $info_array['code']);

        if (preg_match('/^[0-9]+$/', $info_array['account']))
        {
            $user = ORM::factory("User")
                ->where("username", "=", $info_array['account'])
                ->find();

            $info_array['username'] = $info_array['account'];
            $info_array['email'] = 'unknown@fateak.com';
        }
        else
        {
            $user = ORM::factory("User")
                ->where("email", "=", $info_array['account'])
                ->find();

            $info_array['email'] = $info_array['account'];
            $info_array['username'] = "-";
        }

        if ($user->loaded())
        {
            throw new Webservice_Exception('Your account has been registered.');
        }

        try
        {
            $account = $info_array['account'];
            unset($info_array['account']);

            $user = ORM::factory('User')
                    ->create_user(array_merge($info_array, $pass_array), array('username','password','email','language'));
            $user->add('roles', 1);
        } 
        catch (ORM_Validation_Exception $e) 
        {
            $errors = implode(', ', $e->errors('models', TRUE));
            throw new Webservice_Exception($errors);
        }

        return $this->_login($account, $pass_array['password']);
    }

    /**
     * forgot password
     */
    public function forgot($params)
    {
        $this->check_params($params, 'system', 'data', 'info');

        $info_array = JSON::decode($this->rsa_decode($params['info']));
        $pass_array = JSON::decode($this->rsa_decode($params['data']));

        $auth = Auth::instance();

        $this->_check_password($pass_array);

        $this->frequency(30, 2);

        $this->_check_validation_code($info_array['account'], $info_array['code']);
            
        $user = ORM::factory("User");
        $user->where($user->unique_key($info_array['account']), "=", $info_array['account'])->find();

        if (! $user->loaded())
        {
            throw new Webservice_Exception('This account is not exist.');
        }
        else
        {
            try
            {
                $user->update_user($pass_array);
            } 
            catch (ORM_Validation_Exception $e) 
            {
                $errors = implode(', ', $e->errors('models', TRUE));
                throw new Webservice_Exception($errors);
            }
        }

    }

    /**
     * 修改密码
     */
    public function change($params)
    {
        $this->check_params($params, 'data', 'uid', 'token');

        $pass_array = JSON::decode($this->rsa_decode($params['data']));

        $original_password = $pass_array['ori'];
        $new_password = $pass_array['new'];
        $pass_array = array('password' => $new_password, 'password_confirm' => $new_password);

        $this->_check_password($pass_array);

        $this->frequency(60, 2);

        $user = ORM::factory('User', $params['uid']);
        
        if (! $user->loaded())
        {
            throw new Webservice_Exception('This account is not exist.');
        }

        $auth = Auth::instance();

        if ($user->password == $auth->hash($original_password))
        {
           try
            {
                $user->update_user($pass_array);
            } 
            catch (ORM_Validation_Exception $e) 
            {
                $errors = implode(', ', $e->errors('models', TRUE));
                throw new Webservice_Exception($errors);
            }
        }
        else
        {
            throw new Webservice_Exception('Original password is wrong.');
        }

    }

    /**
     * Password checker
     */
    protected function _check_password($pass_array)
    {
        if (! preg_match('/[a-zA-Z]+/', $pass_array['password']))
        {
            throw new Webservice_Exception('Password must contains alpha.');
        }

        if (strlen($pass_array['password']) < 8)
        {
            throw new Webservice_Exception('Password must contains more than 8 character.');
        }
        
        if ($pass_array['password'] != $pass_array['password_confirm'])
        {
            throw new Webservice_Exception('Wrong password confirmation.');
        }
    } 

    /**
     * Validation code checker
     */
    protected function _check_validation_code($account, $code)
    {
        $redis = FRedis::instance();

        $account_key = "account:vcode:" . $account;

        $validation_code = $redis->get($account_key);

        if ($validation_code != $code)
        {
            throw new Webservice_Exception('Wrong validation code.');
        }
    }

}
