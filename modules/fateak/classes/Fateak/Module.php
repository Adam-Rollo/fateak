<?php
/**
 * Module class for module observer
 * Fateak - Rollo
 */
class Fateak_Module
{
    /**
     * Get all active modules
     */
    public static function modules($modules = NULL)
    {
        return Kohana::modules($modules);
    } 

    /**
     * Call to execute a Module action
     * @param string The name of the action to execute
     * @param mixed The value to action.
     */
    public static function action($action, $return = array())
    {
        $oringial_args = func_get_args();
        if (count($oringial_args) == 1)
        {
            $oringial_args[] = array();
        }
        list( $action, $return ) = $oringial_args;

        $function = str_replace(".", "_", $action);
        $filterargs = array_slice(func_get_args(), 2);

        foreach (Kohana::modules() as $name => $module)
        {
            $class = ucfirst($name).'_Action';
            $args = $filterargs;
            array_unshift($args, $return);

            if (is_callable(array($class, $function)))
            {
                try
                {
                    $return = call_user_func_array(array($class, $function), $args);
                }
                catch(Exception $e){}
            }
        }

        return $return;
    }
}
