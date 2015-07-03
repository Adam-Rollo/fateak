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
        $this->_model = $model->where('scp', '=', $scp_id)->order_by('lft', 'ASC');
    }

    public function data()
    {
        return $this->_model->find_all()->as_array();
    }
}
