<?php

class Fateak_Session_Redis extends Session
{
    /**
     * Redis handler
     */
    protected $_redis;

    /**
     * Table name (Hash Type).Example:
     * session:xxxxxxxxxxxxxx
     */
    protected $_table_name = 'session';

    /**
     * Garbage collection requests
     * @var integer
     */
    protected $_gc = 500;

    /**
     * The current session id
     * @var string
     */
    protected $_session_id;

    /**
     * The old session id
     * @var string
     */
    protected $_update_id;

    /**
     * The client user id
     * @var integer
     */
    protected $_user_id;

    /**
     * Class constructor
     *
     * @param  array   $config  Configuration [Optional]
     * @param  string  $id      Session id [Optional]
     */
    public function __construct(array $config = array(), $id = NULL)
    {

        $redis_server = isset($config['redis_server']) ? $config['redis_server'] : 'default';

        $this->_redis = FRedis::instance($redis_server);

        if (isset($config['table_name'])) {

            $this->_table_name = $config['table_name'];

        }
    }

    public function id()
    {

    }

    /**
     * Loads the raw session data string and returns it.
     *
     * @param   string  $id session id
     * @return  string
     */
    protected function _read($id = NULL)
    {

    }

    /**
     * Generate a new session id and return it.
     *
     * @return  string
     */
    protected function _regenerate()
    {

    }

    /**
     * Writes the current session.
     *
     * @return  boolean
     */
    protected function _write()
    {

    }

    /**
     * Destroys the current session.
     *
     * @return  boolean
     */
    protected function _destroy()
    {

    }

    /**
     * Restarts the current session.
     *
     * @return  boolean
     */
    protected function _restart()
    {

    }
}
