<?php
/**
 * Webservice base class
 *
 * All webservice api should extend this class.
 *
 * @package    Basement\Webservice
 * @author     Fateak - Rollo
 */
abstract class Fateak_Webservice {

    protected static $instance = array();

    public static function execute($api_class, $api_function, $params = array())
    {
        try 
        {
            if (isset(self::$instance[$api_class])) 
            {
                $api_object = self::$instance[$api_class];
            } 
            else 
            {
                $api_class = 'Webservice_' . ucfirst($api_class);
                if ( ! class_exists($api_class)) 
                {
                    throw new Kohana_Exception("This API class is not exist.");
                }
                $api_object = new $api_class();
                self::$instance[$api_class] = $api_object;
            }

            if (! method_exists($api_object, $api_function)) 
            {
                throw new Kohana_Exception("This Method is not exist.");
            }

            $result = $api_object->$api_function($params);
            $wr = new Webservice_Result(Webservice_Result::SUCCESS, $result); 

        } 
        catch (Webservice_Exception $e) 
        {
            $error = $e->getMessage();
            $wr = new Webservice_Result(Webservice_Result::FAILURE, $error, $params); 
        } 
        catch (Exception $e) 
        {
            $error = $e->getMessage();
            $line = $e->getLine();
            $wr = new Webservice_Result(Webservice_Result::FAILURE, $line . " : " . $error);
        }

        return $wr;
    }

    public function check_params()
    {
        $args = func_get_args();
        $params = array_shift($args);
        foreach ($args as $arg) 
        {
            if (! isset($params[$arg]) ) 
            {
                throw new Webservice_Exception('You must delivery param :param !', array(':param' => $arg));
            }
        }
    }

    public function frequency($duration, $times)
    {
        $redis = FRedis::instance();

        $key = Request::current()->action() . '.' . Request::$client_ip;

        $result = $redis->lua('limitip', array($key, $duration, $times, time()), 1);

        if ($result == 'N')
        {
            throw new Webservice_Exception('You send request too frequently.');
        }

        return true;
    }

    public function easy_md5($user_id, $auth_token)
    {
        $webservice_config = Kohana::$config->load('webservice');

        $right_token = md5($webservice_config['easy_salt'] . $user_id);

        if ($auth_token != $right_token)
        {
            throw new Webservice_Exception('Your auth token is wrong.');
        }
    }

    public function rsa_decode($encrypted)
    {
        $webservice_config = Kohana::$config->load('webservice');

        $decrypted = "";

        openssl_private_decrypt(base64_decode($encrypted), $decrypted, $webservice_config['rsa_private_key']);

        return $decrypted;
    }

    public function token_auth($user_id, $token, $agent_filter = true)
    {
        $redis = FRedis::instance();
        $token_key = "auth:token:" . $user_id;

        $user_info = $redis->hMGet($token_key, array('device', 'ip', 'token'));

        if ($token != $user_info['token'])
        {
            throw new Webservice_Exception('Token is invalid.');
        }

        if ($agent_filter && (Request::$client_ip != $user_info['ip'] || Request::$user_agent != $user_info['device']))
        {
            throw new Webservice_Exception('invalid Environment.');
        }
    }

}
