<?php
/**
 * Fateak - Rollo
 * combine usage with fateak.table.js
 */
class Fateak_FTable
{
    /**
     * @var total number
     */
    protected $_total_numbers;

    /**
     * @var data
     */
    protected $_data;

    /**
     * @var use script
     */
    protected $_is_script;

    /**
     * load data from database and $_GET
     */
    public function __construct(Model $model, $params, $options = array('fuzzy' => TRUE, 'script' => FALSE))
    {
        $this->_is_script = isset($options['script']) ? $options['script'] : FALSE;


        $offset = ($params['page'] - 1) * $params['rowsPerPage'];
        if ($params['keyword'])
        {
            if ($options['fuzzy'])
            {
                // if processing become slow. delete "%" and use '='.
                $model->where($params['keytype'], 'LIKE', '%'.$params['keyword'].'%');
            }
            else
            {
                $model->where($params['keytype'], '=', $params['keyword']);
            }
        }
        $this->_total_numbers = $model->reset(FALSE)->count_all();

        if ($this->_is_script)
        {
            $this->_data = $model->load_ftable_script($params, $options['script_columns']);
        }
        else
        {
            if ($params['sort'])
            {
                $model->order_by($params['sort'], $params['order']);
            }

            $this->_data = $model->offset($offset)
                ->limit($params['rowsPerPage'])
                ->find_all();
        }
    }

    /**
     * count all
     */
    public function total()
    {
        return $this->_total_numbers;
    }

    /**
     * get data
     * @param btns: Button with placeholder
     * @param properties: process relationship: has_many
     */
    public function data($btns = array(), $properties = array(), $scripts = array())
    {
        if (empty($btns) && empty($properties) && empty($script))
        {
            return $this->_data->as_array();
        }

        $result = array();

        foreach ($this->_data as $model)
        {
            $row = array();

            foreach ($properties as $property)
            {
                if ($this->_is_script)
                {
                    $row[$property] = JSON::decode($model[$property]);
                }
                else
                {
                    $row[$property] = $model->$property->find_all()->as_array();
                }
            }

            if (is_array($model))
            {
                $row = array_merge($model, $row);
            }
            else
            {
                $row = array_merge($model->as_array(), $row);
            }

            foreach ($btns as $bn => $btn)
            {
                preg_match_all('/\[:([a-zA-Z0-9_]+):\]/', $btn, $matches);

                foreach ($matches[1] as $k => $column)
                {
                    if (isset($row[$column]))
                    {
                        $btn = str_replace($matches[0][$k], $row[$column], $btn);
                        $row[$bn] = $btn;
                    }
                }
            }

            foreach ($scripts as $scolumn => $script_group)
            {
                if (is_array($script_group['function']))
                {
                    $script_class = $script_group['function']['object'];
                    $script_function = $script_group['function']['method'];
                }
                else
                {
                    list($script_class, $script_function) = explode('::', $script_group['function']);
                }

                $script_args = (isset($script_group['params']) && ! empty($script_group['params'])) ? $script_group['params'] : array();

                foreach ($script_args as $arg_name => $script_arg)
                {
                    if (preg_match('/\[:([a-zA-Z0-9_]+):\]/', $script_arg, $matches))
                    {
                        $script_args[$arg_name] = str_replace($matches[0], $row[$matches[1]], $script_arg);
                    }
                }

                $row[$scolumn] = call_user_func_array(array($script_class, $script_function), $script_args);
            }

            $result[] = $row;
        }

        return $result;
    }

    /**
     * process "has many"
     */
    public function has_many($column)
    {

        foreach ($this->_data as $model)
        {
            $many_models = $model->$column->find_all()->as_array();
            $model_array = $model->as_array();
        }
    }
}
