<?php

class CURL {
    var $callback = false;
    var $secure = false;
    var $conn = false;
    var $cookiefile =false;

   
    function CURL($u) {
      global $debug;
        $this->conn = curl_init();
        if ($debug) {
            $this->cookiefile='f:/crawler/temp/'.md5($u);
        } else {
            $this->cookiefile='temp/'.md5($u);
        }
       
    }

    function setCallback($func_name) {
        $this->callback = $func_name;
    }

    function close() {
        curl_close($this->conn);
        /*if (is_file($this->cookiefile)) {
            unlink($this->cookiefile);
        } */
       
    }
   
   
    function doRequest($method, $url, $vars) {

        $ch = $this->conn;

        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);

        if($this->secure) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiefile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        }
        $data = curl_exec($ch);
        if ($data) {
            if ($this->callback)
            {
                $callback = $this->callback;
                $this->callback = false;
                return call_user_func($callback, $data);
            } else {
                return $data;
            }
        } else {
            return curl_error($ch);
        }
    }

    function get($url) {
        return $this->doRequest('GET', $url, 'NULL');
    }

    function post($url, $vars) {
        return $this->doRequest('POST', $url, $vars);
    }
}

?>
