<?php

include "config.php";
include "lib/network/clients.php";

if (
  (
    (
      isAllowedClient(CLIENTS) &&
      CLIENTS_ALL == false
    ) ||
    CLIENTS_ALL == true
  ) && (
    (
      in_array($_SERVER['HTTP_HOST'], HOSTS) &&
      HOSTS_ALL == false
    ) ||
    HOSTS_ALL == true
  )
) {
  if(isset($_GET['name'])) {
    $name = $_GET['name'];
    if(file_exists('api/'.$name.'.php')) {
      include 'lib/api/api.php';
      include 'api/'.$name.'.php';
      if (class_exists($name . '_api')) {
        if(isset($_GET['token']) && $_GET['token'] != '') {
          $token = stripslashes($_GET['token']);
          $query = $db->query('SELECT COUNT(`id`) AS count, `id` FROM `user` WHERE `token` = "' . $token . '"');
          $query = $query->fetch();
          if($query['count'] > 0) {
            $classname = $name . '_api';
            $class = new $classname(array("db" => $db, "uid" => $query['id']));
            $format = (isset($_GET['format']) ? $_GET['format'] : 'json');
            switch($format) {
              case 'xml':
                header('Content-Type: application/xml; charset=utf-8');
                $xml = new SimpleXMLElement('<root/>');
                $arr = array_flip($class->out());
                array_walk_recursive($arr, array( $xml, 'addChild'));
                echo $xml->asXML();
              break;
              case 'json':
              default:
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($class->out());
              break;
            }
          } else {
            header("HTTP/1.1 401 Unauthorized");
            exit;
          }
        } else {
          header("HTTP/1.1 401 Unauthorized");
          exit;
        }
    	} else {
    		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    		exit;
    	}
    } else {
      header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
      exit;
    }
  	die();
  }
} else {
  die();
}
