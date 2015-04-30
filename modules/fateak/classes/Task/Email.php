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
        'lang' => null,
        'refresh' => 'N',
    );

    protected function _execute(array $params)
    {
        try
        {
            if (! is_null($params['tpl']))
            {
                if (is_null($params['lang']))
                {
                    $task_config = Kohana::$config->load('task');
                    $params['lang'] = $task_config['default_lang'];
                }

                $parser = new FTparser(base64_decode($params['tpl']), 'email', base64_decode($params['lang']));
                
                if (! is_null($params['vars']))
                {
                    $vars = base64_decode($params['vars']);   
                    $vars = JSON::decode($vars);
                }
                else
                {
                    $vars = array();
                }

                $refresh = base64_decode($params['refresh']) == 'Y' ? true : false;

                $body = $parser->parse($vars, $refresh); 
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
