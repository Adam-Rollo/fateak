<?php

class Fateak_ACL
{
    /** Rule type: deny */
    const DENY = FALSE;

    /** Rule type: allow */
    const ALLOW = TRUE;

    public static $_perm = array();


    /**
     * Retrieves all named permissions
     *
     * Example:
     * ~~~
     * $permissions = ACL::all();
     * ~~~
     *
     * @param   boolean 
     * @return  array  Perms by name
     */
    public static function all($refresh_cache = FALSE)
    {
        $result = array();

        $result['menus'] = self::menus_permission();
        return self::$_all_perms;
    }

    /**
     * Get permissions from config/acl.php
     */
    public static function extra_permissions()
    {
        $config = Kohana::$config->load('acl');

        return $config->as_array();
    }

    /**
     * Get action permissions
     */
    public static function action_permissions()
    {
        $modules = Module::modules();
        $permissions = array();

        foreach ($modules as $name => $path)
        {
            $controller_path = $path . 'classes' . DS . 'Controller';

            $permissions[$name] = self::find_actions($controller_path); 
        }

        return $permissions;
    }

    /**
     * Find all actions of controller
     */
    protected static function find_actions($path)
    {
        $result = array();

        if (is_dir($path))
        {
            $dir = opendir($path);
        }
        else
        {
            return null;
        }

        while (($cp = readdir($dir)) !== false)
        {
            if ($cp == '.' || $cp == '..')
            {
                continue; 
            }

            $full_path = $path . DS . $cp;

            if (is_dir($full_path))
            {
                $result[$cp] = self::find_actions($full_path); 
            }
            else
            {
                $class_name = substr($cp, 0, strpos('.', $cp));
                $result[$cp] = $class_name;
            }

            // Release memory in loop
            unset($cp);
        }

        return $result;
    }

    /**
     * Get permissions from menus
     *
     * Example: Access Permission of user-list is called menu-user-list
     */
    public static function menus_permissions()
    {
        $result = array();
        $menus = array();

        $root_menus = Menu::root_menus(); 

        foreach ($root_menus as $root_menu)
        {
            $menus[$root_menu->name] = array('children' => Menu::items($root_menu->name)->get_items()); 
        }

        $result = self::menu2permission($menus);
        
        return $result;
    }

    /**
     * Calculation of menus
     */
    protected static function menu2permission($menus)
    {
        $permissions = array();

        foreach ($menus as $name => $detail)
        {
            if (is_array($detail) && is_array($detail['children']) )
            {
                $permissions['menu-' . $name] = self::menu2permission($detail['children']);
            }
            else
            {
                $permissions['menu-' . $name] = $detail['url']; 
            }
        }

        return $permissions;
    }

    /**
     * Setter/Getter for ACL cache
     *
     * If your perms will remain the same for a long period of time, use this
     * to reload the ACL from the cache rather than redefining them on every page load.
     *
     * Example:
     * ~~~
     *  if ( ! ACL::cache())
     *  {
     *    // Set perms here
     *    ACL::cache(TRUE);
     *  }
     * ~~~
     *
     * @param   boolean  $save    Cache the current perms [Optional]
     * @param   boolean  $append  Append, rather than replace, cached perms when loading [Optional]
     *
     * @return  boolean
     *
     * @uses    Cache::set
     * @uses    Cache::get
     * @uses    Arr::merge
     */
    public static function cache($save = FALSE, $append = FALSE)
    {
        $cache = Cache::instance();

        if ($save)
        {
            // set the cache for performance in production
            if (Kohana::$environment === Kohana::PRODUCTION)
            {
                // Cache all defined perms
                return $cache->set('ACL::cache()', self::$_all_perms);
            }

            return false;
        }
        else
        {
            if ($perms = $cache->get('ACL::cache()'))
            {
                if ($append)
                {
                    // Append cached perms
                    self::$_all_perms = Arr::merge(self::$_all_perms, $perms);
                }
                else
                {
                    // Replace existing perms
                    self::$_all_perms = $perms;
                }

                // perms were cached
                return self::$cache = TRUE;
            }
            else
            {
                // perms were not cached
                return self::$cache = FALSE;
            }
        }
    }

    /**
     * Check permission for user
     *
     * If the user doesn't have this permission,
     * failed with an HTTP_Exception_403 or execute `$callback` if it is defined
     *
     * Example:
     * ~~~
     * // Example with a callable function
     * ACL::required(
     *    'administer site',
     *    NULL,
     *    $this->request->redirect(Route::get('user')->uri(array('action' => 'login')))
     * );
     *
     * // Simple check
     *   ACL::required('administer site');
     * ~~~
     *
     * @since     2.0
     *
     * @param     string      $perm_name  Permission name
     * @param     Model_User  $user       User object [Optional]
     * @param     callable    $callback   A callable function that execute if it is defined [Optional]
     * @param     array       $args       The callback arguments
     *
     * @return    boolean
     *
     * @throws    HTTP_Exception_403 If the user doesn't have permission
     * @throws    Exception          if the `$callback` is a not valid callback
     */
    public static function required($perm_name, Model_User $user = NULL, $callback = NULL, array $args = array())
    {
        if ( ! self::check($perm_name, $user))
        {
            if ( ! is_null($callback))
            {
                // Check if the $callback is a valid callback
                if ( ! is_callable($callback))
                {
                    throw new Exception('An invalid callback was added to the ACL::required().');
                }
                call_user_func($callback, $args);

                return;
            }

            // If the action is set and the role hasn't been matched, the user doesn't have permission
            throw HTTP_Exception::factory(403, 'Unauthorized attempt to access action :perm.',
                    array(':perm' => $perm_name));
        }
    }

    /**
     * Check permission for current user
     *
     * Checks permission and redirects if is required to URL
     * defined in `$route`
     *
     * @since  2.0
     * @param  string  $perm_name  Permission name
     * @param  string  $route      Route name [Optional]
     * @param  array   $uri        Additional route params [Optional]
     *
     * @throws HTTP_Exception_403
     *
     * @uses   Request::redirect()
     * @uses   Route::get()
     */
    public static function redirect($perm_name, $route = NULL, array $uri = array())
    {
        if ( ! self::check($perm_name))
        {
            if ( ! is_null($route) AND is_string($route))
            {
                Request::initial()->redirect(Route::get($route)->uri($uri), 403);

                return;
            }

            // If the action is set and the role hasn't been matched, the user doesn't have permission.
            throw HTTP_Exception::factory(403, 'Unauthorized attempt to access action :perm.',
                    array(':perm' => $perm_name));
        }
    }

    /**
     * Checks if the current user has permission to access the current request
     *
     * If the user is not given, used currently active user
     *
     * @param   string      $perm_name  Permission name
     * @param   Model_User  $user       User object [Optional]
     *
     * @return  boolean
     *
     * @uses    User::active_user
     */
    public static function check($perm_name, Model_User $user = NULL)
    {
        // If we weren't given an auth object
        if (is_null($user))
        {
            // Just get the default instance.
            $user = User::active_user();

            if (is_null($user))
            {
                return false;
            }
        }

        // User #2 has all privileges:
        if ($user->id == User::ADMIN_ID)
        {
            return self::ALLOW;
        }

        // To reduce the number of SQL queries, we cache the user's permissions
        // in a static variable.
        if ( ! isset(self::$_perm[$user->id]))
        {
            self::_set_permissions($user);
        }

        return isset(self::$_perm[$user->id][$perm_name]);
    }

    /**
     * set permission
     */
    protected static function _set_permissions($user)
    {
        $roles = self::get_user_roles($user);

        $permissions = array();

        foreach ($roles as $role)
        {
            $result = DB::select('permission')
                ->from('permissions')
                ->where('rid', '=', $role->id)
                ->execute();

            foreach ($result as $p)
            {
                $permission_name = $p['permission'];
                if (! isset($permissions[$permission_name]))
                    $permissions[$permission_name] = self::ALLOW;
            }
        }

        self::$_perm[$user->id] = $permissions;
    }

    /**
     * Get all roles for user
     *
     * @since   2.0
     * @param   Model_User  $user  User object
     * @return  array  All roles for user
     */
    public static function get_user_roles(Model_User $user)
    {
        $roles = $user->roles->find_all()->as_array();

        return $roles;
    }

    /**
     * Get all user's permission
     */
    public static function get_user_permissions(Model_User $user)
    {
        if ( ! isset(self::$_perm[$user->id]))
        {
            self::_set_permissions($user);
        }

        return self::$_perm[$user->id];
    }
}
