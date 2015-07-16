<?php
/**
 * Webservice 文档
 */
class Controller_Webservice_Doc extends Controller_Fate
{
    public function action_index()
    {
        $modules = Module::modules();
        unset($modules['auth']);unset($modules['cache']);unset($modules['database']);unset($modules['image']);unset($modules['minion']);unset($modules['unittest']);unset($modules['captcha']);
        $api_modules = array('0' => __('Please select a module'));
        $current_module = $this->request->query('module');
        $current_module = $current_module ? $current_module : '0';
        foreach ($modules as $module_name => $module_path) {
            $webservice_file_dir = $module_path . 'classes' . DS . 'Webservice';
            $this->find_api($webservice_file_dir, $api_modules);
        }

        if ($current_module) {
            // parent class
            $parent_webservice = new ReflectionClass('Webservice');
            $parent_methods = $parent_webservice->getMethods(ReflectionMethod::IS_PUBLIC);
            $parent_functions = array();
            foreach ($parent_methods as $parent_method) {
                $parent_functions[] = $parent_method->getName();
            }


            $webservice_functions = array();
            $webservice_class = new ReflectionClass($current_module);

            $webservice_class_comment = $webservice_class->getDocComment();
            $webservice_class_comment_array = explode("\n", str_replace("\r\n", "\n", $webservice_class_comment));
            array_walk($webservice_class_comment_array, function(& $value){
                $value = trim($value, ' */');        
            });
            $webservice_class_comment = "";
            foreach ($webservice_class_comment_array as $comment_line) {
                if ($comment_line != "") {
                    // $comment_line = preg_replace('/(@[\w]+)/', '<a href="#">$1</a>', $comment_line);
                    $webservice_class_comment .= $comment_line . PHP_EOL;
                }
            }


            $methods = $webservice_class->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $key => $method) {
                $simple_function = array();

                if (in_array($method->getName(), $parent_functions)) {
                    continue;
                } else {
                    $simple_function['name'] = $method->getName();
                    $simple_function['classfunction'] = $current_module . '/' . $method->getName();
                    $simple_function['class'] = $current_module;
                    $simple_function['function'] = $method->getName();
                }

                $comment = $method->getDocComment ();
                $comment_array = explode("\n", str_replace("\r\n", "\n", $comment));
                array_walk($comment_array, function(& $value){
                    $value = trim($value, ' */');        
                });
                $comment_array = array_filter($comment_array, function($value){
                    if ($value == "") {
                        return FALSE;
                    } else {
                        return TRUE;
                    }     
                });
                $comment_result = array('params' => array(), 'return' => array('type' => '', 'info' => ''), 'comment' => '');
                foreach ($comment_array as $comment_line) {
                    preg_match_all('/@param ([\w]+):? ([\S ]+)/', $comment_line, $matches);
                    if (! empty($matches[0])) {
                        $comment_result['params'][$matches[1][0]] = $matches[2][0];
                        continue;
                    }
                    preg_match_all('/@return ([\w]+):? ([\S]+)/', $comment_line, $matches);
                    if (! empty($matches[0])) {
                        $comment_result['return'] = array('type' => $matches[1][0], 'info' => $matches[2][0]);
                        continue;
                    }
                    $comment_result['comment'] .= $comment_line . PHP_EOL;
                }
                $comment_result['comment'] = trim($comment_result['comment']);

                $simple_function['comment'] = $comment_result;

                $webservice_functions[] = $simple_function;
            }

        } else {
            $webservice_functions = array();
            $webservice_class_comment = "";
        }

        $this->title = __("Webservice Documents");
        $view = View::factory('webservice/index')
            ->set('action', URL::base() . 'webservice/doc/index')
            ->set('current', $current_module)
            ->set('webservice_class_comment', $webservice_class_comment)
            ->set('webservice_functions', $webservice_functions)
            ->set('modules', $api_modules);
        $this->response->body($view);
    }

    protected function find_api($path, & $result)
    {

        if (is_dir($path))
        {
            $dir = opendir($path);
        }
        else
        {
            return null;
        }

        while (($cp = readdir($dir)) !== false)
        {
            if ($cp[0] == '.')
            {
                continue; 
            }

            $full_path = $path . DS . $cp;

            if (is_dir($full_path))
            {
                $this->find_api($full_path, $result); 
            }
            else
            {
                $relative_path = str_replace(DS, '_', substr($path, strpos($path, 'Webservice')));
                $class_name = $relative_path . "_" . substr($cp, 0, strpos($cp, '.'));
                $result[$class_name] = $class_name;
            }

            // Release memory in loop
            unset($cp);
        }

        return $result;
    }
}
