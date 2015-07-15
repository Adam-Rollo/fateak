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
            // Internaitionlize site
            I18n::initialize();
        } 
        catch (Exception $e)
        {
            Log::debug($e->getFile() . " " . $e->getLine() . " " . $e->getMessage()); 
        }
    }

    public static function save_file($handler, $user_id, $file_src, $file_id, $file_name)
    {
        try
        {
            $file = ORM::factory('User_File');
            $file->id = $file_id;
            $file->user_id = $user_id;
            $file->name = $file_name;
            $file->path = $file_src;
            $file->save();
        }
        catch (Exception $e)
        {
            Log::debug($e->getMessage());
        }
    }

    public static function after_controller($errors)
    {
        try
        {
            $site_config = Kohana::$config->load('site');

            if ($site_config['optimizer_status'])
            {
                FOptimizer::save();
            }
        } 
        catch (Exception $e)
        {
            $errors[] = $e->getFile() . " " . $e->getLine() . " " . $e->getMessage(); 
        }

    }

}
