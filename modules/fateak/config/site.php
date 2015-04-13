<?php defined('SYSPATH') OR die('No direct script access.');

return array
(
    /**
     * Site name
     */
    'site_name' => 'Tseaps - Fateak',

    /**
     * CSRF Key
     */
    'csrf_key' => 'norseforce212',

    /**
     * List of all supported languages. Array keys match language segment from the URI.
     * A default fallback language can be set by I18n::$default.
     *
     * Options for each language:
     *  i18n_code - The target language for the I18n class
     *  locale    - Locale name(s) for setting all locale information (http://php.net/setlocale)
     */
    'installed_locales' => array(
    	'en' => array(
    	    'name'      => 'English',
    	    'i18n_code' => 'en',
            'locale'    => array('en_US.utf-8'),
	),
	'zh' => array(
	    'name'      => 'Chinese (Simplified)',
	    'i18n_code' => 'zh',
	    'locale'    => array('zh_CN.utf-8'),
	),
    ),

    /**
     * server charset for development
     */
    'server_iconv' => 'UTF-8',

    /**
     * Site EMail address and other configurations
     */
    'email_host' => 'smtp.163.com',
    'site_email' => 'hejiao_xtu@163.com',
    'email_pass' => '517611141hejiao',
);
