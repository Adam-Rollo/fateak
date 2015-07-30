<?php
/**
 * Simple Chinese words segmentation
 * SCWS source: https://github.com/hightman/scws
 * SCWS author: HightMan
 *
 * Fateak Class
 */
class Fateak_SCWS
{

    protected static $scws = null;

    /**
     * Singleton
     */
    public static function instance($extra_dicts = array())
    {
        if (is_null(self::$scws))
        {
            $so = scws_new();
            $so->set_charset('utf8');

            $scws_path = MODPATH . 'fateak' . DS . 'data' . DS . 'scws' . DS;
            $so->set_dict($scws_path . 'dict.utf8.xdb');
            $so->set_rule($scws_path . 'rules.utf8.ini');

            foreach ($extra_dicts as $dict)
            {
                if (strstr($dict, MODPATH))
                {
                    $so->add_dict($dict);
                }
                else
                {
                    $dict_path = MODPATH . $dict . DS . 'data' . DS . 'scws' . DS . 'dict.utf8.xdb';
                    $so->add_dict($dict_path);
                }
            }

            register_shutdown_function(array('SCWS', 'release'));
 
            self::$scws = $so;
        }
            
        return self::$scws;
    }

    public static function combine($words)
    {
        $result = '';

        foreach ($words as $word)
        {
            $result .= $word['word'] . " ";
        }

        return trim($result);
    }

    /**
     * release handler
     */
    public static function release()
    {
        self::$scws->close();
    } 
}
