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
     * Life time
     */
    protected $_lifetime;

    /**
     * The current session id
     * @var string
     */
    protected $_session_id;

    /**
     * Class constructor
     *
     * @param  array   $config  Configuration [Optional]
     * @param  string  $id      Session id [Optional]
     */
    public function __construct(array $config = NULL, $id = NULL)
    {
        $session_config = Kohana::$config->load('session')->get('redis');

        $redis_server = isset($config['redis_server']) ? $config['redis_server'] : $session_config['server'];
        $this->_lifetime = isset($config['lifetime']) ? $config['lifetime'] : $session_config['lifetime'];

        $this->_redis = FRedis::instance($redis_server);

        if (isset($config['table_name'])) {

            $this->_table_name = $config['table_name'];

        }

        parent::__construct($config, $id);
    }

    public function id()
    {
        return $this->_session_id;
    }

    /**
     * Loads the raw session data string and returns it.
     *
     * @param   string  $id session id
     * @return  string
     */
    protected function _read($id = NULL)
    {
        try
        {
            if ($id OR $id = Cookie::get($this->_name))
            {
                $session_key = $this->_table_name . ':' . $id;

                $this->_session_id = $id;

                if ($this->_expire())
                {
                    Message::alert(__('Your session has benn expired. Please login again.'));

                    throw new Exception_Session(__('Your session has benn expired. Please login again.'));
                }

                return $this->_redis->hGet($session_key, 'content');

            }
        }
        catch (Exception $e)
        {
            Log::debug("Session Error:" . $e->getLine() . " : " . $e->getMessage());
        }

        // Create a new session id
        $this->_regenerate();

        return NULL;
    }

    /**
     * Generate a new session id and return it.
     *
     * @return  string
     */
    protected function _regenerate()
    {
        do
        {
            // Create a new session id
            $id = str_replace('.', '-', uniqid(NULL, TRUE));
            $session_id = $this->_table_name . ":" . $id;

            $usual_ips = $this->_redis->hGet($session_id, 'ips');

        } while ($usual_ips !== FALSE);

        $this->_session_id = $id;
    }

    /**
     * Writes the current session.
     *
     * @return  boolean
     */
    protected function _write()
    {
        $session_key = $this->_table_name . ":" . $this->_session_id;

        $this->_redis->hSet($session_key, 'content', $this->__toString());        
        $this->_redis->hSet($session_key, 'last_active', $this->_data['last_active']);        
        $this->_redis->setTimeout($session_key, $this->_lifetime);        

        Cookie::set($this->_name, $this->_session_id, $this->_lifetime);

        return TRUE;
    }

    /**
     * Destroys the current session.
     *
     * @return  boolean
     */
    protected function _destroy()
    {
        $session_key = $this->_table_name . ":" . $this->_session_id;

        $this->_redis->delete($session_key);

        Cookie::delete($this->_name);
    }

    /**
     * Restarts the current session.
     *
     * @return  boolean
     */
    protected function _restart()
    {
        $this->_regenerate();

        return TRUE;
    }

    /**
     * Overtime process
     */
    protected function _expire()
    {
        $session_key = $this->_table_name . ":" . $this->_session_id;

        if ($this->_lifetime)
        {
            // Expire sessions when their lifetime is up
            $expires = $this->_lifetime;
        }
        else
        {
            // Expire sessions after one month
            $expires = Date::MONTH;
        }

        if ( (time() - $this->_redis->hGet($session_key, 'last_active')) > $expires )
        {
            return $this->restart();
        }

        return FALSE;
    }
}
