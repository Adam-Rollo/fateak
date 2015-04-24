<?php
/**
 * User API and Auth API
 * 关于用户的所有网络服务都在最基础的模块之中，它包含了认证和信息提取
 *
 * @author Fateak - Rollo
 */
class Webservice_User extends Webservice
{
    /**
     * 菜单提取，包括菜单的标题，名称，链接，图标以及上下级关ok
     * 返回的数组键名以及意义如下
     *                                     
     * [title] => Tera                  菜单的标题
     * [url] => blog/5                  菜单所关联的URL
     * [children] => array(...)         菜单的子菜单，子菜单的结构和此一样
     * [access] => 1                    是否可以访问
     * [descp] => all map no lock       菜单的描述
     * [params] =>                      菜单的参数
     * [image] => fa-align-left         菜单的图标，需要在class为fa的标签中使用，将这个类添加到fa类后面
     *
     * @param menu: 菜单名称。菜单名称是菜单标题的小写化，并且将空格改成了连字符-。
     *
     * @return Array: 返回了菜单的标题链接等内容
     */
    public function get_menus ($params)
    {
        $this->check_params($params, 'menu');

        $menu = Menu::items($params['menu']);
        return $menu->get_items();
    }
}
