<?php
/**
 * Fateak process ajax
 * Ajax result in fateak always in this way:
 * {'success':'Y', 'message':'Sth must be wrong', 'total': 0, 'data':{...}, 'callback':'function(){...}', 'redirect':'foo/bar'}
 */
class Fateak_Fajax
{
    /**
     * @var sccucess or false
     */
    protected $success = true;

    /**
     * @var message of success or fail
     */
    protected $message = array();

    /**
     * @var data
     */
    protected $data = null;

    /**
     * @var callback function
     */
    protected $callback = null;

    /**
     * @var redirect url
     */
    protected $redirect = null;

    /**
     * @var total records number of data
     */
    protected $total = 0;

    /**
     * Fajax construction
     */
    public function __construct() {}

    /**
     * build ajax result
     */
    public function build_result()
    {
        $result = array();       
        $result['success'] = $this->success ? 'Y' : 'N';
        $result['message'] = $this->message;
        $result['data'] = $this->data;
        $result['callback'] = $this->callback;
        $result['redirect'] = $this->redirect;
        $result['total'] = $this->total;

        return JSON::encode($result);
    }

    /**
     * success of false ?
     */
    public function success($result = true)
    {
        $this->success = $result;

        return $this->success;
    }

    /**
     * set messages
     */
    public function message($message)
    {
        $this->message[] = $message;

        return $this->message;
    }

    /**
     * process data
     */
    public function data($data)
    {
        $this->data = $data;

        return $this->data;
    }

    /**
     * callback function
     */
    public function callback($callback)
    {
        $this->callback = $callback;

        return $this->callback;
    }

    /**
     * redirect url
     */
    public function redirect($url)
    {
        $this->redirect = $url;

        return $this->redirect;
    }

    /**
     * total number
     */
    public function total($number)
    {
        $this->total = $number;

        return $this->total;
    }

}
