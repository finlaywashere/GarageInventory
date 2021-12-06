<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/product.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/invoice.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/journal.php";

/**
	Helper function to update values in the db
*/
function update_value($table, $selector_name, $selector_value, $column_name, $column_value){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE ? SET ?=? WHERE ?=?;");
	$stmt->bind_param("sssss",$table,$column_name,$column_value,$selector_name,$selector_value);
	$stmt->execute();
	
	$conn->close();
	return 1;
}
?>
