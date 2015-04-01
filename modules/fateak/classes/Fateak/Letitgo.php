<?php
/**
 * For Task
 * Fateak - Rollo
 */
class Fateak_Letitgo
{

    /**
     * task: 大小写敏感
     */
    public static function execute($task, $params)
    {
        $tasker = DOCROOT . 'index.php';

        $php_config = Kohana::$config->load('system');

        $php_command = $php_config['php_command'];

        $query = "";
        foreach ($params as $k => $v) {
            $query .= "--" . $k . "=" . $v . " ";
        }

        $command = "{$php_command} {$tasker} --task={$task} {$query}&";

        pclose(popen($command, 'r'));

    }
}
