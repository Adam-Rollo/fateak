<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Form helper class. Unless otherwise noted, all generated HTML will be made
 * safe using the [HTML::chars] method. This prevents against simple XSS
 * attacks that could otherwise be triggered by inserting HTML characters into
 * form fields.
 *
 * @package    Kohana
 * @category   Helpers
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Form {

	/**
	 * Generates an opening HTML form tag.
	 *
	 *     // Form will submit back to the current page using POST
	 *     echo Form::open();
	 *
	 *     // Form will submit to 'search' using GET
	 *     echo Form::open('search', array('method' => 'get'));
	 *
	 *     // When "file" inputs are present, you must include the "enctype"
	 *     echo Form::open(NULL, array('enctype' => 'multipart/form-data'));
	 *
	 * @param   mixed   $action     form action, defaults to the current request URI, or [Request] class to use
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    Request
	 * @uses    URL::site
	 * @uses    HTML::attributes
	 */
	public static function open($action = NULL, array $attributes = NULL)
	{
		if ($action instanceof Request)
		{
			// Use the current URI
			$action = $action->uri();
		}

		if ( ! $action)
		{
			// Allow empty form actions (submits back to the current url).
			$action = '';
		}
		elseif (strpos($action, '://') === FALSE)
		{
			// Make the URI absolute
			$action = URL::site($action);
		}

		// Add the form action to the attributes
		$attributes['action'] = $action;

		// Only accept the default character set
		$attributes['accept-charset'] = Kohana::$charset;

		if ( ! isset($attributes['method']))
		{
			// Use POST method
			$attributes['method'] = 'post';
		}

		$out =  '<form'.HTML::attributes($attributes).'>' . PHP_EOL;

		if (class_exists('CSRF'))
		{
			$action  = md5($action . CSRF::key());
			$out 	.= self::hidden('_token', CSRF::token(FALSE, $action)).PHP_EOL;
			$out 	.= self::hidden('_action', $action).PHP_EOL;
		}

                return $out;
	}

	/**
	 * Creates the closing form tag.
	 *
	 *     echo Form::close();
	 *
	 * @return  string
	 */
	public static function close()
	{
		return '</form>';
	}

	/**
	 * Creates a form input. If no type is specified, a "text" type input will
	 * be returned.
         * Fateak - Rollo
	 *
	 *     echo Form::input('username', $username);
	 *
	 * @param   string  $name       input name
	 * @param   string  $value      input value
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    HTML::attributes
	 */
	public static function input($name, $value = NULL, array $attributes = NULL)
	{
		// Set the input name
                if (! is_null($name))
                {
		        $attributes['name'] = $name;
                }

		// Set the input value
		$attributes['value'] = $value;

		if ( ! isset($attributes['type']))
		{
			// Default type is text
			$attributes['type'] = 'text';
		}

		return '<input'.HTML::attributes($attributes).' />';
	}

	/**
	 * Creates a hidden form input.
	 *
	 *     echo Form::hidden('csrf', $token);
	 *
	 * @param   string  $name       input name
	 * @param   string  $value      input value
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    Form::input
	 */
	public static function hidden($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['type'] = 'hidden';

		return Form::input($name, $value, $attributes);
	}

	/**
	 * Creates a password form input.
	 *
	 *     echo Form::password('password');
	 *
	 * @param   string  $name       input name
	 * @param   string  $value      input value
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    Form::input
	 */
	public static function password($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['type'] = 'password';

		return Form::input($name, $value, $attributes);
	}

	/**
	 * Creates a file upload form input. No input value can be specified.
         * Fateak - Rollo
         * Vendor: JCrop
         * You must load JCrop script and stylesheet in controller :
         * Assets::add_body_js('jcrop', 'assets/vendor/jcrop/js/jquery.Jcrop.min.js', -2);
         * Assets::add_body_js('fimage', 'assets/js/jquery.fateak.image.js', 2);
         * Assets::add_css('jcrop', 'assets/vendor/jcrop/css/jquery.Jcrop.min.css', -2);
	 *
	 *     echo Form::file('image');
	 *
	 * @param   string  $name       input name
	 * @param   array   $attributes html attributes
         * @param   array   key: type(file,image)/crop(true,false)/width/height
	 * @return  string
	 * @uses    Form::input
	 */
	public static function file($name, array $attributes = NULL, $options = array() )
	{
		$attributes['type'] = 'file';

                $output = Form::input($name, NULL, $attributes);

                if (! isset($options['type']))
                {
                        $options['type'] = 'file';
                }

                if ( $options['type'] == 'image' )
                {
                        $options['crop'] = isset($options['crop']) ? $options['crop'] : FALSE;

                        $default_value = isset($attributes['value']) ? $attributes['value'] : NULL;

                        if (! isset($attributes['id']))
                        {
                            $attributes['id'] = $name;
                        }
                        
                        // New widgets
                        $upload_button = "<input class='fupload-image' upb='{$name}' type='button' value='" . __('Upload') . "' data-toggle='modal' data-target='.image-modal' />"; 
                        $image_area = "<div id='preupimage-{$name}' class='preupimage' imgarea='{$name}'></div>";
                        $hidden_input = Form::hidden($name, $default_value, array('upi' => $name));

                        // Upload URL
                        $upload_url_query = isset($options['upload_query']) ? ( "?" . $options['upload_query'] ) : ""; 
                        $upload_url = URL::base() . "upload" . $upload_url_query;

                        // Max number of images
                        $max_images = isset($options['max_images']) ? $options['max_images'] : 1;

                        $extra_params = array(
                                "'fid':'{$name}'", 
                                "'uploadURL':'{$upload_url}'",       
                                "'maxNum':'{$max_images}'",
                        );

                        if ($options['crop'])
                        {
                                $options['width'] = isset($options['width']) ? $options['width'] : NULL;
                                $options['height'] = isset($options['height']) ? $options['height'] : NULL;

                                $crop_url_query = isset($options['crop_query']) ? ( "?" . $options['crop_query'] ) : ""; 
                                $crop_url = URL::base() . "upload/crop" . $crop_url_query;

                                $extra_params[] = "'cropURL':'{$crop_url}'";
                                $extra_params[] = "'crop':true";

                                if (isset($options['aspect_ratio']))
                                {
                                    $extra_params[] = "'aspectRatio':'" . $options['aspect_ratio'] . "'";
                                }
                        }

                        $extra_params = implode(',', $extra_params);

                        $script = "<script>"
                                . "(function($){"
                                . "$(\"input[upb='{$name}']\").FImage({" . $extra_params . "});"
                                . "})(jQuery);"
                                . "</script>";

                        $output = $upload_button . $image_area . $hidden_input . $script;


                }



		return $output;
	}

	/**
	 * Creates a checkbox form input.
	 *
	 *     echo Form::checkbox('remember_me', 1, (bool) $remember);
	 *
	 * @param   string  $name       input name
	 * @param   string  $value      input value
	 * @param   boolean $checked    checked status
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    Form::input
	 */
	public static function checkbox($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$attributes['type'] = 'checkbox';

		if ($checked === TRUE)
		{
			// Make the checkbox active
			$attributes[] = 'checked';
		}

		return Form::input($name, $value, $attributes);
	}

	/**
	 * Creates a radio form input.
	 *
	 *     echo Form::radio('like_cats', 1, $cats);
	 *     echo Form::radio('like_cats', 0, ! $cats);
	 *
	 * @param   string  $name       input name
	 * @param   string  $value      input value
	 * @param   boolean $checked    checked status
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    Form::input
	 */
	public static function radio($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$attributes['type'] = 'radio';

		if ($checked === TRUE)
		{
			// Make the radio active
			$attributes[] = 'checked';
		}

		return Form::input($name, $value, $attributes);
	}

	/**
	 * Creates a textarea form input.
         * Rollo - Fateak
         * You must load these js:
         *   Assets::add_body_js('ckeditor', 'media/vendor/ckeditor/ckeditor.js', -4);
         *   Assets::add_body_js('ckeditor-jq', 'media/vendor/ckeditor/adapters/jquery.js', -3);
	 *
	 *     echo Form::textarea('about', $about);
         *
         * If you use CKEditor width fateak-modal, you need declare this function in your modal initialize.
         *      var ps = function(){
         *           for(var instance in CKEDITOR.instances ) { 
         *               CKEDITOR.instances[instance].updateElement();
         *           }
         *       };
	 *
	 * @param   string  $name           textarea name
	 * @param   string  $body           textarea body
	 * @param   array   $attributes     html attributes
	 * @param   boolean $double_encode  encode existing HTML characters
	 * @return  string
	 * @uses    HTML::attributes
	 * @uses    HTML::chars
	 */
	public static function textarea($name, $body = '', array $attributes = NULL, $double_encode = TRUE)
	{
		// Set the input name
		$attributes['name'] = $name;

                // If it's a CKEditor
                if (strstr($attributes['class'], 'ckeditor')) {
                        // CKE need more rows
		        $attributes += array('rows' => 60, 'cols' => 50);
                    
                        $output = '<textarea'.HTML::attributes($attributes).'>'.HTML::chars($body, $double_encode).'</textarea>';
                    
                        $output .= '<script>(function($){$( document ).ready( function() {'
                            . '$( "textarea.ckeditor" ).ckeditor({'
                                . '"language":"' . I18n::lang() . '", '
                                . '"allowedContent": true,'
                                . '"filebrowserImageUploadUrl":"/upload?path=cke"'
                            . '});});})(jQuery);'
                            . 'if((typeof(ckFillImage)) != "function"){'
                                . 'function ckFillImage(img){'
                                    . 'jQuery(".cke_dialog_tab_selected").each(function(){
                                        if ($(this).attr("id").indexOf("info") > 0) { return; }
                                        var fTabID = jQuery(this).parent().find("a:first").attr("id");
                                        jQuery("div[aria-labelledby=\'cke_info_"+fTabID.substr(9)+"\']").find("input:first").val("'.URL::base(TRUE).'"+img);
                                      });'
                            .'}}</script>';
                } else {
		        // Add default rows and cols attributes (required)
		        $attributes += array('rows' => 10, 'cols' => 50);
                        $output = '<textarea'.HTML::attributes($attributes).'>'.HTML::chars($body, $double_encode).'</textarea>';
                }

		return $output;
	}

	/**
	 * Creates a select form input.
	 *
	 *     echo Form::select('country', $countries, $country);
	 *
	 * [!!] Support for multiple selected options was added in v3.0.7.
	 *
	 * @param   string  $name       input name
	 * @param   array   $options    available options
	 * @param   mixed   $selected   selected option string, or an array of selected options
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    HTML::attributes
	 */
	public static function select($name, array $options = NULL, $selected = NULL, array $attributes = NULL)
	{
		// Set the input name
		$attributes['name'] = $name;

		if (is_array($selected))
		{
			// This is a multi-select, god save us!
			$attributes[] = 'multiple';
		}

		if ( ! is_array($selected))
		{
			if ($selected === NULL)
			{
				// Use an empty array
				$selected = array();
			}
			else
			{
				// Convert the selected options to an array
				$selected = array( (string) $selected);
			}
		}

		if (empty($options))
		{
			// There are no options
			$options = '';
		}
		else
		{
			foreach ($options as $value => $name)
			{
				if (is_array($name))
				{
					// Create a new optgroup
					$group = array('label' => $value);

					// Create a new list of options
					$_options = array();

					foreach ($name as $_value => $_name)
					{
						// Force value to be string
						$_value = (string) $_value;

						// Create a new attribute set for this option
						$option = array('value' => $_value);

						if (in_array($_value, $selected))
						{
							// This option is selected
							$option[] = 'selected';
						}

						// Change the option to the HTML string
						$_options[] = '<option'.HTML::attributes($option).'>'.HTML::chars($_name, FALSE).'</option>';
					}

					// Compile the options into a string
					$_options = "\n".implode("\n", $_options)."\n";

					$options[$value] = '<optgroup'.HTML::attributes($group).'>'.$_options.'</optgroup>';
				}
				else
				{
					// Force value to be string
					$value = (string) $value;

					// Create a new attribute set for this option
					$option = array('value' => $value);

					if (in_array($value, $selected))
					{
						// This option is selected
						$option[] = 'selected';
					}

					// Change the option to the HTML string
					$options[$value] = '<option'.HTML::attributes($option).'>'.HTML::chars($name, FALSE).'</option>';
				}
			}

			// Compile the options into a single string
			$options = "\n".implode("\n", $options)."\n";
		}

		return '<select'.HTML::attributes($attributes).'>'.$options.'</select>';
	}

	/**
	 * Creates a submit form input.
	 *
	 *     echo Form::submit(NULL, 'Login');
	 *
	 * @param   string  $name       input name
	 * @param   string  $value      input value
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    Form::input
	 */
	public static function submit($name, $value, array $attributes = NULL)
	{
		$attributes['type'] = 'submit';

		return Form::input($name, $value, $attributes);
	}

	/**
	 * Creates a image form input.
	 *
	 *     echo Form::image(NULL, NULL, array('src' => 'media/img/login.png'));
	 *
	 * @param   string  $name       input name
	 * @param   string  $value      input value
	 * @param   array   $attributes html attributes
	 * @param   boolean $index      add index file to URL?
	 * @return  string
	 * @uses    Form::input
	 */
	public static function image($name, $value, array $attributes = NULL, $index = FALSE)
	{
		if ( ! empty($attributes['src']))
		{
			if (strpos($attributes['src'], '://') === FALSE)
			{
				// Add the base URL
				$attributes['src'] = URL::base($index).$attributes['src'];
			}
		}

		$attributes['type'] = 'image';

		return Form::input($name, $value, $attributes);
	}

	/**
	 * Creates a button form input. Note that the body of a button is NOT escaped,
	 * to allow images and other HTML to be used.
	 *
	 *     echo Form::button('save', 'Save Profile', array('type' => 'submit'));
	 *
	 * @param   string  $name       input name
	 * @param   string  $body       input value
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    HTML::attributes
	 */
	public static function button($name, $body, array $attributes = NULL)
	{
		// Set the input name
		$attributes['name'] = $name;

		return '<button'.HTML::attributes($attributes).'>'.$body.'</button>';
	}

	/**
	 * Creates a form label. Label text is not automatically translated.
	 *
	 *     echo Form::label('username', 'Username');
	 *
	 * @param   string  $input      target input
	 * @param   string  $text       label text
	 * @param   array   $attributes html attributes
	 * @return  string
	 * @uses    HTML::attributes
	 */
	public static function label($input, $text = NULL, array $attributes = NULL)
	{
		if ($text === NULL)
		{
			// Use the input name as the text
			$text = ucwords(preg_replace('/[\W_]+/', ' ', $input));
		}

		// Set the label target
		$attributes['for'] = $input;

		return '<label'.HTML::attributes($attributes).'>'.$text.'</label>';
	}

}
