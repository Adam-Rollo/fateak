<?php
/**
 * Rely on mongo >= 2.4.9
 * FMongoManager = MongoDB Manager In Fateak
 */
class Fateak_FMongoManager 
{
    /**
     * An array store all redis servers
     * All available servers should be config in config/database.php
     *
     * 'mongo_default' => array
     * (
     *     'host' => '127.0.0.1',
     *     'port' => '27017',
     * ),
     */
    protected static $_servers = array();
    
    // store collections 
    public $manager = null;

    protected $_collections = array();

    protected $_writeConcern = null;

    protected $_redis;

    /**
     * Singleton, MongoDB connection
     */
    public static function instance($server = 'default', $config = array())
    {
        if (isset(self::$_servers[$server])) 
        {
            return self::$_servers[$server];
        } 
        else 
        {
            $server_name = 'mongo_' . $server;
            $database_config = Kohana::$config->load('database');

            if (isset($database_config[$server_name])) 
            {
                $server_config = $database_config[$server_name];

                $db_info = "mongodb://{$server_config['host']}:{$server_config['port']}";

                $mongo_manager = new MongoDB\Driver\Manager($db_info);

                $mongo_obj = new FMongoManager();
                $mongo_obj->manager = $mongo_manager;

                self::$_servers[$server] = $mongo_obj;

                return $mongo_obj;
            } 
            else 
            {
                // 默认芒果服务器
                return self::instance();
            }
        }
    }

    /**
     * Create a new collection
     * 
     * @param Array.
     *  create: collection_name.
     *  autoIndexId, capped, size, max, flags
     */
    public function create_collection($config)
    {
        $this->_collections[$config['create']] = new MongoDB\Driver\Command($config);

        return $this->_collections[$config['create']];
    }

    /**
     * Get a collection
     */
    public function get_collection($collection_name)
    {
        return $this->_collections[$collection_name];
    }

    /**
     * Get Mongo Write Bulk
     */
    public function get_bulk($config = null)
    {
        $config = is_null($config) ? array('ordered' => true) : $config;
        return new MongoDB\Driver\BulkWrite($config);
    }

    /**
     * Set concern
     */
    public function concern($w = null, $timeout = 1000)
    {
        if (is_null($w))
        {
            $w = MongoDB\Driver\WriteConcern::MAJORITY;
        }

        $writeConcern = new MongoDB\Driver\WriteConcern($w, $timeout);

        $this->_writeConcern = $writeConcern;
    }

    /**
     * write 
     */
    public function write($collection_name, $bulk)
    {
        if (is_null($this->_writeConcern))
        {
            $this->concern();
        }

        $result = $this->manager->executeBulkWrite($collection_name, $bulk);

        return $result;
    }

    /**
     * Command
     */
    public function execute($db_name, $cmd)
    {
        $cursor = $this->manager->executeCommand($db_name, new MongoDB\Driver\Command($cmd));
        
        return $cursor->toArray();
    }

    /**
     * quick select
     */
    public function select($db_name, $collection_name, $filter = [], $options = [])
    {
        $query = new MongoDB\Driver\Query($filter, $options);
        $cursor = $this->manager->executeQuery($db_name . "." . $collection_name, $query)->toArray();

        if (empty($cursor))
        {
            return false;
        }
        else
        {
            return $cursor;
        }
    }
}
