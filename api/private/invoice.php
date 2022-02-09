<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

function invoice_create($subtotal, $total, $customer, $type, $notes, $entries, $paid, $orig_id, $date, $related=0){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	
	$stmt = $conn->prepare("INSERT INTO invoices (invoice_subtotal, invoice_total, customer_id, invoice_type, invoice_notes,invoice_paid, original_id, invoice_date, invoice_related) VALUES (?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param("iiiisissi",$subtotal,$total,$customer,$type,$notes,$paid,$orig_id,$date,$related);
	$stmt->execute();
	$stmt = $conn->prepare("SELECT LAST_INSERT_ID();");
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$id = $row['LAST_INSERT_ID()'];
	for($i = 0; $i < count($entries); $i++){
		$product = $entries[$i]->{'product'};
		$orig = $entries[$i]->{'orig'};
		$count = $entries[$i]->{'count'};
		$unit_count = $entries[$i]->{'unit_count'};
		$unit_price = $entries[$i]->{'unit_price'};
		$notes = $entries[$i]->{'notes'};
		$discount = $entries[$i]->{'unit_discount'};
		if($notes == ""){
			$notes = NULL;
		}
		if($orig == ""){
			$orig = NULL;
		}
		$stmt = $conn->prepare("INSERT INTO invoice_entries (invoice_id, product_id, original_id, entry_count, unit_count, entry_unit_price, entry_notes, entry_discount) VALUES (?,?,?,?,?,?,?,?);");
		$stmt->bind_param("iisiiisi",$id,$product,$orig,$count,$unit_count,$unit_price,$notes,$discount);
		$stmt->execute();
		adjust_stock($product,$count);
	}

	$conn->close();
	return $id;
}

function invoice_search($stype, $value, $offset, $limit){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = NULL;
	if($stype == 1){
		// Search by invoice ID
		$conn->close();
		return array((int) $value);
	}else if($stype == 2){
		// Search by date
		$stmt = $conn->prepare("SELECT invoice_id FROM invoices WHERE DATE(invoice_date) = ? AND invoice_id > ? LIMIT ?;");
		$stmt->bind_param("sii",$value,$offset,$limit);
	}else if($stype == 3){
		// Search by customer
		$stmt = $conn->prepare("SELECT invoice_id FROM invoices WHERE customer_id = ? AND invoice_id > ? LIMIT ?;");
		$stmt->bind_param("iii",$value,$offset,$limit);
	}else{
		$conn->close();
		return NULL;
	}
	$stmt->execute();
	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return array();
	}
	$return = array();
	while($row = $result->fetch_assoc()){
		array_push($return,$row['invoice_id']);
	}
	$conn->close();
	return $return;
}

function invoice_product_search($id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT `invoice_id` FROM `invoice_entries` WHERE `product_id`=?;");
	$stmt->bind_param("i",$id);
	$stmt->execute();

	$result = $stmt->get_result();

	$return = array();

	while($row = $result->fetch_assoc()){
		array_push($return,$row['invoice_id']);
	}

	$conn->close();
	return array_unique($return);
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
	$return = array("notes" => $row['invoice_notes'], "original_id" => $row['original_id'], "type" => $row['invoice_type'], "date" => $row['invoice_date'], "total" => $row['invoice_total'], "subtotal" => $row['invoice_subtotal'], "customer" => $row['customer_id'], "invoice_id" => $row['invoice_id'], "paid" => $row['invoice_paid'], "related" => $row['invoice_related']);

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
	$return = array("invoice" => $row['invoice_id'], "product" => $row['product_id'], "original_id" => $row['original_id'], "unit_count" => $row['unit_count'],"count" => $row['entry_count'],"unit_price" => $row['entry_unit_price'],"notes" => $row['entry_notes'], "unit_discount" => $row['entry_discount']);

	$conn->close();
	return $return;
}


?>
