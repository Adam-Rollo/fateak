<?php

class Task_Email extends Minion_Task
{
    protected $_options = array(
        'email' => 'shirendeyaodian@aliyun.com',
        'toName' => 'Deer User',
        'title' => 'An Email from Fateak',
        'body' => '',
        'tpl' => null,
        'vars' => null,
    );

    protected function _execute(array $params)
    {
        try
        {
            if (! is_null($params['tpl']))
            {
                $parser = new FTparser(base64_decode($params['tpl']), 'email');
                
                if (! is_null($params['vars']))
                {
                    $vars = base64_decode($params['vars']);   
                    $vars = JSON::decode($vars);
                }
                else
                {
                    $vars = array();
                }

                $body = $parser->parse($vars); 
            }
            else
            {
                $body = base64_decode($params['body']);
            }

            $email = Email::factory()
                ->subject(base64_decode($params['title']))
                ->to(base64_decode($params['email']))
                ->message($body);

            $email->send();
        }
        catch (Exception $e)
        {
            $log = Log::instance();
            $log->add(Log::ERROR, $e->getMessage());
        }
    }
}
