<?php

class api {
    public $arr = array();

    public $db;
    public $uid = false;

    public function __construct(array $arr) {
        foreach($arr as $k => $v) {
            $this->{$k} = $v;
        }
    }

    function parse_formdata() {
        $data = array();
        $input = file_get_contents('php://input');
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];
        $a_blocks = preg_split("/-+$boundary/", $input);
        array_pop($a_blocks);
        foreach ($a_blocks as $id => $block) {
            if (empty($block))
              continue;
            if (strpos($block, 'application/octet-stream') !== FALSE) {
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            } else {
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }
            $data[$matches[1]] = $matches[2];
        }
        return $data;
    }

    function parse_cookie($str) {
        $headerCookies = explode('; ', $str);
        $cookies = array();
        foreach($headerCookies as $itm) {
            list($key, $val) = explode('=', $itm,2);
            $cookies[$key] = $val;
        }
        return $cookies;
    }

    function getallheaders() {
      $headers = [];
      foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
          $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
      }
      return $headers;
    }

    function get_method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    function get_custom_headers() {
        $headers = $this->getallheaders();
        $headers['Cookie'] = $this->parse_cookie($headers['Cookie']);
        $al = explode(",", $headers['Accept-Language']);
        $aclg = array();
        foreach($al as $k => $v) {
            $a = explode(";", $v);
            $aclg[$a[0]] = substr(($a[1] == null ? "q=1" : $a[1]), 2);
        }
        $headers['Accept-Language'] = $aclg;
        $headers['Accept-Encoding'] = explode(", ", $headers['Accept-Encoding']);
        return $headers;
    }

    function content($ctype) {
        switch ($ctype) {
            case 'application/json':
                $data = json_decode(file_get_contents('php://input'));
            break;
            case 'multipart/form-data':
                $data = $this->parse_formdata();
                unset($data[""]);
            break;
            case 'application/x-www-form-urlencoded':
                parse_str(file_get_contents('php://input'), $data);
            break;
            default:
                $cd = array(
                    'application/json',
                    'multipart/form-data',
                    'application/x-www-form-urlencoded'
                );
                $dt = array();
                foreach($cd as $k => $v) {
                    $dt[$v] = $this->content($v)[1];
                }
                $data = max($dt);
                $ctype = array_search($data, $dt);
            break;
        }
        return array($ctype, $data);
    }

    function fetchUser() {
        $user = $this->db->query('SELECT `id`, `username` FROM `user` WHERE `id` = ' . $this->uid);
        return $user->fetch(PDO::FETCH_ASSOC);
    }

    function out() {
        $method = $_SERVER['REQUEST_METHOD'];
        $headers = $this->get_custom_headers();
        $data = $this->content($headers['Content-Type']);
        $headers['Cookie'] = $this->parse_cookie($headers['Cookie']);
        $get = $_GET;
        $files = $_FILES;
        $user = $this->fetchUser();

        $this->arr["request"] = $_SERVER['REQUEST_URI'];
        $this->arr["method"] = $method;
        $this->arr["data-type"] = $data[0];
        $this->arr["data"] = $data[1];
        $this->arr["headers"] = $headers;
        $this->arr["get"] = $get;
        $this->arr["files"] = $files;
        $this->arr["user"] = $user;
        return $this->arr;
    }
}

?>
