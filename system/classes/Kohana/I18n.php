<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Internationalization (i18n) class. Provides language loading and translation
 * methods without dependencies on [gettext](http://php.net/gettext).
 *
 * Typically this class would never be used directly, but used via the __()
 * function, which loads the message and replaces parameters:
 *
 *     // Display a translated message
 *     echo __('Hello, world');
 *
 *     // With parameter replacement
 *     echo __('Hello, :user', array(':user' => $username));
 *
 * @package    Kohana
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_I18n {

    /**
     * @var  string   target language: en-us, es-es, zh-cn, etc
     */
    public static $lang = 'en-us';

    /**
     * @var  string  source language: en-us, es-es, zh-cn, etc
     */
    public static $source = 'en-us';

    /**
     * @var  string   target language: en, es, zh, etc
     */
    public static $default = 'en';

    /**
     * @var  string   active language: en, es, zh, etc
     */
    public static $active = 'en';

    /**
     * @var  array  cache of loaded languages
     */
    protected static $_cache = array();

    /**
     * @var  array  array of available languages
     */
    protected static $_languages = array();

    /**
     * @var  string  source language: en-us, es-es, zh-cn, etc
     */
    public static $_cookie = 'lang';

    /**
     * Initialize I18n to decide language.
     * Fateak - Rollo
     * Language detect order:
     * 1) Cookie
     * 2) User's configuration
     * 3) Browser
     */
    public static function initialize()
    {
        self::$_languages = Kohana::$config->load('site')->get('installed_locales');

        // Cookie detect
        $locale = self::cookieLocale();

        // User's configuration
        if (! $locale)
        {
            $locale = self::userLocale();
        }

        // Detect Browser
        if (! $locale)
        {
            $locale = self::browserLocale();
        }

        if(! $locale)
        {
            $locale = I18n::$default;
        }

        self::lang($locale);

        return I18n::$lang;
    }

    /**
     * Test if $lang exists in the list of available langs in config
     *
     * @param type  string $lang
     * @return bool returns TRUE if $lang is available, otherwise FALSE
     */
    public static function isAvailable($lang)
    {
        return (bool) array_key_exists($lang, self::$_languages);
    }

    /**
     * Detect language based on the request cookie.
     *
     *     // Get the language
     *     $lang = I18n::cookieLocale();
     *
     * @return  string
     */
    public static function cookieLocale()
    {
        $cookie_data = strtolower(Cookie::get(self::$_cookie));

        //double check cookie data
        if ($cookie_data AND preg_match("/^([a-z]{2,3}(?:_[A-Z]{2})?)$/", trim($cookie_data), $matches))
        {
            $locale = $matches[1];

            if( self::isAvailable($locale) )
            {
                return $locale;
            }
        }

        return FALSE;
    }

    /**
     * Detect language based on the User data.
     *
     *     // Get the language
     *     $lang = I18n::userLocale();
     *
     * @return  string
     */
    public static function userLocale()
    {
        $user = User::active_user();
        
        if (is_null($user))
        {
            return FALSE;
        }

        $language = $user->language;

        //double check cookie data
        if ($language AND preg_match("/^([a-z]{2,3}(?:_[A-Z]{2})?)$/", trim($language), $matches))
        {
            $locale = $matches[1];

            if( self::isAvailable($locale) )
            {
                return $locale;
            }
        }

        return FALSE;
    }

    /**
     * Detect language based on the Web Browser.
     *
     *     // Get the language
     *     $lang = I18n::browserLocale();
     *
     * @return  string
     */
    public static function browserLocale()
    {
                $browser_langs = Request::accept_lang();
                if (!is_array($browser_langs))
                {
                        $browser_langs = array($browser_langs);
                }

                foreach ($browser_langs as $language => $weight)
                {
                //double check cookie data
                if ($language AND preg_match("/^([a-z]{2,3}(?:_[A-Z]{2})?)$/", trim($language), $matches))
                {
                    $locale = $matches[1];

                    if( self::isAvailable($locale) )
                    {
                        return $locale;
                    }
                }
        
                }
                
                return false;
        }



    /**
     * Get and set the target language.
     *
     *     // Get the current language
     *     $lang = I18n::lang();
     *
     *     // Change the current language to Spanish
     *     I18n::lang('es-es');
     *
     * @param   string  $lang   new language setting
     * @return  string
     * @since   3.0.2
     */
    public static function lang($lang = NULL)
    {

        if ($lang && self::isAvailable($lang))
        {
            // Normalize the language
            I18n::$lang = strtolower(str_replace(array(' ', '_'), '-', self::$_languages[$lang]['i18n_code']));

            // Store the identified lang as active  
                        I18n::$active = $lang;

            // Set locale
            setlocale(LC_ALL, self::$_languages[$lang]['locale']);
        
            // Update language in cookie
            if (strtolower(Cookie::get(self::$_cookie)) !== $lang) 
            {
                // Trying to set language to cookies
                Cookie::set(self::$_cookie, $lang, Date::YEAR);
            }
        }

        return I18n::$lang;
    }

    /**
     * Returns translation of a string. If no translation exists, the original
     * string will be returned. No parameters are replaced.
     *
     *     $hello = I18n::get('Hello friends, my name is :name');
     *
     * @param   string  $string text to translate
     * @param   string  $lang   target language
     * @return  string
     */
    public static function get($string, $lang = NULL)
    {
        if ( ! $lang)
        {
            // Use the global target language
            $lang = I18n::$lang;
        }

        // Load the translation table for this language
        $table = I18n::load($lang);

        // Return the translated string if it exists
        return isset($table[$string]) ? $table[$string] : $string;
    }

    /**
     * Returns the translation table for a given language.
     *
     *     // Get all defined Spanish messages
     *     $messages = I18n::load('es-es');
     *
     * @param   string  $lang   language to load
     * @return  array
     */
    public static function load($lang)
    {
        if (isset(I18n::$_cache[$lang]))
        {
            return I18n::$_cache[$lang];
        }

        // New translation table
        $table = array();

        // Split the language: language, region, locale, etc
        $parts = explode('-', $lang);

        do
        {
            // Create a path for this set of parts
            $path = implode(DIRECTORY_SEPARATOR, $parts);

            if ($files = Kohana::find_file('i18n', $path, NULL, TRUE))
            {
                $t = array();
                foreach ($files as $file)
                {
                    // Merge the language strings into the sub table
                    $t = array_merge($t, Kohana::load($file));
                }

                // Append the sub table, preventing less specific language
                // files from overloading more specific files
                $table += $t;
            }

            // Remove the last part
            array_pop($parts);
        }
        while ($parts);

        // Cache the translation table locally
        return I18n::$_cache[$lang] = $table;
    }

        /**
         * Base on bootstrap and fateak.js
         */
        public static function language_selector($div_class = array(), $button_class = array())
        {
                $div_class = implode(" ", array_merge($div_class, array('btn-group')));
                $output = "<div class='{$div_class}'>";

                $button_class = implode(" ", array_merge($button_class, array('btn', 'dropdown-toggle')));
                $active_language = __(self::$_languages[I18n::$active]['name']);
                $button = "<button type='button' class='{$button_class}' data-toggle='dropdown' aria-expanded='false'>{$active_language} <span class='caret'></span></button>";

                $languages_html = '<ul class="dropdown-menu" role="menu">';
                foreach (self::$_languages as $k => $v)
                {
                        $languages_html .= "<li><a href='" . URL::base() . "i18n?lang=" . $k . "&url=" . urlencode(URL::current()) . "' >" . __($v['name']) . "</a></li>";
                }
                $languages_html .= "</ul>";

                $output .= $button . $languages_html . "</div>";

                return $output;
        }

}

if ( ! function_exists('__'))
{
    /**
     * Kohana translation/internationalization function. The PHP function
     * [strtr](http://php.net/strtr) is used for replacing parameters.
     *
     *    __('Welcome back, :user', array(':user' => $username));
     *
     * [!!] The target language is defined by [I18n::$lang].
     *
     * @uses    I18n::get
     * @param   string  $string text to translate
     * @param   array   $values values to replace in the translated text
     * @param   string  $lang   source language
     * @return  string
     */
    function __($string, array $values = NULL, $lang = 'en-us')
    {
        if ($lang !== I18n::$lang)
        {
            // The message and target languages are different
            // Get the translation for this message
            $string = I18n::get($string);
        }

        return empty($values) ? $string : strtr($string, $values);
    }
}
