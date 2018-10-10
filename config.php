<?php

/*GLOBAL*/
define("NAME", "Monkey API");

/*NETWORK*/
define("HOSTS", array(
  "example.tld",
  "localhost",
  "127.0.0.1"
));
define("HOSTS_ALL", true);

define("CLIENTS", array(
  "8.*",
  "100.47.*"
));
define("CLIENTS_ALL", true);

/*DATABASE*/
define("DBHOST", "localhost");
define("DBPORT", "3306");
define("DBTYPE", "mysql");
define("DBNAME", "mapi");
define("DBUSER", "user");
define("DBPASS", "password");

include "lib/database/data_connect.php";
$db = pdo_connect_dbserver(true, DBTYPE, DBHOST, DBNAME, DBUSER, DBPASS, DBPORT);
