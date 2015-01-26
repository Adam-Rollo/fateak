<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

    public function action_index()
    {
        /**
        $dreamer = ORM::factory('dream');
        $dreamer->string = '汉字';
        $dreamer->number = 12;
        $dreamer->save();
        **/

        /**
        $dreamer = ORM::factory('dream', 10);
        echo $dreamer->string;
        **/

        /**
        $dreamer = ORM::factory('dream');
        $dreamer->string = md5(time());
        $dreamer->number = time();
        $dreamer->save();
        **/

        /**
        $id = mt_rand(1, 100000);
        $dreamer = ORM::factory('dream', $id);
        $dreamer->string = md5(time());
        $dreamer->save();
        **/

        /**
        $dreamer = ORM::factory('ptest');
        $dreamer->str = md5(time() + mt_rand(1, 100));
        $dreamer->box = mt_rand(1,20);
        $dreamer->save();
        **/

        $dreamer = ORM::factory('puser');
        $dreamer->fname = $this->random_string(mt_rand(3,9));
        $dreamer->lname = $this->random_string(mt_rand(3,9));
        $dreamer->signed = $this->ecrypt($dreamer->fname);
        // echo $dreamer->signed;
        $dreamer->save();

	$this->response->body('hello, world!');

    }

    public function action_memory()
    {
        echo "E:".memory_get_usage() . "<br/>";
        //$this->memory_test();    
        $this->memory_test();
        echo "E:".memory_get_usage() . "<br/>";
        $this->memory_loop_test();
        echo "E:".memory_get_usage() . "<br/>";
    }

    public function action_tc()
    {
        echo Autolink::filter('www.f.com/a/b.html');
    }

    private function memory_test()
    {
        echo memory_get_usage() . "<br/>";
        $dreamer = ORM::factory('puser')->where('signed', '=', 21)->limit(1000);
        $darr = $dreamer->find_all()->as_array();
        echo memory_get_usage() . "<br/>";
        $int = 255;
        echo memory_get_usage() . "<br/>";
        //unset($dreamer);
        //unset($darr);
        echo memory_get_usage() . "<br/>";
        unset($int);
        echo memory_get_usage() . "<br/>";

        $this->memory_loop_test();
        
    }

    private function memory_loop_test()
    {
        echo memory_get_usage() . "<br/>";
        for ($i = 0; $i < 10; $i++) {
            $dreamer = ORM::factory('puser')->where('signed', '=', 21)->limit(1000);
            $darr = $dreamer->find_all()->as_array();
            echo memory_get_usage() . "<br/>";
            unset($darr);
            unset($dreamer);
            echo memory_get_usage() . "<br/>";
        }
     
    }

    protected function random_string($len)
    {
        $string = "";
        for ($i = 0; $i < $len; $i++) {
            $number = mt_rand(97, 122);
            $string .= chr($number);
        }
        return ucfirst($string);
    }

    protected function ecrypt($str)
    {
        $str1 = substr($str, 0, 1);
        $number = abs(ord($str1) - 97) % 4;
        //echo ord($str1)." ";
        $str2 = substr($str, 1, 1);
        //echo ord($str2)." ";
        $number = $number * 25 + ord($str2) - 97;
        $number = $number % 100;

        return $number;
    }

} // End Welcome
