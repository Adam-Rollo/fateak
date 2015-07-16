<?php
/**
 * Fateak 
 * System optimizer
 *
 * @uses Profiler
 * @author Rollo
 */
class Controller_Optimizer extends Controller_Fate
{
    /**
     * Before every action
     */
    public function before()
    {
        ACL::required('super adam');

        parent::before();
    }

    /**
     * index Page
     */
    public function action_index()
    {
        // Page settings
        $this->title = "时间与记忆 ~ Time and Memory";
        $this->breadcrumb->addItem('Time and Memory');

        // init
        $redis = FRedis::instance();
        $current = $this->request->query('uri');

        // Get uri
        $uris = $redis->sMembers('foptimizer.uri');
        asort($uris);
        $routes = array('' => '请选择动作');
        foreach ($uris as $i => $uri)
        {
            $routes[$uri] = $uri;
        }
        unset($uris);

        $uri_reports = array();
        if ($current)
        {
            // Get items list
            $items = $redis->sMembers('foptimizer.uri:' . $current);

            foreach ($items as $item)
            {
                $record = $redis->hGetall('foptimizer.info:' . $item);
                $group_name = substr($item, 0, strpos($item, ':'));
                $item_name = substr($item, strpos($item, ':') + 1);
                $uri_reports[$group_name][$item_name] = $record;
            }
        }


        // View
        $view = View::factory('optimizer/index')
            ->set('action', URL::base() . 'optimizer/index')
            ->set('current', $current)
            ->set('uri_reports', $uri_reports)
            ->set('routes', $routes);
        
        $this->response->body($view);
    }

    public function action_delete()
    {
        $redis = FRedis::instance();

        $keys = $redis->keys('foptimizer*');

        $count = 0;

        foreach ($keys as $key)
        {
            $count += $redis->del($key);
        }

        $this->ajax->data("您已经成功删除" . $count . "条记录");
        $this->response->body($this->ajax->build_result());
    }
}
