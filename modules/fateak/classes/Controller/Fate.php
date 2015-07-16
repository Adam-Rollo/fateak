<?php

class Controller_Fate extends Controller_Land
{
    /**
     * @var View Template page
     */
    public $template = 'layout/fate';

    /**
     * @var BreadCrumb 
     */
    public $breadcrumb;

    public function before()
    {
        parent::before();

        if ($this->is_ajax)
        {
            return;
        }

        $user = User::active_user(array());

        if (is_null($user))
        {
            throw HTTP_Exception::factory(403, 'Unauthorized attempt to access action Fate');
        }

        // Breadcrumb
        $this->breadcrumb = Breadcrumb::instance();
        $this->breadcrumb->addItem('Home', 'fate/index');
        $this->template->bind('breadcrumb', $this->breadcrumb);

        // Assets
        // Set css in head
        Assets::add_css('bootstrap', 'assets/css/bootstrap.css', -10);
        Assets::add_css('savant', 'assets/css/savant.css', 1);
        // Set js in head
        Assets::add_head_js('jquery', 'assets/js/jquery-1.11.2.min.js', -10);
        Assets::add_head_js('bootstrap', 'assets/js/bootstrap.min.js', -5);
        // set js in body
        Assets::add_body_js('fateak', 'assets/js/fateak.js', 0);

        // Default Title
        $controller = $this->request->controller();
        $this->title = $controller;
    }

    public function action_index()
    {
        $this->response->body('You need rewrite cusmiuze hone page.');
    }

}
