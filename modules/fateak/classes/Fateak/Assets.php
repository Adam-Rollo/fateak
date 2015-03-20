<?php
/**
 * PHP load and orgnize js and css.
 */
class Fateak_Assets
{
    /**
     * all css store in it.
     */
    protected static $_all_css = array();

    /**
     * js in head tag store in it.
     */
    protected static $_head_js = array();

    /**
     * js in body tag store in it.
     */
    protected static $_body_js = array();

    /**
     * get all css store in $_all_css.
     */
    public static function all_css()
    {
        uasort(self::$_all_css, array('self', 'sort_by_weight'));

        $css_html = "";

        foreach (self::$_all_css as $css) {
            $css_html .= PHP_EOL.HTML::style($css['url']);
        }

        return $css_html;
    }

    /**
     * get all head js store in $_head_js.
     */
    public static function head_js()
    {
        uasort(self::$_head_js, array('self', 'sort_by_weight'));

        $js_html = "";

        foreach (self::$_head_js as $js) {
            $js_html .= PHP_EOL.HTML::script($js['url']);
        }

        return $js_html;
    }

    /**
     * get all body javascript store in $_body_js.
     */
    public static function body_js()
    {
        uasort(self::$_body_js, array('self', 'sort_by_weight'));

        $js_html = "";

        foreach (self::$_body_js as $js) {
            $js_html .= PHP_EOL.HTML::script($js['url']);
        }

        return $js_html;
    }



    /**
     * set css in _all_css.
     */
    public static function add_css($name, $url, $weight = 0)
    {
        self::$_all_css[$name] = array('url' => $url, 'weight' => $weight);
    }

    /**
     * set js in _head_js.
     */
    public static function add_head_js($name, $url, $weight = 0)
    {
        self::$_head_js[$name] = array('url' => $url, 'weight' => $weight);
    }

    /**
     * set js in _body_js.
     */
    public static function add_body_js($name, $url, $weight = 0)
    {
        self::$_body_js[$name] = array('url' => $url, 'weight' => $weight);
    }

    /**
     * Custom sorting method for assets based on 'weight' key
     *
     * @param   array    $a
     * @param   array    $b
     * @return  integer  The sorted order for assests
     */
    protected static function sort_by_weight($a, $b)
    {
        $a_weight = (is_array($a) AND isset($a['weight'])) ? $a['weight'] : 0;
        $b_weight = (is_array($b) AND isset($b['weight'])) ? $b['weight'] : 0;

        if ($a_weight == $b_weight)
        {
            return 0;
        }

        return ($a_weight > $b_weight) ? +1 : -1;
    }

    /**
     * make url convert to real path
     * Fateak - Rollo
     */
    public static function url2path($url)
    {
        $relative_url = trim(str_replace("/assets/", "", $url), "/");

        $path_url = APPPATH . 'media' . DS . $relative_url;

        $path = str_replace("/", DS, $path_url);

        return $path;
    }

    /**
     * make real path convert to url
     * Fateak - Rollo
     */
    public static function path2url($path)
    {
        $assets_path = APPPATH . 'media';

        $relative_path = trim(str_replace($assets_path, "", $path), DS);
        $url = "/assets/" . str_replace(DS, "/", $relative_path);

        return $url;
    }
}

