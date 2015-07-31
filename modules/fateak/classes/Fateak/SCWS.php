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
                if (strstr($dict, APPPATH))
                {
                    $so->add_dict($dict);
                }
                else
                {
                    $dict_path = APPPATH . 'dicts' . DS . $dict . DS . 'dict.utf8.xdb';
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

    public static function split_more($words)
    {
        $keywords = explode(' ', $words);

        $so = self::instance(array('material'));

        $words_result = array();

        foreach ($keywords as $keyword)
        {
            preg_match_all('/[0-9a-zA-Z]+/', $keyword, $match);

            $so->send_text($keyword);

            $words = $so->get_tops(5);

            if (empty($words))
            {
                $words = $so->get_result();
            }

            $keyword = self::combine($words);
            $words_result[] = $keyword;

            if (! empty($match[0]))
            {
                $words_result[] = implode(' ', $match[0]);    
            }
        }

        return implode(' ', $words_result);
    }

    public static function compile($words)
    {
        $keywords = explode(' ', $words);

        $so = self::instance(array('material'));

        $words_result = array();

        foreach ($keywords as $keyword)
        {
            preg_match_all('/[0-9a-zA-Z]+/', $keyword, $match);

            $so->send_text($keyword);

            $words = $so->get_result();

            $keyword = self::combine($words);

            $words_result[] = $keyword;

            if (! empty($match[0]))
            {
                $words_result[] = implode(' ', $match[0]);    
            }
        }

        return implode(' ', $words_result);
    }

    /**
     * release handler
     */
    public static function release()
    {
        self::$scws->close();
    } 
}
