<?php

class Fateak_FTable_Render
{
    public static function img($src, $attr)
    {
        return "<img src='{$src}' {$attr} />";
    }

    public static function imgs($src, $attr)
    {
        $img_arr = JSON::decode($src);

        $result = "<ul>";
        foreach($img_arr as $img)
        {
            $result .= "<li style='float:left;list-style:none;margin-right:10px'><img src='{$img}' {$attr} /></li>";
        }
        $result .= "<li style='clear:both;list-style:none'></li></ul>";

        return $result;
    }

    public static function status($src, $dics, $attr = null)
    {
        if (isset($dics[$src]))
        {
            return $dics[$src];
        }
        else
        {
            return "未知状态";
        }
    }

    public static function ftime($src, $format = 'Y-m-d H:i:s')
    {
        return date($format, $src);
    }
}
