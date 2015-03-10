<?php

class Fateak_CURL
{
    public static function get($url)
    {
        // create curl resource 
        $ch = curl_init(); 

        // set url 
        curl_setopt($ch, CURLOPT_URL, $url); 

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        //设定为不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        // $output contains the output string 
        $output = curl_exec($ch); 

        // close curl resource to free up system resources 
        curl_close($ch); 

        return $output;
    }

    public static function post($url, $data)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1);
        @curl_setopt($ch, CURLOPT_POSTFIELDS,  $data);
        //$curl_file = new CurlFile($data['file'], 'image/jpg');
        //curl_setopt($ch, CURLOPT_POSTFIELDS,  $curl_file);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        $output = curl_exec($ch);

        if ($output === false) {
            Log::info("[".curl_errno($ch)."]:".curl_error($ch));  
            return array('errno' => curl_errno($ch), 'error' => curl_error($ch));
        }

        curl_close($ch);

        return $output;
    }


    private function cURLcheckBasicFunctions() 
    { 
        if( !function_exists("curl_init") && 
                !function_exists("curl_setopt") && 
                !function_exists("curl_exec") && 
                !function_exists("curl_close") ) return false; 
        else return true; 
    } 

    /* 
     * Returns string status information. 
     * Can be changed to int or bool return types. 
     */ 
    public static function cURLdownload($url, $file) 
    { 
        if( !$this->cURLcheckBasicFunctions() ) return "UNAVAILABLE: cURL Basic Functions"; 
        $ch = curl_init(); 
        if($ch) 
        { 
            $fp = fopen($file, "w"); 
            if($fp) 
            { 
                if( !curl_setopt($ch, CURLOPT_URL, $url) ) 
                { 
                    fclose($fp); // to match fopen() 
                    curl_close($ch); // to match curl_init() 
                    return "FAIL: curl_setopt(CURLOPT_URL)"; 
                } 
                if( !curl_setopt($ch, CURLOPT_FILE, $fp) ) return "FAIL: curl_setopt(CURLOPT_FILE)"; 
                if( !curl_setopt($ch, CURLOPT_HEADER, 0) ) return "FAIL: curl_setopt(CURLOPT_HEADER)"; 
                if( !curl_exec($ch) ) return "FAIL: curl_exec()"; 
                curl_close($ch); 
                fclose($fp); 
                return "SUCCESS: $file [$url]"; 
            } 
            else return "FAIL: fopen()"; 
        } 
        else return "FAIL: curl_init()"; 
    } 
}
