<?php
/**
 * PHP Template parser
 *
 * @author     Rollo
 * @copyright  2015 Fateak
 */
class Fateak_FTparser
{

    protected $variables = array();

    protected $string = null;

    protected $relative_path = null;

    public function __construct($str, $path = false, $lang = null)
    {
        if ($path)
        {
            $this->relative_path = $path . '/' . $str;

            if (is_null($lang))
            {
                $lang = I18n::$lang;
            }

            $str .= "_" . strtolower(substr($lang, 0, 2)); 

            if ($path === true)
            {
                $path = 'templates';
            }
            else
            {
                $path = trim(str_replace('/', DS, $path), DS);
                $path = 'templates' . DS . $path;
            }

            $template_path = Kohana::find_file($path, $str, 'html');

            $this->string = file_get_contents($template_path);
        }
        else
        {
            $this->string = $str;
        }
    }

    public function parse($external_variables = array(), $refresh_cache = false)
    {
        // Load cache
        $cache = Cache::instance('file');
        $cache_key = 'tpl::' . $this->relative_path;

	if (($str = $cache->get($cache_key)) && $refresh_cache === false)
        {
            return $str;
        }

        // parse HTML process ckeditor
        $str = $this->parse_html($this->string);

        // Import external variables
        $this->variables = $external_variables;

        // Extract variables
        preg_match_all("/\{\%v\%\s?(\w+) ?= ?([\s\S]+?)\s?(\[([^\s\"\'\^\(\)\\\\]+)\])?\s?\}/", $str, $matches);
        $variables_number = count($matches[0]);

        for ($i=0; $i<$variables_number; $i++) 
        {
            $variable_name = trim($matches[1][$i]);
            $matches_value = trim($matches[2][$i]);
            if (preg_match('/^\'([\s\S]*)\'$/', $matches_value, $string)) 
            {
                $this->variables[$variable_name] = $string[1];
            } 
            else 
            {
                $this->variables[$variable_name] = $this->call($matches_value, $matches[4][$i]);
            }
            $str = str_replace($matches[0][$i], "", $str);
        }

        // Process loops 
        $str = $this->parse_loop($str);

        // Process Condition
        $str = $this->parse_condition($str);

        // Process Assignment
        $str = $this->parse_assignment($str);

        // Set cache
        $cache->set($cache_key, $str);

        return trim($str);
    }

    private function parse_html($str)
    {
        $str = str_replace('&amp;', '&', $str);
        $str = str_replace('&lt;', '<', $str);
        $str = str_replace('&gt;', '>', $str);
        $str = str_replace('&quot;', '"', $str);
        $str = str_replace('&apos;', '\'', $str);
        $str = str_replace('&#39;', '\'', $str);
        return $str;
    }

    protected function call($function, $params)
    {
        if ($params == "") 
        {
            $args = array();
        } 
        else 
        {
            parse_str($params, $args);
        }

        foreach ($args as $k => $v) 
        {
            if (is_array($v)) 
            {
                foreach ($v as $vk =>$vv) 
                {
                    $this->str2var($args[$k], $vk, $vv);
                }
            } 
            else 
            {
                $this->str2var($args, $k, $v);
            }
        }

        reset($args);
        list ($api_class, $api_function) = explode('/', $function);
        $result = Webservice::execute($api_class, $api_function, $args); 
        
        if ($result->get_status() == 'N') 
        {
            return "PHPer Template Error: " . $result->get_message();
        } 
        else 
        {
            return $result->get_result(TRUE);
        }
    }

    protected function str2var(& $args, $k, $v)
    {
        if (is_string($v) && preg_match('/^#/', $v)) 
        {
            $assumed_variable = substr($v, 1);
            if (strstr($assumed_variable, '.')) 
            {
                $args[$k] = $this->variables;
                $assumed_variable_arr = explode('.', $assumed_variable);
                foreach ($assumed_variable_arr as $single_assumed_variable) 
                {
                    if (isset($args[$k][$single_assumed_variable])) 
                    {
                        $args[$k] = $args[$k][$single_assumed_variable];
                    } 
                    else 
                    {
                        break;
                    }
                }
            } 
            else 
            {
                if (isset($this->variables[$assumed_variable])) 
                {
                    $args[$k] = $this->variables[$assumed_variable];
                }
            }           
        } 

    }

    protected function parse_loop($str, $lvl = 1)
    {
        extract($this->variables);
        $result = $str;
        $deep = ($lvl == 1) ? '' : $lvl;
        $pattern = "/\{\%l".$deep."\%([\s\S]+?) as ([\s\S]+?)\}([\s\S]+?)\{l".$deep."\%\}/";
        preg_match_all($pattern, $str, $matches);
        $loops_number = count($matches[0]);
        if ($loops_number > 0) 
        {
            $lvl++;
            for ($i=0; $i<$loops_number; $i++) 
            {
                list($sets, $kn) = $this->variable_process($matches[1][$i]);
                $loop_exist_flag = TRUE;
                if (!empty($kn)) 
                {
                    $data = $$sets;
                    foreach ($kn as $k) 
                    {
                        if (isset($data[$k])) 
                        {
                            $data = $data[$k];
                        } 
                        else 
                        {
                            $loop_exist_flag = FALSE;
                            break;
                        }
                    }
                }
                else 
                {
                    $data = $$sets;
                }
                $value = trim($matches[2][$i]);
                $html = "";
                if ($loop_exist_flag) 
                {
                    if (is_array($data)) 
                    {
                        foreach ($data as $key => $v) 
                        {
                            $tmp_html = str_replace('^', "^".$key, $matches[3][$i]);
                            $html .= preg_replace('/([\W])'.$value.'([\W])/', '$1'.$matches[1][$i].".".$key.'$2', $tmp_html);
                        }
                        $html = $this->parse_loop($html, $lvl);
                    } 
                    else 
                    {
                        $html = "";
                    }
                }
                $result = str_replace($matches[0][$i], $html, $result);
            }
        }
        
        return $result;
    }

    protected function parse_condition($str, $lvl = 1)
    {
        extract($this->variables);
        $result = $str;
        $deep = ($lvl == 1) ? '' : $lvl;
        $pattern = "/\{\%if".$deep."\%([\s\S]+?) ([\<\>\=@]+) ?(\S+?)?\}([\s\S]+?)(\{\%else\%\}([\s\S]+?))?\{\%endif".$deep."\%\}/";
        preg_match_all($pattern, $str, $matches);
        $loops_number = count($matches[0]);
        $lvl ++;
        for ($i=0; $i<$loops_number; $i++) 
        {
            if ($postion = strpos($matches[4][$i], '{%elseif%')) 
            {
                $if_content = substr($matches[4][$i], 0, $postion);
            } 
            else 
            {
                $if_content = $matches[4][$i];
            }

            $queues = array($this->condition_push(
                $matches[1][$i], $matches[2][$i], $matches[3][$i], $if_content       
            ));
            $content = $matches[4][$i] . '{%else';
            preg_match_all("/if\%([\s\S]+?)([\<\>\=@]+)([\S ]+?)?\}([\s\S]+?)\{\%else/", $content, $eis);
            for ($j=0; $j < count($eis[0]); $j++) 
            {
                array_push($queues, $this->condition_push(
                    $eis[1][$j], $eis[2][$j], $eis[3][$j], $eis[4][$j]             
                ));
            }

            $html = $this->condition_queues($queues, trim($matches[6][$i]));
                
            $html = $this->parse_condition($html, $lvl);
            $result = str_replace($matches[0][$i], $html, $result);

        }
        return $result;
    }


    protected function parse_assignment($str)
    {
        extract($this->variables);
        $result = $str;
        preg_match_all('/\{\%e\%([\s\S]+?)\}/', $str, $matches);
        foreach ($matches[1] as $k => $var) 
        {
            $var = trim($var);
            $real_value = $this->ex_assign($var);
            $result = str_replace($matches[0][$k], __($real_value), $result);
        }
        return $result;
    }

    private function condition_queues($queues, $default = "")
    {
        foreach ($queues as $condition) 
        {
            if ($this->check($condition['subject'], $condition['object'], $condition['operator'])) 
            {
                return $condition['content'];
            }
        }
        return $default;
    }

    private function condition_push($subject, $operator, $object, $content)
    {
        $subject = trim($subject);
        $object = trim($object);
        $object = $this->str_judge($object) ? $this->str_filter($object) : $this->ex_assign($object);
        return array(
            'subject' => $this->ex_assign($subject),
            'operator' => trim($operator),                       
            'object' => $object,
            'content' => trim($content),
        );
    }

    private function variable_process($v)
    {
        $v = trim($v);
        $k = array();
        if (strstr($v, '.')) 
        {
            $varr = explode('.', $v);
            $vname = array_shift($varr);
            foreach ($varr as $key) 
            {
                $k[] = $key;
            }
            $v = $vname;
        }

        return array($v, $k);
    }

    private function ex_assign($v)
    {
        if (strstr($v, '.')) 
        {
            $varr = explode('.', trim($v));
            $data = array_shift($varr);
            $data = $this->variables[$data];
            foreach ($varr as $k) 
            {
                if (strstr($k, '^')) 
                {
                    $data = substr($k, 1);
                    break;
                } 
                else 
                {
                    if (isset($data[$k])) 
                    {
                        $data = $data[$k];
                    } 
                    else 
                    {
                        return null;
                    }
                }
            }
        } 
        else 
        {
            if (isset($this->variables[$v])) 
            {
                $data = $this->variables[$v];
            } 
            else 
            {
                return null;
            }
        }

        return $data;
    }

    private function str_judge($v)
    {
        return strstr($v, '\'') || is_numeric($v);
    }

    private function str_filter($v)
    {
        return str_replace('\'', '', $v);
    }

    private function check($subject, $object, $operator)
    {
        if (is_null($subject)) 
        {
            return $subject;
        }

        switch ($operator) 
        {
            case ">":
                return $subject > $object;
                break;
            case "<":
                return $subject < $object;
                break;
            case ">=":
                return $subject >= $object;
                break;
            case "<=":
                return $subject <= $object;
                break;
            case "==":
                return $subject == $object;
                break;
            case "<>":
                return $subject != $object;
                break;
            case "@":
                if (empty($subject) || $subject == '') {
                    return FALSE;
                } else {
                    return TRUE;
                }
                break;
        }
        return FALSE;
    }

}
