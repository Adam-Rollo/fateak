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
}
