<?php
/**
 * Email message building and sending
 *
 * @package    Gleez\Email
 * @author     Gleez Team
 * @version    1.1.5
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    http://gleezcms.org/license Gleez CMS License
 * @link       https://github.com/Synchro/PHPMailer
 */
class Gleez_Email {

	/**
	 * Mail object
	 * @var PHPMailer
	 */
	protected $_mail;

	/**
	 * Create a new email message
	 *
	 * @param   boolean  $exceptions  PHPMailer should throw external exceptions? [Optional]
	 * @return  Email
	 */
	public static function factory($exceptions = TRUE)
	{
		return new Email($exceptions);
	}

        /**
         * Fateak - Rollo
         * Send Email
         * Email Queue: 1) queue:confirmation.email 2) queue:notification:email
         * Queue switch: queue:email:switch
         */
        public static function send_email($toEmail, $title, $tpl, $params = array(), $refresh_cache = false, $queue_type = 'confirmation')
        {
            $config = Kohana::$config->load('email');

            $queue = $config['email_queue'];

            $refresh = $refresh_cache ? 'Y' : 'N';

            $content_array = array(
                    'title' => $title, 
                    'email' => $toEmail, 
                    'tpl' => $tpl, 
                    'vars' => JSON::encode($params), 
                    'refresh' => $refresh,
                    'lang' => I18n::lang(),
            );

            if ($queue)
            {
                $redis = FRedis::instance();

                array_walk($content_array, function(& $v, $k){
                    $v = base64_encode($v);
                });

                $key = "queue:" . $queue_type . ".email";

                $redis->lpush($key, serialize($content_array));
            }
            else
            {
                Letitgo::execute('Email', $content_array);
            }
        }

	/**
	 * Class constructor
	 *
	 * @param   boolean  $exceptions  PHPMailer should throw external exceptions? [Optional]
	 */
	public function __construct($exceptions = TRUE)
	{
		require_once Kohana::find_file('vendor/PHPMailer', 'PHPMailerAutoload');

		// Create phpmailer object
		$this->_mail = new PHPMailer($exceptions);

                // Load configuration
                $site_config = Kohana::$config->load('email');

		// Set some defaults
		$this->_mail->setFrom($site_config['site_email'], $site_config['email_site_name']);
		$this->_mail->WordWrap = 70;
		$this->_mail->CharSet  = Kohana::$charset;
		$this->_mail->XMailer  = Kohana::version(FALSE, TRUE);
		$this->_mail->setLanguage(I18n::$lang);
		$this->_mail->Debugoutput = 'error_log';

                // Set server infomation
                $this->_mail->SMTPAuth = true;
                $this->_mail->isSMTP();
                $this->_mail->Encoding = "base64";
                $this->_mail->IsHTML(true);
                $this->_mail->Host = $site_config['email_host'];
                $this->_mail->Username = $site_config['site_email'];
                $this->_mail->Password = $site_config['email_pass'];
                $this->_mail->FromName = $site_config['email_site_name'];
	}

	/**
	 * Set the message subject
	 *
	 * @param   string  $subject  New subject
	 * @return  Email
	 */
	public function subject($subject)
	{
		// Change the subject
		$this->_mail->Subject = $subject;

		return $this;
	}

	/**
	 * Set the message body
	 *
	 * Multiple bodies with different types can be added by calling this method
	 * multiple times. Every email is required to have a "plain" message body.
	 *
	 * @param   string  $body  New message body
	 * @param   string  $type  Mime type: text/html, text/plain [Optional]
	 * @return  Email
	 */
	public function message($body, $type = NULL)
	{
		if ( ! $type OR $type === 'text/plain')
		{
			// Set the main text/plain body
			$this->_mail->Body = $body;
		}
		else
		{
			// Add a custom mime type
			$this->_mail->msgHTML($body);
		}

		return $this;
	}

	/**
	 * Add one or more email recipients
	 *
	 * Example:
	 * ~~~
	 * // A single recipient
	 * $email->to('john.doe@domain.com', 'John Doe');
	 * ~~~
	 *
	 * @param   string  $email  Single email address
	 * @param   string  $name   Full name [Optional]
	 * @return  Email
	 */
	public function to($email, $name = NULL)
	{
		$this->_mail->addAddress($email, $name);

		return $this;
	}

	/**
	 * Add a "carbon copy" email recipient
	 *
	 * @param   string  $email  Email address
	 * @param   string  $name   Full name [Optional]
	 * @return  Email
	 */
	public function cc($email, $name = NULL)
	{
		$this->_mail->addCC($email, $name);

		return $this;
	}

	/**
	 * Add a "blind carbon copy" email recipient
	 *
	 * @param   string  $email  Email address
	 * @param   string  $name   Full name [Optional]
	 * @return  Email
	 */
	public function bcc($email, $name = NULL)
	{
		$this->_mail->addBCC($email, $name);

		return $this;
	}

	/**
	 * Add email senders
	 *
	 * @param   string  $email  Email address
	 * @param   string  $name   Full name [Optional]
	 * @return  Email
	 */
	public function from($email, $name = NULL )
	{
		$this->_mail->setFrom($email, $name);

		return $this;
	}

	/**
	 * Add "reply to" email sender
	 *
	 * @param   string  $email  Email address
	 * @param   string  $name   Full name [Optional]
	 * @return  Email
	 */
	public function reply_to($email, $name = NULL)
	{
		$this->_mail->addReplyTo($email, $name);

		return $this;
	}

	/**
	 * Set the return path for bounce messages
	 *
	 * @param   string  $email  Email address
	 * @return  Email
	 */
	public function return_path($email)
	{
		$this->_mail->Sender = $email;

		return $this;
	}

	/**
	 * Sends the email
	 *
	 * @return  boolean
	 */
	public function send()
	{
		try
		{
			$this->_mail->send();
			return TRUE;
		}
		catch(Exception $e)
		{
			$message = __('Error sending mail error: :e', array(':e' => $e->getMessage()));
                        $log = Log::instance();
                        $log->add(Log::ERROR, $message);
			return FALSE;
		}
	}

	/**
	 * Mail object of the instance
	 *
	 * @return  PHPMailer
	 */
	public function mail()
	{
		return $this->_mail;
	}

}
