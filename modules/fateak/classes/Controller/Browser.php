<?php
/**
 * For CKEditor and other browser function
 *
 * @author Fateak - Rollo
 */
class Controller_Browser extends Controller
{
    /**
     * Browser specific directory to find image
     *
     * @internal fateak-action-acl
     */
    public function action_images()
    {
        ACL::required('controller_browser_action_index');

        $user = User::active_user();

        if ($user->id > 0)
        {
            $root = APPPATH . 'media' . DS . 'users' . DS . User::get_group($user->id) . DS . $user->id;
        }
        else
        {
            $root = APPPATH . 'media';
        }

        $tree = $this->tree($root);
        $menu = $this->ul($tree);

        $view = View::factory('browser/image')
            ->set('root', Assets::path2url($root))
            ->set('tree', $tree)
            ->set('menu', $menu);

        $this->response->body($view);
    }

    private function tree($dir)
    {
        $node = array();
        $dir_handler = opendir($dir);

        while (($file = readdir($dir_handler))) 
        {
            if (substr($file, 0, 1) != '.' && $file != '..') 
            {
                $child = $dir . DS . $file;

                if (is_dir($child)) 
                {
                    $node[$file] = $this->tree($child); 
                } 
                else 
                {
                    $node[] = $file;
                }
            }
        }

        return $node;
    }

    private function ul($nodes, $dir = "")
    {
        $output = "<ul src='".$dir."' style='display:none'>";

        if ($dir == "") 
        {
            $output = "<span id='rooter' class='folder' src=''>"
                . "<i class='glyphicon glyphicon-folder-open'></i> ".__('Root Directory')."</span>" . $output;
        }

        foreach ($nodes as $k => $node) 
        {
            if (is_array($node)) 
            {
                $current_dir = $dir . "/" . $k;
                $output .= "<li name='".$k."'><i class='glyphicon glyphicon-folder-close'></i> ";
                $output .= "<span class='folder' src='" . $current_dir . "'>".$k."</span>" . $this->ul($node, $current_dir);
            } 
            else 
            {
                $ext = File::getExt($node);
                if (preg_match('/^(?:jpe?g|png|[gt]if|bmp)$/', $ext)) 
                {
                    $output .= "<li class='file' src='" . $dir . '/' . $node . "' name='".substr($node, 13)."'>";
                    $output .= "<i class='glyphicon glyphicon-picture'></i> ";
                    $output .= substr($node, 13);
                } 
                else 
                {
                    $output .= "<li class='file' src='" . $dir . '/' . $node . "' name='". $node ."'>";
                    $output .= "<i class='glyphicon glyphicon-file'></i> ";
                    $output .= $node;
                }
            }
            $output .= "</li>";
        }
        $output .= "</ul>";
        
        return $output;
    }
}
