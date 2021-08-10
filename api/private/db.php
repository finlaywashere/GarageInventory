<?php

require_once "config.php";

function db_connect(){
	GLOBAL $dbhost;
	GLOBAL $dbuser;
	GLOBAL $dbpass;
	GLOBAL $dbname;

	$conn = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
	if($conn->connect_error){
		return 0;
	}
	return $conn;
}

?>
