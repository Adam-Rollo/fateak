<?php
/**
 * Fateak - Rollo
 */
class Fateak_FTree
{
    /**
     * Model 
     */
    protected $_model;

    /**
     *  Construct function
     */
    public function __construct(Model $model, $scp_id)
    {
        $this->_model = $model->where('scp', '=', $scp_id)->where('active', '=', 1)->order_by('lft', 'ASC');
    }

    public function data()
    {
        return $this->_model->find_all()->as_array();
    }

    public static function make_array($categories)
    {
        $result = array();
        $stack = array();
        $i = 0;
        
        foreach ($categories as $category)
        {
            while ((count($stack) > 0) && ($stack[count($stack)-1]['rgt'] < $category['rgt']))
            {
                array_pop($stack);
            }

            if (count($stack) > 0)
            {
                $stack[count($stack) - 1]['children'][$i] = $category;
                $a = & $stack[count($stack) - 1]['children'][$i];
            }
            else
            {
                $result['tree'] = $category;
                $a = & $result['tree'];
            }

            $stack[] = $a;
            $i++;
        }

        return $result;

    }

    public static function ul($nodes, $options = array())
    {
        $html = "<ul>";

        foreach ($nodes['children'] as $child)
        {
            $attr = array('cid' => $child['id']);
            $title = $child['title'];

            foreach ($options as $option => $callback)
            {
                if (isset($child[$option]))
                {
                    $attr[$option] = $child[$option];
                    $title .= $callback($child[$option]);
                }
            }

            $attr_json = JSON::encode($attr);

            $html .= "<li data-jstree='" . $attr_json . "'>" . $title;

            if (isset($child['children']))
            {
                $html .= self::ul($child, $options);
            }

            $html .= "</li>";
        }

        $html .= "</ul>";

        return $html;
    }
}
