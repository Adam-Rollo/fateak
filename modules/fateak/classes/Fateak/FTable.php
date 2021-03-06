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

        foreach ($params['keyword'] as $k_offset => $keyword)
        {
            if ($keyword != '')
            {
                $keytype_type = $model->column_info($params['keytype'][$k_offset]);

                if ($keytype_type['type'] == 'string' && $options['fuzzy'])
                {
                    // if processing become slow. delete "%" and use '='.
                    $model->where($params['keytype'][$k_offset], 'LIKE', '%'.$keyword.'%');
                }
                else
                {
                    $model->where($params['keytype'][$k_offset], '=', $keyword);
                }
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
        if (empty($btns) && empty($properties) && empty($scripts))
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
                if (is_array($btn))
                {
                    $filter_callback = $btn['callback'];
                    $filter_params = $btn['params'];

                    foreach ($filter_params as $filter_k => $filter_v)
                    {
                        preg_match_all('/\[:([a-zA-Z0-9_]+):\]/', $filter_v, $matches);

                        foreach ($matches[1] as $k => $column)
                        {
                            if (isset($row[$column]))
                            {
                                $filter_params[$filter_k] = str_replace($matches[0][$k], $row[$column], $filter_v);
                            }
                        }
                    }

                    if (! $filter_callback($filter_params))
                    {
                        $row[$bn] = '';
                        continue;
                    }

                    $btn = $btn['text'];
                }

                preg_match_all('/\[:([a-zA-Z0-9_:]+):\]/', $btn, $matches);

                foreach ($matches[1] as $k => $column)
                {
                    if (strstr($column, ':'))
                    {
                        $var_iterator = explode(':', $column);
                        $value = & $row[$var_iterator[0]];
                        unset($var_iterator[0]);

                        foreach($var_iterator as $i)
                        {
                            if (isset($value[$i]))
                            {
                                $value = & $value[$i];
                            }
                            else
                            {
                                break;
                            }
                        }

                        $btn = str_replace($matches[0][$k], $value, $btn);
                        $row[$bn] = $btn;
                    }
                    else
                    {
                        if (isset($row[$column]))
                        {
                            $btn = str_replace($matches[0][$k], $row[$column], $btn);
                            $row[$bn] = $btn;
                        }
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
                    if (is_array($script_arg))
                    {
                        continue;
                    }

                    if (preg_match('/\[:([a-zA-Z0-9_:]+):\]/', $script_arg, $matches))
                    {
                        if (strstr($matches[1], ':'))
                        {
                            $var_iterator = explode(':', $matches[1]);
                            $value = & $row[$var_iterator[0]];
                            unset($var_iterator[0]);

                            foreach($var_iterator as $k)
                            {
                                $value = & $value[$k];
                            }

                            $script_args[$arg_name] = str_replace($matches[0], $value, $script_arg);

                            //$value = call_user_func_array(array($script_class, $script_function), $script_args);
                        }
                        else
                        {
                            $script_args[$arg_name] = str_replace($matches[0], $row[$matches[1]], $script_arg);
                        }

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
