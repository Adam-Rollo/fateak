<?php

class Fateak_WebHelper
{
    /**
     * 限制Group的访问频率
     */
    public static function ip_frequency($group, $duration, $times)
    {
        $redis = FRedis::instance();

        $key = $group . '.' . Request::$client_ip;

        $result = $redis->lua('limitip', array($key, $duration, $times, time()), 1);

        if ($result == 'N')
        {
            throw new WebHelper_Exception('You send request too frequently.');
        }
    }

    public static function send_email_code($email)
    {
        if (! Valid::email($email))
        {
            throw new WebHelper_Exception('Wrong E-mail format.');
        }

        $random_code = Text::random('numeric', 6); 

        $email_config = Kohana::$config->load('email');

        $vars = array('code' => $random_code, 'site_name' => $email_config['email_site_name']);

        Email::send_email($email, __('Please check your validation code.'), 'sendcode', $vars, true);

        $redis = FRedis::instance();

        $account_key = "account:vcode:" . $email;

        $redis->set($account_key, $random_code);

        $redis->setTimeout($account_key, 1800);
    }
}
