<?php

class Model_User_Profile extends ORM
{
    /**
     * PDO must declare table columns
     */
    protected $_table_columns = array(
        'id' => array('type' => 'int'),
        'name' => array('type' => 'string'),
        'dob' => array('type' => 'string'),
        'avatar' => array('type' => 'string'),
        'bio' => array('type' => 'string'),
        'country' => array('type' => 'string'),
        'province' => array('type' => 'string'),
        'city' => array('type' => 'string'),
        'photowall' => array('type' => 'string'),
    );

    /**
     * explicit db group. Developers could rewrite or change the value
     */
    protected $_db_group = 'default';

    /**
     * Image fields
     */
    protected $_image_fields = array('avatar', 'photowall');

    /**
     * Update user's profile
     */
    public function update_user($values, $expected = NULL)
    {
        foreach ($this->_image_fields as $field_name)
        {
            if (! isset($values[$field_name]))
            {
                continue;
            }

            $images = JSON::decode($values[$field_name]);

            if (is_array($images))
            {
                $images_array = array();

                foreach ($images as $image)
                {
                    if (preg_match("/(\/.*([\d]+)\/)tmp\/(.*)/", $image))
                    {
                        $images_array[] = Upload::move_tmp_by_url($image);
                    }
                    else
                    {
                        $images_array[] = $image;
                    }   
                }

                $values[$field_name] = JSON::encode($images_array);
            }
        }

	    return $this->values($values, $expected)->update();
    }

    public function get_name($id, $default = 'Unknown')
    {
        $this->select('id', 'name')
            ->where('id', '=', $id)
            ->find();

        if ($this->loaded($this) && ($this->name != ''))
        {
            $user_name = $this->name;
        }
        else
        {
            $user_name = $default;
        }

        return $user_name;
    }
}
