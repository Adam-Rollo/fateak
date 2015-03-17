<?php

class Controller_Upload extends Controller
{
    /**
     * Recieve Image and save
     */
    public function action_index()
    {
        $this->response->body(print_r($_POST, 1));
    }
}
