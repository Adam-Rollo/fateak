<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Default auth role
 *
 * @package    Kohana/Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Model_Auth_Role extends ORM {

    /**
     * Permission DB
     */
    static public $_permission_db = null;

	// Relationships
	protected $_has_many = array(
		'users' => array('model' => 'User','through' => 'roles_users'),
	);

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('min_length', array(':value', 4)),
				array('max_length', array(':value', 32)),
			),
			'description' => array(
				array('max_length', array(':value', 255)),
			)
		);
	}

    /**
     * Get Permissions
     */
     public function permissions($rid = NULL)
     {
        if (is_null($rid))
        {
            $rid = $this->id;
        }

        $permissions = DB::select('permission')
            ->from('permissions')
            ->where('rid', '=', $rid)
            ->execute(self::$_permission_db)
            ->as_array();

        return $permissions;
     }

     /**
      * Set Permissions
      */
     public function update_permissions($permissions, $rid = NULL)
     {
        if (is_null($rid))
        {
            $rid = $this->id;
        }

        $this->_db->begin();   

        DB::delete('permissions')
            ->where('rid', '=', $rid)
            ->execute(self::$_permission_db);

        foreach($permissions as $permission)
        {
            $prefix = substr($permission, 0, strpos($permission, '-'));
            switch($prefix)
            {
                case 'menu':
                    break;
                case 'controller':
                    break;
                default:
                    $prefix = 'other';
            }

            DB::insert('permissions')
                ->columns(array('rid', 'permission', 'module'))
                ->values(array($rid, $permission, $prefix))
                ->execute(self::$_permission_db);
        }

        $this->_db->commit();
     }

} // End Auth Role Model
