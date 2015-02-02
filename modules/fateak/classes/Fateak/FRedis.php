<?php
/**
 * Rely on phpredis 2.2.7
 * FRedis = Redis in Fateak
 */
class Fateak_FRedis
{
    /**
     * An array store all redis servers
     * All available servers should be config in config/database.php
     *
     * 'redis_default' => array
     * (
     *     'host' => '127.0.0.1',
     *     'port' => '6379',
     * ),
     */
    protected static $_servers = array();

    /**
     * Singleton, Redis connection
     */
    public static function instance($server = 'default', $config = array())
    {
        if (isset(self::$_servers[$server])) {

            return self::$_servers[$server];

        } else {

            $server_name = 'redis_' . $server;
            $database_config = Kohana::$config->load('database');

            if (isset($database_config[$server_name])) {

                $server_config = $database_config[$server_name];

                $redis = new Redis();
                $redis->connect($server_config['host'], $server_config['port']);

                self::$_servers[$server] = $redis;

                return $redis;

            } else {

                return self::instance();

            }
        }
    }

    /**
     * Watch and do transaction.processing increase or decrease.
     */
    public static function plus($key, $delta, $server = 'default')
    {
        $redis = self::instance($server);

        do
        {
            $redis->watch($key);

            $value = $redis->get($key);

            $value += $delta;

            $redis->multi();

            $redis->set($key, $value);
    
            $flag = $redis->exec();

        } while (! $flag);

    }
}
