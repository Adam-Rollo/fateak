<?php
/**
 * To hide url of fateak's administrator.
 * This class should be inherited
 * When you create a new admin controller, you must add the url to the file:robot.txt for safe reason. 
 * The best practise to protect admin directory, is never put the url in your homepage.
 *
 * @author Fateak - Rollo
 */
abstract class Controller_Admin extends Controller_Template
{
    /**
     * @var  View  page template
     */
    public $template = 'layout/admin';

    /**
     * Before function
     */
    public function before()
    {
        parent::before();

        // Load configuration
        $admin_config = Kohana::$config->load('admin');

        // Set title
        $this->template->title = $admin_config->get('title');

        // Set js in head
        Assets::add_head_js('jquery', 'assets/js/jquery-1.11.2.min.js', -10);
        Assets::add_head_js('bootstrap', 'assets/js/bootstrap.min.js', -5);

    }

    public function action_index()
    {
        Assets::add_body_js('fmenu', 'assets/js/menu.js', 5); 
    }


}
