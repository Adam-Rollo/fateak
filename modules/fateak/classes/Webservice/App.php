<?php
/**
 * User API and Auth API
 * 关于用户的所有网络服务都在最基础的模块之中，它包含了认证和信息提取
 *
 * @author Fateak - Rollo
 */
class Webservice_App extends Webservice
{
    /**
     * 得到用户权限
     */
    protected function get_permissions($user_id)
    {
        User::set_user($user_id);
    } 

    /**
     * Get HTML
     */
    public function html($params)
    {
        $this->check_params($params, 'url');

        if (strstr($params['url'], 'http'))
        {
            $content = CURL::get($params['url']);
        }
        else
        {
            $url = URL::base() . trim($params['url'], '/');
            $content = CURL::get($url);
        }

        return $content;

    }

    /**
     * Synchronization Queue
     */
    public static function synchronization($model, $user_id)
    {
        $redis = FRedis::instance();

        foreach($model->changed() as $column)
        {
            $random_salt = $model->id . Text::random('alpha', 6);
            $random_key = "toapp:" . $model->object_name() . ":change:" . $random_salt;

            while ($redis->hGet($random_key,'id'))
            {
                $random_salt = $customer->id . Text::random('alpha', 6);
                $random_key = "toapp:" . $model->object_name() . ":change:" . $random_salt;
            }

            $redis->zAdd("toapp:" . $model->object_name() . ":queue:" . $user_id, time(), $random_salt);

            $redis->hMSet($random_key, array('id' => $model->id, 'column' => $column, 'value' => $model->$column));
        }
    }

    /**
     * Synchronization Queue load
     */
    public static function synchronization_load($table, $user_id, $timestamp = 0)
    {
        $redis = FRedis::instance();

        $changes = $redis->zRangeByScore("toapp:{$table}:queue:" . $user_id , $timestamp, time());

        foreach ($changes as $k => $change)
        {
            $changes[$k] = $redis->hMGet("toapp:customer:change:" . $change, array('id', 'column', 'value'));
        }

        return $changes;

    }
}
