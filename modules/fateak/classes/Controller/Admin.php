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
    public $template = null;

    /**
     * Before function
     */
    public function before()
    {
        $controller = strtolower($this->request->controller());
        $action = $this->request->action();

        if ($action === 'login')
        {
            $this->template = 'layout/login';
        }
        else
        {
            $this->template = 'layout/admin';
            // Set css in head
            Assets::add_css('bootstrap', 'assets/css/bootstrap.css', -10);
            // Set js in head
            Assets::add_head_js('jquery', 'assets/js/jquery-1.11.2.min.js', -10);
            Assets::add_head_js('bootstrap', 'assets/js/bootstrap.min.js', -5);
            // set js in body
            Assets::add_body_js('fateak', 'assets/js/fateak.js', 0);
        }

        parent::before();

        // Load configuration
        $admin_config = Kohana::$config->load('admin');

        // Set title
        $this->template->title = $admin_config->get('title');

        // ACL
        $user = User::active_user(array());
        if ($action !== 'login')
        {
            if (is_null($user))
            {
                Message::set(Message::WARN, __('You must login first.'));
                HTTP::redirect($controller . '/login');
            }
            else 
            {
                if (! $user->is_role('login'))
                {
                    Message::alert(__('You haven\'t permission to login.'));
                    HTTP::redirect($controller . '/login');
                }
            }
        }
        else
        {
            if (! is_null($user))
            {
                HTTP::redirect( $controller );
            }    
        }

        // View variable
        $this->template->controller = $controller;
    }

    abstract public function action_index();

    abstract public function action_login();

    protected function _load_iframe_tools()
    {
        Assets::add_body_js('fmenu', 'assets/js/menu.js', 5); 

    }

    public function action_logout()
    {
        $controller = $this->request->controller();

	// Sign out the user
	Auth::instance()->logout();

	// Redirect to the user account and then the signin page if logout worked as expected
        HTTP::redirect(strtolower($controller).'/login');
    }

}
