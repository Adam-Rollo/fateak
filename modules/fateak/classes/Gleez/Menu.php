<?php
/**
 * Core Menu Class
 *
 * This class can be used to easily build out a menu in the form
 * of an unordered list. You can add any attributes you'd like to
 * the list, and each list item has special classes to help you style it.
 *
 * @package    Gleez\Menu
 * @author     Gleez Team
 * @author     1.2.0
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    http://gleezcms.org/license  Gleez CMS License
 */
class Gleez_Menu {

	/**
	 * Associative array of list items
	 * @var array
	 */
	protected $items = array();

	/**
	 * Associative array of attributes for list
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Creates and returns a new menu object
	 *
	 * @param   array  $items  Array of list items (instead of using add() method) [Optional]
	 * @return  Menu
	 */
	public static function factory(array $items = NULL)
	{
		return new static($items);
	}

	/**
	 * Constructor, globally sets $items array
	 *
	 * @param   array  $items  Array of list items (instead of using add() method) [Optional]
	 */
	public function __construct(array $items = NULL)
	{
		$this->items   = $items;
	}

	/**
	 * Add's a new list item to the menu. if parent_id is passed will add as child
	 *
	 * @param   string  $id         Unique id
	 * @param   string  $title      Title of link
	 * @param   string  $url        URL (address) of link
	 * @param   string  $descp      Additional text of link [Optional]
	 * @param   array   $params     Params of the item to handle logic [Optional]
	 * @param   string  $image      Menu icon [Optional]
	 * @param   string  $parent_id  Parent Id of the link [Optional]
	 * @param   Menu    $children   Instance of class that contain children [Optional]
	 * @return  Menu
	 */
	public function add($id, $title, $url, $descp = '', array $params = NULL, $image = NULL, $parent_id = NULL, Menu $children = NULL)
	{
		if( $parent_id )
		{
			$this->items = static::add_child($parent_id, $this->items, $id, $title, $url, $descp, $params, $image, $children);
		}
		else
		{
			$this->items[$id] = array
			(
				'title'    => $title,
				'url'      => $url,
				'children' => ($children instanceof Menu) ? $children->get_items() : NULL,
				'access'   => TRUE, // @todo
				'descp'	   => $descp,
				'params'   => $params,
				'image'    => $image
			);
		}

		return $this;
	}

	/**
	 * Remove an item from the menu
	 *
	 * @param   string   $target_id  Id of link
	 * @param   boolean  $parent_id  Parent Id of link [Optional]
	 * @return  Menu
	 */
	public function remove($target_id, $parent_id = FALSE)
	{
		if ($parent_id)
		{
			$this->items = static::remove_child($target_id, $this->items);
		}
		else if (isset( $this->items[$target_id]))
		{
			unset($this->items[$target_id]);
		}

		return $this;
	}

	/**
	 * Change an item title of this menu
	 *
	 * @param   string   $target_id  Id of link item
	 * @param   string   $title      New Title for the item
	 * @param   boolean  $parent_id  Parent Id of link [Optional]
	 * @return  Menu
	 */
	public function set_title($target_id, $title, $parent_id = FALSE)
	{
		if ( $parent_id )
		{
			$this->items = static::change_title_url($target_id, $this->items, $title);
		}
		else if ( isset( $this->items[$target_id] ) )
		{
			$this->items[$target_id]['title'] = (string)$title;
		}

		return $this;
	}

	/**
	 * Change an item url of this menu
	 *
	 * @param   string   $target_id  Id of link
	 * @param   string   $url      	 New url of the item
	 * @param   boolean  $parent_id  Parent Id of link [Optional]
	 * @return  MENU
	 */
	public function set_url($target_id, $url, $parent_id = FALSE)
	{
		if ( $parent_id )
		{
			$this->items = static::change_title_url($target_id, $this->items, $url, 'url');
		}
		else if ( isset( $this->items[$target_id] ) )
		{
			$this->items[$target_id]['url'] = (string)$url;
		}

		return $this;
	}

	/**
	 * Renders the HTML output for the menu
	 *
	 * @param   array   $attributes  Associative array of html attributes [Optional]
	 * @param   array   $items       The parent item's array, only used internally [Optional]
	 *
	 * @return  string  HTML unordered list
	 */
	public function render(array $attributes = NULL, array $items = NULL, array $options = array())
	{
		static $i;

		$items = empty($items) ? $this->items : $items;
		$attributes = empty($attributes) ? $this->attributes : $attributes;

		if (empty($items)) return;

		$i++;

		//This attribute detects we're in nav or widget for styling
		$is_widget	= isset($attributes['widget']);
		if($is_widget) unset($attributes['widget']);

		$attributes['class'] = empty($attributes['class']) ? 'level-'.$i : $attributes['class'].' level-'.$i;
		$menu = '<ul'.HTML::attributes($attributes).'>';
		$num_items = count($items);
		$_i = 1;

		foreach ($items as $key => $item)
		{
			$has_children = count($item['children']);
                        // Fateak plus
			$classes = array('fateak-common-menu');
			$attributes  = array();
			$caret = NULL;

			// Add first, last and parent classes to the list of links to help out themers.
			if ($_i == 1)          $classes[] = 'first';
			if ($_i == $num_items) $classes[] = 'last';
			if ( $has_children )
			{
                                // Fateak minus
				// $classes[] = 'parent dropdown';
				$classes[] = 'fateak-parent-menu';
				$attributes[] = 'dropdown-toggle collapsed';
				if($i == 2) $classes[] = 'dropdown-submenu';
			}

			// Check if the menu item URI is or contains the current URI
			if (URL::is_active($item['url']) && ! in_array('ajax', $options))
			{
				$classes[] = 'active';
				$attributes[] = 'active';
			}

			if ( ! empty($classes))
			{
				$classes = HTML::attributes(array('class' => implode(' ', $classes)));
			}

			if ( ! empty($attributes))
			{
				$attributes = array('class' => implode(' ', $attributes));
			}

			$id = HTML::attributes(array('id' => 'menu-'.$key));

			//Twitter bootstrap attributes
			if ($has_children)
			{
                                // fateak minus
				// $attributes['data-toggle'] = 'dropdown';
				$attributes['data-toggle'] = '';
				$item['url'] = '#';
				$caret = ($i == 2) ? '': '<b class="caret"></b>';
                                // fateak minus
				// $class = 'dropdown-menu';
				$class = '';
			}

			//Twitter bootstrap use collapse for widget menu chlidren
			if($has_children && $is_widget)
			{
				$attributes['data-toggle'] = 'collapse';
				$attributes['data-parent'] = '#menu-'.$key;
				$item['url'] ='#collapse-'.$key;
				$class = 'panel-collapse collapse';
				$caret = ($i == 2) ? '': '<i class="fa fa-chevron-down submenu"></i>';
			}

			//set title
			$title = (isset($item['image'])) ? '<i class="fa fa-fw '.$item['image'].'"></i>' : '';
			$title .= '<span>'.HTML::chars($item['title']).$caret.'</span>';

			if($item['descp'] && !empty($item['descp']))
			{
				$title .= '<span class="menu-descp">' . Text::plain($item['descp']) . '</span>';
			}

			$menu .= '<li'.$classes.'  ' .$id. '>'.HTML::anchor($item['url'], $title, $attributes);
			$menu .= '</li> ';

			if ( $has_children )
			{
				$menu .= "<li id='children-menu-" . $key . "' class='fateak-children-menu'>" 
                                    . $this->render(array('class' => $class, 'id' => 'collapse-'.$key),  $item['children']) 
                                    . "</li>";
			}

			$_i++;
		}

		$menu .= '</ul>';
		$i--;

		return $menu;
	}

	/**
	 * Renders the HTML output for menu without any attributes or active item
	 *
	 * @return   string
	 */
	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}

	/**
	 * Nicely outputs contents of $this->items for debugging info
	 *
	 * @return   string
	 */
	public function debug()
	{
		return Debug::vars($this->items);
	}

	/**
	 * Nicely outputs contents of $this->items as array
	 *
	 * @return array
	 */
	public function get_items()
	{
		return $this->items;
	}

	/**
	 * Static method to display menu based on its unique name
	 *
	 * @param   string   $name The name of the menu
	 * @param   array    $attr The css class or id array [Optional]
	 * @return  string
	 */
	public static function links($name, $attr = array('class' =>'menus'), $options = array())
	{
		$cache = Cache::instance();
                $cache_key = 'Menu::' . $name;

		if ( ! $items = $cache->get($cache_key))
		{
			$_menu = ORM::factory('Menu')->where('name', '=', (string)$name)->find()->as_array();
			if ( ! $_menu) return;

			$ritems = ORM::factory('Menu')
				->where('lft', '>', $_menu['lft'])
				->where('rgt', '<', $_menu['rgt'])
				->where('scp', '=', $_menu['scp'])
				->where('active', '=', 1)
				->order_by('lft', 'ASC')
				->find_all();

			$items = array();
			foreach($ritems as $item)
			{
				$items[] = $item->as_array();
			}

			if (empty($items)) return;

			// set the cache for performance in production
			if (Kohana::$environment === Kohana::PRODUCTION)
			{
				$cache->set($cache_key, $items, Date::DAY);
			}
		}

		// Initiate Menu Object
		$menu = static::factory();

		// Start with an empty $right stack
		$stack = array();

                // Fateak ACL Menu
                $ignore_parent_stack = array();

		foreach( $items as &$item)
		{
                        // Fateak ACL Menu
                        if (in_array('acl', $options))
                        {
                                $ignore_parent_count = count($ignore_parent_stack);
                                if ( $ignore_parent_count && $item['pid'] == $ignore_parent_stack[$ignore_parent_count - 1] )
                                {
                                        array_push($ignore_parent_stack, $item['id']);
                                        continue;
                                }
                                else
                                {
                                       array_pop($ignore_parent_stack);
                                }

                                $menu_key = 'menu-' . $item['name'];
                                
                                if (! ACL::check($menu_key))
                                {
                                        array_push($ignore_parent_stack, $item['id']);
                                        continue; 
                                }
                        }

			// check if we should remove a node from the stack
			while(count($stack) > 0 AND $stack[count($stack) - 1]['rgt'] < $item['rgt'])
			{
				array_pop($stack);
			}

			if(count($stack) > 0)
			{
				$menu->add($item['name'], $item['title'], $item['url'], $item['descp'], $item['params'], $item['image'], $stack[count($stack) - 1]['name']);
			}
			else
			{
				$menu->add($item['name'], $item['title'], $item['url'], $item['descp'], $item['params'], $item['image']);
			}

			$stack[] = &$item;
		}

		// unset the stack array to freeup memory
		unset( $stack );

		return $menu->render( $attr, null, $options );
	}

	/**
	 * Static method to return menu object based on its unique name
	 *
	 * @param   string   $name The name of the menu
	 * @return  object   Menu
	 */
	public static function items($name, $options = array())
	{
		$cache = Cache::instance();

		if( ! $items = $cache->get($name) )
		{
			$_menu = ORM::factory('Menu')->where('name', '=', (string)$name)->find()->as_array();
			if( ! $_menu) return;

			$ritems = ORM::factory('Menu')
				->where('lft', '>', $_menu['lft'])
				->where('rgt', '<', $_menu['rgt'])
				->where('scp', '=', $_menu['scp'])
				->where('active', '=', 1)
				->order_by('lft', 'ASC')
				->find_all();

			$items = array();
			foreach($ritems as $item)
			{
				$items[] = $item->as_array();
			}

			if (empty($items)) return;

			// set the cache for performance in production
			if (Kohana::$environment === Kohana::PRODUCTION)
			{
				$cache->set($name, $items, Date::DAY);
			}
		}

		//Initiate Menu Object
		$menu = static::factory();

		// start with an empty $right stack
		$stack = array();

                // Fateak ACL Menu
                $ignore_parent_stack = array();

		foreach( $items as &$item)
		{
                        // Fateak ACL Menu
                        if (in_array('acl', $options))
                        {
                                $ignore_parent_count = count($ignore_parent_stack);
                                if ( $ignore_parent_count && $item['pid'] == $ignore_parent_stack[$ignore_parent_count - 1] )
                                {
                                        array_push($ignore_parent_stack, $item['id']);
                                        continue;
                                }
                                else
                                {
                                       array_pop($ignore_parent_stack);
                                }

                                $menu_key = 'menu-' . $item['name'];
                                
                                if (! ACL::check($menu_key))
                                {
                                        array_push($ignore_parent_stack, $item['id']);
                                        continue; 
                                }
                        }

			// check if we should remove a node from the stack
			while(count($stack) > 0 AND $stack[count($stack) - 1]['rgt'] < $item['rgt'])
			{
				array_pop($stack);
			}

			if(count($stack) > 0)
			{
				$menu->add($item['name'], $item['title'], $item['url'], $item['descp'], $item['params'], $item['image'], $stack[count($stack) - 1]['name']);
			}
			else
			{
				$menu->add($item['name'], $item['title'], $item['url'], $item['descp'], $item['params'], $item['image']);
			}

			$stack[] = &$item;
		}

		// unset the stack array to freeup memory
		unset( $stack );

		// Enable developers to override menu
		Module::action('menus_items', $menu);
		Module::action("menus_items_{$name}", $menu);

		return $menu;
	}

	/**
	 * Private method to change menu based on its unique name
	 *
	 * @param   string   $needle The name of the menu
	 * @param   array    $array  The array of items
	 * @param   string   $string The new value
	 * @param   string   $op     The action title/url to change [Optional]
	 *
	 * @return  array
	 */
	private static function change_title_url($needle, array $array, $string, $op = 'title')
	{
		foreach ($array as $key => $value)
		{
			# Check for val
			if ($key == $needle)
			{
				if($op == 'title') $array[$key]['title'] = (string)$string;
				if($op == 'url')   $array[$key]['url']   = (string)$string;

				return $array;
			}

			if (isset($value['children']))
			{
				$array[$key]['children'] = static::change_title_url($needle, $value['children'], $string, $op);
			}
		}

		return $array;
	}

	/**
	 * Private method to add menu based on its parent's unique name
	 *
	 * @param   string         $needle   The parent unique name of the menu
	 * @param   array          $array    The array of items
	 * @param   string         $id       The new id of menu
	 * @param   string         $title    The new title
	 * @param   string         $url      The new url
	 * @param   string|boolean $descp    The additional text of url [Optional]
	 * @param   array          $params   The new params [Optional]
	 * @param   string         $image    The image or icon of url [Optional]
	 * @param   Menu           $children The new children [Optional]
	 *
	 * @return  array
	 */
	private static function add_child($needle, array $array, $id, $title, $url, $descp = FALSE, array $params = NULL, $image = NULL, Menu $children = NULL)
	{
		foreach ($array as $key => $value)
		{
			if ($key == $needle)
			{
				$array[$key]['children'][$id] = array
				(
					'title'    => $title,
					'url'      => $url,
					'children' => ($children instanceof Menu) ? $children->get_items() : NULL,
					'access'   => TRUE, // @todo
					'descp'	   => $descp,
					'params'   => $params,
					'image'    => $image
				);

				return $array;
			}

			if (isset($value['children']))
			{
				$array[$key]['children'] = static::add_child($needle, $value['children'], $id, $title, $url, $descp, $params, $image, $children);
			}
		}

		return $array;
	}

	/**
	 * private method to remove a child menu based on its unique name
	 *
	 * @param   string   $needle The name of the menu
	 * @param   array    $array  The array of items
	 * @return  array
	 */
	private static function remove_child($needle, array $array)
	{
		foreach ($array as $key => $value)
		{
			if ($key == $needle)
			{
				unset($array[$key]);

				return $array;
			}

			if (isset($value['children']))
			{
				$array[$key]['children'] = static::remove_child($needle, $value['children']);
			}
		}

		return $array;
	}

        /**
         * Get root menu with ACL
         * Fateak - Rollo
         */
        public static function root_menus($options = array())
        {
                $roots = ORM::factory('Menu')
                        ->where('pid', '=', 0)
                        ->order_by('rgt', 'DESC')
                        ->find_all();

                $result = array();

                foreach ($roots as $root)
                {
                        $permmision = 'menu-' . $root->name;
                        if ( (! in_array('acl', $options)) || ACL::check($permmision) )
                        {
                                $result[] = $root; 
                        }
                }

                return $result;
        }

}
