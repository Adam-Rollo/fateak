<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Default auth user
 *
 * @package    Kohana/Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Model_Auth_User extends ORM {

        /**
         * Fateak - Rollo
         * Has One relationshop for Table division.
         */
        protected $_has_one = array(
                'user_profile' => array('model' => 'User_Profile', 'foreign_key' => 'id'),        
        );

    /**
     * A user has many tokens and roles
     *
     * @var array Relationhips
     */
    protected $_has_many = array(
        'user_tokens' => array('model' => 'User_Token'),
        'roles'       => array('model' => 'Role', 'through' => 'roles_users'),
    );

    /**
     * Rules for the user model. Because the password is _always_ a hash
     * when it's set,you need to run an additional not_empty rule in your controller
     * to make sure you didn't hash an empty string. The password rules
     * should be enforced outside the model or with a model helper method.
     *
     * @return array Rules
     */
    public function rules()
    {
        return array(
            'email' => array(
                array('not_empty'),
                array('email'),
                array(array($this, 'unique'), array('email', ':value')),
            ),
            'password' => array(
                array('not_empty'),
            ),
        );
    }

    /**
     * Filters to run when data is set in this model. The password filter
     * automatically hashes the password when it's set in the model.
     *
     * @return array Filters
     */
    public function filters()
    {
        return array(
            'password' => array(
                array(array(Auth::instance(), 'hash'))
            )
        );
    }

    /**
     * Labels for fields in this model
     *
     * @return array Labels
     */
    public function labels()
    {
        return array(
            'email'            => 'email address',
            'password'         => 'password',
        );
    }

    /**
     * Complete the login for a user by incrementing the logins and saving login timestamp
     *
     * @return void
     */
    public function complete_login()
    {
        if ($this->_loaded)
        {
            // Update the number of logins
            $this->logins = new Database_Expression('logins + 1');

            // Set the last login date
            $this->last_login = time();

            // Commonly used IP -- Fateak - Rollo
            $ips = ($this->ips == '') ? array() : explode(',', $this->ips);
            if (! in_array(Request::$client_ip, $ips))
            {
                if (count($ips) > 3)
                {
                    array_shift($ips);
                }

                $ips[] = Request::$client_ip;
                $this->ips = implode(',', $ips);
            }

            // Save the user
            $this->update();
        }
    }

    /**
     * Tests if a unique key value exists in the database.
     *
     * @param   mixed    the value to test
     * @param   string   field name
     * @return  boolean
     */
    public function unique_key_exists($value, $field = NULL)
    {
        if ($field === NULL)
        {
            // Automatically determine field by looking at the value
            $field = $this->unique_key($value);
        }

        return (bool) DB::select(array(DB::expr('COUNT(*)'), 'total_count'))
            ->from($this->_table_name)
            ->where($field, '=', $value)
            ->where($this->_primary_key, '!=', $this->pk())
            ->execute($this->_db)
            ->get('total_count');
    }

    /**
     * Allows a model use both email and username as unique identifiers for login
     *
     * @param   string  unique value
     * @return  string  field name
     */
    public function unique_key($value)
    {
        return Valid::email($value) ? 'email' : 'username';
    }

    /**
     * Password validation for plain passwords.
     *
     * @param array $values
     * @return Validation
     */
    public static function get_password_validation($values)
    {
        return Validation::factory($values)
            ->rule('password', 'min_length', array(':value', 8))
            ->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));
    }

    /**
     * Create a new user
     *
     * Example usage:
     * ~~~
     * $user = ORM::factory('User')->create_user($_POST, array(
     *  'username',
     *  'password',
     *  'email',
     * );
     * ~~~
     *
     * @param array $values
     * @param array $expected
     * @throws ORM_Validation_Exception
     */
    public function create_user($values, $expected)
    {
        // Validation for passwords
        $extra_validation = Model_User::get_password_validation($values)
            ->rule('password', 'not_empty');

        $result = $this->values($values, $expected)->create($extra_validation);

                $return = array( 'errors' => array(), 'uid' => $result->pk());
                Module::action('create_user', $return);

                if (! empty($return['errors']))
                {
                    $message = implode(', ', $return['errors']);
                    throw new Kohana_Exception($message);
                }

                return $result;
    }

    /**
     * Update an existing user
     *
     * [!!] We make the assumption that if a user does not supply a password, that they do not wish to update their password.
     *
     * Example usage:
     * ~~~
     * $user = ORM::factory('User')
     *  ->where('username', '=', 'kiall')
     *  ->find()
     *  ->update_user($_POST, array(
     *      'username',
     *      'password',
     *      'email',
     *  );
     * ~~~
     *
     * @param array $values
     * @param array $expected
     * @throws ORM_Validation_Exception
     */
    public function update_user($values, $expected = NULL)
    {
        if (empty($values['password']))
        {
            unset($values['password'], $values['password_confirm']);
        }

        // Validation for passwords
        $extra_validation = Model_User::get_password_validation($values);

        return $this->values($values, $expected)->update($extra_validation);
    }

    /**
     * Fateak - Rollo
     */
    public function refresh_roles($roles)
    {
        if (in_array(0, $roles))
        {
            throw new Kohana_Exception('You are fool me !');
        }

        $this->_db->begin();

        $this->remove('roles');

        $this->add('roles', $roles);

        $this->_db->commit();
    }

} // End Auth User Model
