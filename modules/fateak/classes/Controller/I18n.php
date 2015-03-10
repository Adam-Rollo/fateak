<?php
/**
 * Language settings
 */
class Controller_I18n extends Controller
{
    /**
     *  Change language.
     */
    public function action_index()
    {
        $language = $this->request->query('lang');

        I18n::lang($language);

        if (! $this->request->is_ajax())
        {
            $url = urldecode($this->request->query('url'));
           
            HTTP::redirect($url);
        }
    }
}
