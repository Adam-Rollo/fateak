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

            // Process Language
            if (isset($params['lang']))
            {
                I18n::lang($params['lang']);
                unset($params['lang']);
            }
            else
            {
                $webservice_config = Kohana::$config->load('webservice');
                I18n::lang($webservice_config['default_lang']);
            }

            $this->decode_params($params);

            if (strstr($api_class, '_'))
            {
                $api_class_name_array = explode('_', $api_class);
                $api_class_converted_array = array();
                foreach ($api_class_name_array as $api_class_name_part)
                {
                    $api_class_converted_array[] = ucfirst($api_class_name_part);
                }

                $api_class = implode('_', $api_class_converted_array);
            }
            else
            {
                $api_class = ucfirst($api_class);
            }

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
