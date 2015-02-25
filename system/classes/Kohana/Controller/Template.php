<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Abstract controller class for automatic templating.
 *
 * @package    Kohana
 * @category   Controller
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Kohana_Controller_Template extends Controller {

	/**
	 * @var  View  page template
	 */
	public $template = 'template';

	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = TRUE;

	/**
	 * Loads the template [View] object.
	 */
	public function before()
	{
		parent::before();

		if ($this->auto_render === TRUE)
		{
			// Load the template
			$this->template = View::factory($this->template);
		}
	}

	/**
	 * Assigns the template [View] as the request response.
	 */
	public function after()
	{
		if ($this->auto_render === TRUE)
		{
			$this->response->body($this->template->render());
		}

		parent::after();
	}

	/**
	 * Returns TRUE if the POST has a valid CSRF
	 *
	 * Usage:<br>
	 * <code>
	 * 	if ($this->valid_post('upload_photo')) { ... }
	 * </code>
	 *
	 * @param   string|NULL  $submit Submit value [Optional]
	 * @return  boolean  Return TRUE if it's valid $_POST
	 *
	 * @uses    Request::is_post
	 * @uses    Request::post_max_size_exceeded
	 * @uses    Request::get_post_max_size
	 * @uses    Request::post
	 * @uses    Message::error
	 * @uses    CSRF::valid
	 * @uses    Captcha::valid
	 */
	public function valid_post($submit = NULL)
	{
		if ( ! $this->request->is_post())
		{
			return FALSE;
		}

		if (Request::post_max_size_exceeded())
		{
			$this->_errors = array('_action' => __('Max file size of :max Bytes exceeded!',
				array(':max' => Request::get_post_max_size())) );
			return FALSE;
		}

		if ( ! is_null($submit) )
		{
			if ( ! isset($_POST[$submit]))
			{
				$this->_errors = array('_action' => __('This form has altered. Please try submitting it again.'));
				return FALSE;
			}
		}

		$_token  = $this->request->post('_token');
		$_action = $this->request->post('_action');

		$has_csrf = ! empty($_token) AND ! empty($_action);
		$valid_csrf = CSRF::valid($_token, $_action);

		if ($has_csrf AND ! $valid_csrf)
		{
			// CSRF was submitted but expired
			$this->_errors = array('_token' => __('This form has expired. Please try submitting it again.'));
			return FALSE;
		}

		if (isset($_POST['_captcha']))
		{
			$captcha = $this->request->post('_captcha');
			if (empty($captcha))
			{
				// CSRF was not entered
				$this->_errors = array('_captcha' => __('The security code can\'t be empty.'));
				return FALSE;
			}
			elseif ( ! Captcha::valid($captcha))
			{
				$this->_errors = array('_captcha' => __('The security answer was wrong.'));
				return FALSE;
			}
		}

		return $has_csrf AND $valid_csrf;
	}

}
