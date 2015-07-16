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
                    if (strstr($name , 'WHERE'))
                    {
                        list($select, $condition) = explode('WHERE', $name);

                        $key = preg_replace('/= ([\S]+)/', '= @', $condition);
                        $key = preg_replace('/OFFSET ([0-9]+)/', 'OFFSET @', $key);

                        $key = $select . "WHERE" . $key;
                    }
                    else
                    {
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

