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
}
