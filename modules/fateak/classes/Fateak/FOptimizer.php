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
        $uri = "unknown";

        // 分部统计
        $details = array();
        $groups = Profiler::groups();
        foreach ($groups as $group_name => $group)
        {
            $details[$group_name] = array();

            if ($group_name == 'requests')
            {
                $requests = array_keys($group);
                $uri = str_replace('"', '', $requests[0]);

                foreach ($group as $name => $tokens)
                {
                    $name = str_replace('"', '', $name);
                    $details[$group_name][$name] = Profiler::stats($tokens);
                }
                
            }
            else if (strpos($group_name, 'database') === 0)
            {
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

                    $details[$group_name][$key] = Profiler::stats($tokens);
                }
  
            }
            else
            {
                foreach ($group as $name => $tokens)
                {
                    $details[$group_name][$name] = Profiler::stats($tokens);
                }
            }
        }

        Log::debug(Fsystem::dump_str($details));

        $details_json = JSON::encode($details);
            
        $result = $redis->lua('record_profiler', array($uri, $details_json), 1);

        Log::debug(Fsystem::dump_str($result));
        $result = $redis->getLastError();
        Log::debug(Fsystem::dump_str($result));
    }
}

