<?php

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
        if (is_null($user_id))
        {
            $user = User::active_user();
            $user_id = $user->id;
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
                $group = ceil( $user_id / $upload_config['user_number_per_group']);

                $dir = $media_path . DS . 'users' . DS . $group . DS . $user_id . DS . $path;

                if (! is_dir($dir) )
                {
                    System::mkdir($dir);
                }

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
            $this->response->body(implode(',', $result));
        }

    }
}
