<?php
/**
 * Only way to upload file
 *
 * @author Fateak - Rollo
 */
class Controller_Upload extends Controller
{
    /**
     * Recieve Image and save
     *
     * @internal fateak-action-acl
     */
    public function action_index()
    {
        ACL::required('controller_upload_action_index');

        $upload_config = Kohana::$config->load('file');
        $user_id = $this->request->query('user');
        $user = User::active_user();

        if (is_null($user_id))
        {
            $user_id = $user->id;
        }
        else
        {
            if ($user->id != $user_id)
            {
                ACL::required('manage all files');
            }
        }

        $result = array();
        $errors = array();

        foreach ($_FILES as $name => $file)
        {
            $file_errors = array();

            if ( ! (Upload::not_empty($file) && Upload::valid($file)) )
            {
                $file_errors[] = __('Upload file error');
            }
            
            if ( ! Upload::type($file, $upload_config['upload_extension']) )
            {
                $file_errors[] = __('Uploaded file is illagal');
            }

            if ( ! Upload::size($file, $upload_config['upload_max_size']) )
            {
                $file_errors[] = __('Uploaded file is too large');
            }

            if ( ! Upload::dir_writable() )
            {
                $file_errors[] = __('Uploaded directory is not writable');
            }

            if (! empty($errors))
            {
                $errors[] = $file_errors;
                continue;
            }

            $media_path = APPPATH . 'media';
            $path = $this->request->query('path');
            $path = (is_null($path)) ? 'tmp' : urldecode($path);
            
            if ($user_id === '0')
            {
                $dir = $media_path . DS . str_replace(array('/', '\\'), DS, $path);
            }
            else
            {
                $group = User::get_group($user_id);

                $dir = $media_path . DS . 'users' . DS . $group . DS . $user_id . DS . $path;
            }

            if (! is_dir($dir) )
            {
                System::mkdir($dir);
            }

            $result[$name] = Assets::path2url(Upload::save($file, null, $dir));

            Module::action('file_uploaded');
        }

        if ($this->request->is_ajax())
        {
            $ajax = new Fajax();
            $ajax->data($result);
            if ( ! empty($errors))
            {
                $ajax->succuss(false);
                foreach ($errors as $fname => $file)
                {
                    foreach ($file as $error)
                    {
                        $ajax->message('file: ' . $fname . ' ' . $error);
                    }
                }
            }
            $this->response->body($ajax->build_result());
        }
        else
        {
            if ($this->request->query('CKEditor')) {
                $output = __('Upload image successfully ! Browse image lab to insert !')
                    . '<script>parent.ckFillImage("'.implode(',', $result).'");</script>';
                $this->response->body($output);
            } else {
                $this->response->body(implode(',', $result));
            }
        }

    }

    /**
     * Crop Image and remove origin image
     *
     * @internal fateak-action-acl
     */
    public function action_crop()
    {
        ACL::required('controller_upload_action_crop');

        $upload_config = Kohana::$config->load('file');
        $user_id = $this->request->query('user');
        $user = User::active_user();

        if (is_null($user_id))
        {
            $user_id = $user->id;
        }
        else
        {
            if ($user->id != $user_id)
            {
                ACL::required('manage all files');
            }
        }

        $crop_absolutely = $this->request->post('crop_absolutely');
        $width = $this->request->post('crop_w');
        $height = $this->request->post('crop_h');
        $left = $this->request->post('crop_x');
        $top = $this->request->post('crop_y');
        $ruler = $this->request->post('ruler_width');

        $file_src = Assets::url2path($this->request->post('filesrc'));
        $image = Image::factory($file_src);
        $ratio = ($image->width > $ruler) ? $image->width / $ruler : 1;

        $ajax = new Fajax();
        $new_file = File::changeName($file_src);

        try
        {
            if ($crop_absolutely)
            {
                $max_height = $image->height / $ratio;
                $image->resize($ruler, $max_height);
                $image->crop($width, $height, $left, $top);
            }
            else
            {
                $real_left = $ratio * $left; 
                $real_top = $ratio * $top;
                $real_width = $ratio * $width;
                $real_height = $ratio * $height;
                $image->crop($real_width, $real_height, $real_left, $real_top);
            }

            $ajax->data(Assets::path2url($new_file));
        }
        catch (Exception $e)
        {
            $ajax->success(false);
            $ajax->message($e->getMessage());
        }

        $image->save($new_file);
        File::delete($file_src);
        
        $this->response->body($ajax->build_result());
    }
}
