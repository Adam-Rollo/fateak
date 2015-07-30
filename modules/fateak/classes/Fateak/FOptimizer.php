<?php
/**
 * Optimizer - Fateak
 *
 * @uses Kohana_Profiler
 * @author Rollo
 */
class Fateak_FOptimizer 
{

    /**
     * Shutdown function
     */
    public static function save()
    {
        // init
        $redis = FRedis::instance();

        // 分部统计
        $details = array();
        $groups = Profiler::groups();

        if (! isset($groups['requests']))
        {
            return false;
        }

        $requests =  array_keys($groups['requests']);
        $uri = strtolower(str_replace('"', '', $requests[0]));
                

        if (strpos($uri, 'assets') === 0)
        {
            $random_number = mt_rand(0, 1000);

            if ($random_number > 10)
            {
                return false;
            }

            $uri = 'assets/all';
        }

        foreach ($groups as $group_name => $group)
        {
            $details[$group_name] = array();

            if ($group_name == 'requests')
            {
                $requests = array_keys($group);

                foreach ($group as $name => $tokens)
                {
                    $name = str_replace('"', '', $name);
                    $details[$group_name][$name] = Profiler::stats($tokens);
                    $details[$group_name][$name]['exe_times'] = count($tokens);
                }
                
            }
            else if (strpos($group_name, 'database') === 0)
            {
                $db_tokens = array();

                foreach ($group as $name => $tokens)
                {
                    $sql_type = substr($name, 0, 6);

                    if (strstr($name , 'WHERE'))
                    {

                        list($main, $condition) = explode('WHERE', $name);

                        $condition = preg_replace('/= ([\S]+)/', '= @', $condition);
                        $condition = preg_replace('/LIKE ([\S]+\%\')/', 'LIKE @', $condition);
                        $condition = preg_replace('/IN (\([\S ]+\))/', 'IN @', $condition);
                        $condition = preg_replace('/AGAINST (\([\S ]+\))/', 'AGAINST @', $condition);

                        switch ($sql_type)
                        {
                            case 'SELECT':
                                $condition = preg_replace('/OFFSET ([0-9]+)/', 'OFFSET @', $condition);
                                break;
                            case 'INSERT':
                                $main = preg_replace('/VALUES \([ \S]*\)/', 'VALUES @ ', $main);
                                break;
                            case 'UPDATE':
                                $main = preg_replace('/SET [ \S]*/', 'SET @ ', $main);
                                break;
                            case 'DELETE':
                                break;
                            default:
                                break;
                        }

                        $key = $main . "WHERE" . $condition;
                    }
                    else
                    {
                        switch ($sql_type)
                        {
                            case 'SELECT':
                                $name = preg_replace('/OFFSET ([0-9]+)/', 'OFFSET @', $name);
                                break;
                            case 'INSERT':
                                $name = preg_replace('/VALUES \([ \S]*\)/', 'VALUES @ ', $name);
                                break;
                            case 'UPDATE':
                                $name = preg_replace('/SET [ \S]*/', 'SET @ ', $name);
                                break;
                            case 'DELETE':
                                break;
                            default:
                                break;
                        }

                        $key = $name;
                    }


                    if (isset($db_tokens[$key]))
                    {
                        $db_tokens[$key] = array_merge($db_tokens[$key], $tokens);
                    }
                    else
                    {
                        $db_tokens[$key] = $tokens;
                    }

                }

                foreach ($db_tokens as $key => $tokens_array)
                {
                    $details[$group_name][$key] = Profiler::stats($tokens_array);
                    $details[$group_name][$key]['exe_times'] = count($tokens_array);
                }
  
            }
            else
            {
                foreach ($group as $name => $tokens)
                {
                    $details[$group_name][$name] = Profiler::stats($tokens);
                    $details[$group_name][$name]['exe_times'] = count($tokens);
                }
            }
        }

        $details_json = JSON::encode($details);

        $result = $redis->lua('record_profiler', array($uri, $details_json, time()), 1);

        $errors = $redis->getLastError();

        if ($errors)
        {
            Log::debug($errors);
        }
    }
}

