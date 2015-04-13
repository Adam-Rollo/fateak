<?php

class Task_Email extends Minion_Task
{
    protected $_options = array(
        'email' => 'shirendeyaodian@aliyun.com',
        'toName' => 'Deer User',
        'title' => 'An Email from Fateak',
        'body' => '',
    );

    protected function _execute(array $params)
    {
        $email = Email::factory()
            ->subject(base64_decode($params['title']))
            ->to(base64_decode($params['email']))
            ->message(base64_decode($params['body']));

        $email->send();

    }
}
