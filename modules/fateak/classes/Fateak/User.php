<?php
/**
 * All information about User
 * It is different from Model_User
 * Model_User only is a object representing table users. so it just process user's base information
 * This class contains all information about user, such as base info, user profile, work, order, address and so on...
 *
 * @author Rollo - Fateak 
 */
class Fateak_User
{
    const ADMIN_ID = 0;

    /**
     * Cache the user info
     * @var User
     */
    protected static $_current_user = NULL;

    /**
     * Base information
     * @var Model_User
     */
    protected $_base_info;

    /**
     * Extra information
     */
    protected $_extra_info;

    /**
     * Roles
     */
    protected $_roles;

    /**
     * Permissions
     */
    protected $_permissions;

    /**
     * Return the active user.  If there's no active user, return null.
     * Assign array() to $extra_info to get an instance of class User.
     *
     * @param  Array
     * @return User
     */
    public static function active_user( $extra_info = NULL )
    {
        if (is_null(self::$_current_user)) 
        {

            $base_info =  Auth::instance()->get_user();

            if (is_null($base_info))
            {
                return null;
            }

            self::$_current_user = new User(array('base' => $base_info), $extra_info);
        }
        else
        {
            $base_info = self::$_current_user->get();
        }

        $roles = ACL::get_user_roles($base_info);

        foreach ($roles as $role)
        {
            self::$_current_user->_roles[] = $role->name;

        }

        self::$_current_user->_permissions = ACL::get_user_permissions($base_info);

        return is_null($extra_info) ? self::$_current_user->get() : self::$_current_user;
    }


    /**
     * construction
     * @param Model_User
     */
    public function __construct( $info, $extra_info )
    {
        $this->_base_info = $info['base'];

        if (! is_null($extra_info) && is_array($extra_info))
        {
            foreach ($extra_info as $info)
            {
                $this->add_extra_info();
            }
        }
    }

    /**
     * Add extra information
     */
    public function add_extra_info($name)
    {
        $model = 'user_' . $name;

        $object = ORM::factory($model);

        $this->_extra_info[$name] = $object->get_user_info();
    }

    /**
     * Get any information from User object
     */
    public function get($group = 'base')
    {
        if ($group == 'base')
        {
            return $this->_base_info;
        }
        else 
        {
            return $this->_extra_info[$group];
        }
    }

    /**
     * roles
     *
     * @param String
     * @return boolean
     */
    public function is_role($role)
    {
        return in_array($role, $this->_roles) || ($this->_base_info->id == self::ADMIN_ID);
    }

    /**
     * User file system group
     */
    public static function get_group($user_id)
    {
        $upload_config = Kohana::$config->load('file');

        return ceil( $user_id / $upload_config['user_number_per_group']);
    }

    /**
     * Set current user for app/api
     */
    public static function set_user($user_id, $extra_info = array())
    {
        $user = ORM::factory("User", $user_id);
        self::$_current_user = new User(array('base' => $user), $extra_info);
        self::$_current_user->_permissions = ACL::get_user_permissions($user);
    }

}
