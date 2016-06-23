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

        if (! is_array($img_arr))
        {
            return "";
        }

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
        if($src)
            return date($format, $src);
        else
            return '';
    }

    public static function exsit($src)
    {
        return ($src=='' || $src=='[]') ? '' : '有';
    }

    public static function famount($val, $type)
    {
        if($type){
            $val = -$val;
        }
        $val = $val/100;
        return '<font class="text-'.($type?'success':'danger').'">'.$val.'</font>';
    }
}
