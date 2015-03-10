<?php
/**
 * Fateak Action. Called by Module::action
 * The first parameter of functions must be $return
 */
class Fateak_Fateak_Action
{

    public static function before_controller($return)
    {
        try
        {
            I18n::initialize();
        } 
        catch (Exception $e)
        {
            echo $e->getLine . " " . $e->getMessage();    
        }
    }

}
