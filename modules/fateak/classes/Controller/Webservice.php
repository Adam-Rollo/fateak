<?php
/**
 * Process all the webservice request.
 * @author Fateak - Rollo
 */
class Controller_Webservice extends Controller
{
    public function action_index()
    {
        $api_class = $this->request->param('class');
        $api_function = $this->request->param('function');
        $method = $this->request->method();

        if ($method == 'POST') 
        {
            $params = $this->request->post();
            $this->decode_params($params);
            $result = Webservice::execute($api_class, $api_function, $params);
        } 
        else 
        {
            $result = new Webservice_Result(Webservice_Result::FAILURE, _('Please use POST method access API.'));
        }
            
        $this->response->body($result->to_json());

    }

    private function decode_params(& $params)
    {
        foreach ($params as $key => $param) 
        {
            if (strstr($param, '[') || strstr($param, '{')) 
            {
                try 
                {
                    $param = JSON::decode($param);
                    $params[$key] = $param;
                } 
                catch (Exception $e) 
                {
                    continue;
                }
            }
        }
    }
}
