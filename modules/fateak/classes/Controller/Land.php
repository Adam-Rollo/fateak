<?php
/**
 * Template "land" has whole HTML tags: <body> <head> <html>.
 * It's more complicate than island controller 
 * When it's used to be front page, It would be cached by Nginx (Varnish and esi would be used).
 * When user's access is forbidden, it would return 403 error rather than empty string.
 * Usage: Land is always used in URL Request.
 * Ajax: Processing ajax is available.
 *
 * @author Rollo - Fateak
 */
abstract class Controller_Land extends Controller_Template
{
    /**
     * @var  View  page template
     */
    public $template = 'layout/land';

    public function before()
    {
        parent::before();
    }

    public function after()
    {
        $this->template->content = $this->response->body();

        parent::after();
    }

    abstract public function action_index();
}
