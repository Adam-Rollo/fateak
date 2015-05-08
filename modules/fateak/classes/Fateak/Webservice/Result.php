<?php
/**
 * Webservice result set class
 *
 * All webservice api should extend this class.
 *
 * @package    Basement\Webservice
 * @author     Rollo
 */
class Fateak_Webservice_Result
{
    const SUCCESS = 1;

    const FAILURE = 0;

    protected $result;

    protected $status;

    protected $message;

    protected $params;

    public function __construct($type, $value, $params = array())
    {
        if ($type) {

            $this->result = $value;
            $this->status = 'Y';
            $this->message = __('Request success.');

        } else {

            $this->result = array();
            $this->status = 'N';
            $this->message = __($value);

        }
            
        $this->params = $params;

    }

    public function to_array()
    {
        $result = $this->result;
        $result = $this->as_array($result);
        return array(
            'result' => $result,
            'status' => $this->status,
            'message' => $this->message,
            'params' => $this->params,
        );
    }

    private function as_array($result)
    {
       if (is_object($result) && method_exists($result, 'as_array')) {
            $result = $result->as_array(); 
            foreach ($result as $k => $item) {
                if (is_object($item)) {
                    $result[$k] = $this->as_array($item);
                }
            }
        } else if (is_object($result)) {
            throw new Gleez_Exception('Object can not be converted to array !');
        }

        return $result;
    }

    public function to_json()
    {
        $result = $this->to_array();
        return JSON::encode($result);
    }

    public function get_result($arr = FALSE)
    {
        if ($arr) {
            $rarr = $this->to_array();
            return $rarr['result'];
        } else {
            return $this->result;
        }
    }

    public function get_status()
    {
        return $this->status;
    }

    public function get_message()
    {
        return $this->message;
    }
}
