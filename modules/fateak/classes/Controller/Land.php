<?php
/**
 * Template "land" has whole HTML tags: <body> <head> <html>. You must create it by yourself and assign to $template explicitly.
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
     * @var  View  page template. You'd better to rebuild it.
     */
    public $template = 'layout/land';

    /**
     * @var title
     */
    public $title = null;

    /**
     * @var Ajax
     */
    public $is_ajax = false;

    /**
     * @var FAjax object
     */
    public $ajax = null;

    public function before()
    {
        $this->is_ajax = $this->request->is_ajax();

        if ($this->is_ajax)
        {
            $this->ajax = new Fajax();
            $this->init_controller();
            return;
        }
        $this->title = 'Land';
        parent::before();
    }

    public function after()
    {
        if ($this->is_ajax)
        {
            return;
        }

        $this->template->content = $this->response->body();
        $this->template->title = $this->title;

        parent::after();
    }

    abstract public function action_index();
}
