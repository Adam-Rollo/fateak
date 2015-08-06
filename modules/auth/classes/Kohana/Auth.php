<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * User authorization library. Handles user login and logout, as well as secure
 * password hashing.
 *
 * @package    Kohana/Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Kohana_Auth {

    // Auth instances
    protected static $_instance;

    /**
     * Singleton pattern
     *
     * @return Auth
     */
    public static function instance()
    {
        if ( ! isset(Auth::$_instance))
        {
            // Load the configuration for this type
            $config = Kohana::$config->load('auth');

            if ( ! $type = $config->get('driver'))
            {
                $type = 'file';
            }

            // Set the session class name
            $class = 'Auth_'.ucfirst($type);

            // Create a new session instance
            Auth::$_instance = new $class($config);
        }

        return Auth::$_instance;
    }

    protected $_session;

    protected $_config;

    /**
     * Loads Session and configuration options.
     *
     * @param   array  $config  Config Options
     * @return  void
     */
    public function __construct($config = array())
    {
        // Save the config in the object
        $this->_config = $config;

        $this->_session = Session::instance($this->_config['session_type']);
    }

    abstract protected function _login($username, $password, $remember);

    abstract public function password($username);

    abstract public function check_password($password);

    /**
     * Gets the currently logged in user from the session.
     * Returns NULL if no user is currently logged in.
     *
     * @param   mixed  $default  Default value to return if the user is currently not logged in.
     * @return  mixed
     */
    public function get_user($default = NULL)
    {
        $user = $this->_session->get($this->_config['session_key'], $default);

        if (is_null($user))
        {
            return NULL;
        }

        $usual_ips = $user->ips;

        if ($usual_ips == '0.0.0.0')
        {
            return $user;
        }
        else
        {
            $usual_ips = explode(',', $usual_ips);

            if (in_array(Request::$client_ip, $usual_ips))
            {
                return $user;
            }
            else
            {
                $this->logout();

                Message::set(Message::WARN, __('You are logging in an unusual place. Please login again for safe.'));

                throw HTTP_Exception::factory(403, 'You are logging in an unusual place. Please login again for safe.');
            }
        }

        return NULL;
    }

    /**
     * Gets the role of current user from the session.
     * Returns NULL if no user is currently logged in.
     *
     * @param   mixed  $default  Default value to return if the user is currently not logged in.
     * @return  mixed
     */
    public function get_roles($default = NULL)
    {
        return $this->_session->get($this->_config['roles_session_key'], $default);
    }

    /**
     * Gets the permissions of current user from the session.
     * Returns NULL if no user is currently logged in.
     *
     * @param   mixed  $default  Default value to return if the user is currently not logged in.
     * @return  mixed
     */
    public function get_permissions($default = NULL)
    {
        return $this->_session->get($this->_config['permissions_session_key'], $default);
    }

    /**
     * Attempt to log in a user by using an ORM object and plain-text password.
     *
     * @param   string   $username  Username to log in
     * @param   string   $password  Password to check against
     * @param   boolean  $remember  Enable autologin
     * @return  boolean
     */
    public function login($username, $password, $remember = FALSE)
    {
        if (empty($password))
            return FALSE;

        return $this->_login($username, $password, $remember);
    }

    /**
     * Attempt to log in a user by using an ORM object and plain-text password from APP.
     *
     * @param   string   $username  Username to log in
     * @param   string   $password  Password to check against
     * @param   boolean  $remember  Enable autologin
     * @return  boolean
     */
    public function login_by_app($username, $password)
    {
        if (empty($password))
            return FALSE;

        return $this->_login_by_app($username, $password);
    }

    /**
     * Log out a user by removing the related session variables.
     *
     * @param   boolean  $destroy     Completely destroy the session
     * @param   boolean  $logout_all  Remove all tokens for user
     * @return  boolean
     */
    public function logout($destroy = FALSE, $logout_all = FALSE)
    {
        if ($destroy === TRUE)
        {
            // Destroy the session completely
            $this->_session->destroy();
        }
        else
        {
            // Remove the user from the session
            $this->_session->delete($this->_config['session_key']);
            $this->_session->delete($this->_config['roles_session_key']);
            $this->_session->delete($this->_config['permissions_session_key']);
            $this->_session->set('ips', '0.0.0.0');

            // Regenerate session_id
            $this->_session->regenerate();
        }

        // Double check
        return ! $this->logged_in();
    }

    /**
     * Check if there is an active session. Optionally allows checking for a
     * specific role.
     *
     * @param   string  $role  role name
     * @return  mixed
     */
    public function logged_in($role = NULL)
    {
        return ($this->get_user() !== NULL);
    }

    /**
     * Creates a hashed hmac password from a plaintext password. This
     * method is deprecated, [Auth::hash] should be used instead.
     *
     * @deprecated
     * @param  string  $password Plaintext password
     */
    public function hash_password($password)
    {
        return $this->hash($password);
    }

    /**
     * Perform a hmac hash, using the configured method.
     *
     * @param   string  $str  string to hash
     * @return  string
     */
    public function hash($str)
    {
        if ( ! $this->_config['hash_key'])
            throw new Kohana_Exception('A valid hash key must be set in your auth config.');

        return hash_hmac($this->_config['hash_method'], $str, $this->_config['hash_key']);
    }

    protected function complete_login($user)
    {
        // Regenerate session_id
        $this->_session->regenerate();

        // Store username in session
        $this->_session->set($this->_config['session_key'], $user);
        $this->_session->set('ips', $user->ips);

        // Store roles in session
        $roles = $user->roles->find_all()->as_array();
        $this->_session->set($this->_config['roles_session_key'], $roles);

        // Store permissions in session
        $permissions = array();
        foreach ($roles as $role)
        {
            $result = DB::select('permission')
                ->from('permissions')
                ->where('rid', '=', $role['id'])
                ->execute();

            foreach ($result as $p)
            {
                $permission_name = $p['permission'];

                if (! isset($permissions[$permission_name]))
                    $permissions[$permission_name] = ACL::ALLOW;
            }
        }

        $this->_session->set($this->_config['permissions_session_key'], $permissions);

        return TRUE;
    }

} // End Auth
