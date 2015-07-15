<?php
/**
 * Fateak 
 * System optimizer
 *
 * @uses Profiler
 * @author Rollo
 */
class Controller_Optimizer extends Controller_Land
{
    /**
     * Before every action
     */
    public function before()
    {
        ACL::required('super adam');
    }

    /**
     * index Page
     */
    public function index()
    {

    }
}
