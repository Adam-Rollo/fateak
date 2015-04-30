<?php

class Task_Emails extends Minion_Task
{
    protected $_options = array(
    );

    protected function _execute(array $params)
    {
        try
        {
            $redis = FRedis::instance();

            $switch = $redis->get('queue:email:switch'); 

            while ($switch == 'Y')
            {
                $email_content = $redis->brPop('queue:confirmation.email', 'queue:notification.email', 0); 

                $email_array = unserialize($email_content[1]);

                $vars = JSON::decode(base64_decode($email_array['vars']));

                $parser = new FTparser(base64_decode($email_array['tpl']), 'email', base64_decode($email_array['lang']));

                $refresh = base64_decode($email_array['refresh']) == 'Y' ? true : false;
                $body = $parser->parse($vars, $refresh); 

                $email = Email::factory()
                    ->subject(base64_decode($email_array['title']))
                    ->to(base64_decode($email_array['email']))
                    ->message($body);

                $email->send();
            }

        }
        catch (Exception $e)
        {
            $log = Log::instance();
            $log->add(Log::ERROR, $e->getMessage());
        }
    }
}
