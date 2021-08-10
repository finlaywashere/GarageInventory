<?php

require_once "db.php";

function get_product($product_id){
	$conn = db_connect();
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM `products` WHERE `product_id`=?;");
	$stmt->bind_param("i",$product_id);
	$stmt->execute();

	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	if(!$row){
		return 0;
	}
	$return = array($row['original_id'],$row['product_name'],$row['product_desc'],$row['stock_count'],$row['stock_location'],$row['stock_notes']);

	$conn->close();
	return $return;
}
function get_invoice($invoice_id){
	$conn = db_connect();
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM `invoices` WHERE `invoice_id`=?;");
	$stmt->bind_param("i",$invoice_id);
	$stmt->execute();

	$result = $stmt->get_result();
	$row = $stmt->fetch_assoc();
	if(!$row){
		return 0;
	}
	$return = array($row['invoice_price'],$row['invoice_notes'],$row['original_id'],$row['invoice_store'],$row['invoice_price_no_tax'],$row['invoice_timestamp']);

	$conn->close();
	return $return;
}
function get_invoice_entries($invoice_id){
	$conn = db_connect();
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM `invoice_entries` WHERE `invoice_id`=?;");
	$stmt->bind_param("i",$invoice_id);
	$stmt->execute();

	$result = $stmt->get_result();
	
	$return = array();

	while(1){
		$row = $result->fetch_assoc();
		if(!$row) break;
		array_push($return,array($row['product_id'],$row['entry_count'],$row['entry_unit_price'],$row['entry_notes']));
	}
	
	$conn->close();
	return $return;
}

?>
