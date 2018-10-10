<?php
function pdo_connect_dbserver($display_error, $db_type, $db_host = "localhost", $db_name, $db_user, $db_pass, $db_port, $db_mode = 0, $db_pers = true) {
	try {
		switch ($db_type) {
			case 'cubrid':
			case 'dblib': case 'mssql': case 'sybase':
			case 'firebird':
			case 'informix':
			case 'oci':
			case 'mysql':
			case 'pgsql':
				$dbserver = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass, array(PDO::ATTR_PERSISTENT => $db_pers)); break;
			case 'sqlite':
			case 'sqlite2':
				$dbserver = new PDO("$db_type:$db_host", $db_user, $db_pass, array(PDO::ATTR_PERSISTENT => $db_pers)); break;
			case 'ibm':
				$dbserver = new PDO("ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$db_name;HOSTNAME=$db_host;PORT=$db_port;PROTOCOL=TCPIP;", $db_user, $db_pass, array(PDO::ATTR_PERSISTENT => $db_pers)); break;
			case 'odbc':
				$dbserver = new PDO("odbc:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME=$db_host;PORT=$db_port;DATABASE=$db_name;PROTOCOL=TCPIP;UID=$db_user;PWD=$db_pass;", array(PDO::ATTR_PERSISTENT => $db_pers)); break;
			case 'dbase':
				$dbserver = dbase_open($db_host, $db_mode); break;
			case 'mysqli':
				$dbserver = mysqli_connect($db_host, $db_user, $db_pass, $db_name); break;
			default:
				$error .= 'Serveur non pris en charge'; break;
		}
	} catch(PDOException $e) {
		$error .= "[PDO] Erreur de connexion au serveur $db_type";
	}
	if ($display_error == true && isset($error))
		echo "<p class='error connect_dbserver'>$error</p>";
	return $dbserver;
}
