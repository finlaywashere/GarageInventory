<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

/**
	Helper function to update invoice values
*/
function update_invoice($invoice_id, $column_name, $column_value){
	return update_value("invoices","invoice_id",$invoice_id,$column_name,$column_value);
}
/**
	Helper function to update invoice entry values
*/
function update_invoice_entry($entry_id, $column_name, $column_value){
	return update_value("invoice_entries","entry_id",$entry_id,$column_name,$column_value);
}
/**
	Gets the pricing information from an invoice
*/
function get_invoice_total($invoice_id){
	$entries = get_invoice_entries($invoice_id);
	$subtotal = 0;
	for($i = 0; $i < count($entries); $i++){
		$entry = get_invoice_entry($i);
		$count = $entry[2];
		$unit_price = $entry[3];
		$entry_price = $unit_price*$count;
		$subtotal += $entry_price;
	}
	GLOBAL $tax;
	$total = (int) ($subtotal * $tax / 100);
	return array($total,$subtotal);
}

/**
	Gets all the information on file about an invoice from its id
*/
function get_invoice($invoice_id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM `invoices` WHERE `invoice_id`=?;");
	$stmt->bind_param("i",$invoice_id);
	$stmt->execute();

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return 0;
	}
	$row = $result->fetch_assoc();
	$return = array($row['invoice_notes'],$row['original_id'],$row['invoice_store'],$row['invoice_date']);

	$conn->close();
	return $return;
}
/**
	Gets the invoice id's of every invoide in the database
*/
function get_invoices(){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT `invoice_id` FROM `invoices` WHERE 1;");
	$stmt->execute();

	$result = $stmt->get_result();

	$return = array();

	while($row = $result->fetch_assoc()){
		array_push($return,$row['invoice_id']);
	}

	$conn->close();
	return $return;
}
/**
	Deletes an invoice and its entries
*/
function delete_invoice($invoice_id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("DELETE FROM `invoices` WHERE `invoice_id`=?;");
	$stmt->bind_param("i",$invoice_id);
	$stmt->execute();
	$stmt = $conn->prepare("DELETE FROM `invoice_entries` WHERE `invoice_id`=?;");
	$stmt->bind_param("i",$invoice_id);
	$stmt->execute();

	$conn->close();
	return 1;
}
/**
	Deletes an invoice entry
*/
function delete_invoice_entry($entry_id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("DELETE FROM `invoice_entries` WHERE `entry_id`=?;");
	$stmt->bind_param("i",$entry_id);
	$stmt->execute();

	$conn->close();
	return 1;
}
/**
	Gets all of the entry id's within an invoice from its id
*/
function get_invoice_entries($invoice_id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT `entry_id` FROM `invoice_entries` WHERE `invoice_id`=?;");
	$stmt->bind_param("i",$invoice_id);
	$stmt->execute();

	$result = $stmt->get_result();

	$return = array();

	while($row = $result->fetch_assoc()){
		array_push($return,$row['entry_id']);
	}

	$conn->close();
	return $return;
}
/**
	Gets all the information on file about an invoice entry from its id
*/
function get_invoice_entry($entry_id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM `invoice_entries` WHERE `entry_id`=?;");
	$stmt->bind_param("i",$entry_id);
	$stmt->execute();

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return 0;
	}
	$row = $result->fetch_assoc();
	$return = array($row['invoice_id'],$row['product_id'],$row['entry_count'],$row['entry_unit_price'],$row['entry_notes']);

	$conn->close();
	return $return;
}


?>