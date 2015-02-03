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
     * Class constructor
     *
     * @param  array   $config  Configuration [Optional]
     * @param  string  $id      Session id [Optional]
     */
    public function __construct(array $config = array(), $id = NULL)
    {
        $session_server = Kohana::$config->load('session')->get('redis', array('server' => 'default'));

        $redis_server = isset($config['redis_server']) ? $config['redis_server'] : $session_server;

        $this->_redis = FRedis::instance($redis_server);

        if (isset($config['table_name'])) {

            $this->_table_name = $config['table_name'];

        }
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
        if ($id OR $id = Cookie::get($this->_name))
        {
            $session_key = $this->_table_name . ':' . $id;
            
            $usual_ip = $this->_redis->hGet($session_key, 'ip');

            if ( $usual_ip !== FALSE )
            {
                if ( $usual_ip == Request::$client_ip )
                {
                    if ($this->_expire())
                    {
                        throw new Exception_Session(__('Your session has benn expired. Please login again.'));
                    }

                    $this->_session_id = $id;
                    
                    return $this->_redis->hGet($session_key, 'content');
                }
                else 
                {
                    $this->destroy();

                    throw new Exception_Session(__('You are logging in an unusual place. Please login again for safe.'));
                }
            }
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

            $usual_ip = $this->_redis->hGet($session_id, 'ip');

        } while ($usual_ip !== FALSE);

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

        $this->_redis->hSet($session_key, 'ip', Request::$client_ip);        
        $this->_redis->hSet($session_key, 'content', $this->__toString());        
        $this->_redis->hSet($session_key, 'last_active', $this->_data['last_active']);        

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

        $this->_redis->hDel($session_key, 'ip');
        $this->_redis->hDel($session_key, 'content');
        $this->_redis->hDel($session_key, 'last_active');

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
