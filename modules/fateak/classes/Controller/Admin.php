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
        $action = $this->request->action();
        if ($action === 'login')
        {
            $this->template = 'layout/login';
        }
        else
        {
            // Set js in head
            Assets::add_head_js('jquery', 'assets/js/jquery-1.11.2.min.js', -10);
            Assets::add_head_js('bootstrap', 'assets/js/bootstrap.min.js', -5);
        }

        parent::before();

        // Load configuration
        $admin_config = Kohana::$config->load('admin');

        // Set title
        $this->template->title = $admin_config->get('title');

        // ACL
        if ($action !== 'login')
        {
            $user = User::active_user();
            if (is_null($user))
            {
                Message::set(Message::WARN, '123');
                $controller = $this->request->controller();
                HTTP::redirect(strtolower($controller).'/login');
            }
            else 
            {
                if (! User::is_rolo('login'))
                {
                    Message::set('123');
                    HTTP::redirect();
                }
            }
        }

    }

    abstract public function action_index();

    abstract public function action_login();

    protected function _load_iframe_tools()
    {
        Assets::add_body_js('fmenu', 'assets/js/menu.js', 5); 

    }


}
