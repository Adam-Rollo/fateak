<?php

class Fateak_Fsystem
{
    /**
     * Fateak - uniqid
     */
    public static function uniqueID($user_id = null, $extra = null)
    {
        if (is_null($user_id))
        {
            $user_id = mt_rand(10,99) . 'u';
        }

        if (is_null($extra))
        {
            $prefix = $user_id;
        }
        else
        {
            $prefix = $user_id . $extra;
        }

        $id = uniqid($prefix . mt_rand(1000, 9999), true);

        return str_replace('.', '', $id);

    }

    /**
     * Fateak - unique number
     */
    public static function uniqueNumber($user_id = null)
    {
        if (is_null($user_id))
        {
            $user_id = mt_rand(10,99);
        }
        else
        {
            $user_id = $user_id % 100;
        }

        $timeID = (time() + microtime()) * 10000;

        $salt = mt_rand(100,999) * 100 + $user_id;

        return (string) $timeID . (string) $salt;

    }

    /**
     * Fateak - dump string
     */
   public static function dump_str($str)
   {
        ob_start();

        var_dump($str);

        return ob_get_clean();
   }

    /**
     * Make Inputs
     */
    public static function generate_inputs($arr)
    {
        $html = self::inner_inputs($arr, "");

        return $html;
    }

    protected static function inner_inputs($arr, $pre)
    {
        $html = "";

        foreach ($arr as $name => $value)
        {
            $name = ($pre == '') ? $name : $pre . '[' . $name . ']';

            if (is_array($value))
            {
                $html .= self::inner_inputs($value, $name);
            }
            else
            {
                $html .= "<input name='{$name}' value='{$value}' />";
            }
        }

        return $html;
    }

    public static function options_remove_tags($options)
    {
        $result = array();

        foreach ($options as $value => $name)
        {
            $result[$value] = preg_replace('/\<.*\>(.*)\<\/.*\>/', "$1", $name);
        }

        return $result;
    }

    public static function remove_puncts($text)
    {
        if ( trim($text) == '' )
        {
            return '';
        }

        // 英文标点
        $text=preg_replace("/[[:punct:]\s]/",' ',$text);

        // 中文标点
        $text=urlencode($text);
        $text=preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99|%EF%BD%9E|%EF%BC%8E|%EF%BC%88)+/",' ',$text);
        $text=urldecode($text);

        return trim($text);        
    }
}
