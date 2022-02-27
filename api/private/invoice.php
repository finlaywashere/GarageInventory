<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

function invoice_create($subtotal, $total, $customer, $type, $notes, $entries, $orig_id, $date, $related=0, $due=NULL){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	
	$stmt = $conn->prepare("INSERT INTO invoices (invoice_subtotal, invoice_total, customer_id, invoice_type, invoice_notes, original_id, invoice_date, invoice_related, invoice_due) VALUES (?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param("iiiisissis",$subtotal,$total,$customer,$type,$notes,$orig_id,$date,$related,$due);
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
		$due_count = $entries[$i]->{'due'};
		if($notes == ""){
			$notes = NULL;
		}
		if($orig == ""){
			$orig = NULL;
		}
		$stmt = $conn->prepare("INSERT INTO invoice_entries (invoice_id, product_id, original_id, entry_count, unit_count, entry_unit_price, entry_notes, entry_discount, due_count) VALUES (?,?,?,?,?,?,?,?,?);");
		$stmt->bind_param("iisiiisii",$id,$product,$orig,$count,$unit_count,$unit_price,$notes,$discount,$due_count);
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
	$return = array("notes" => $row['invoice_notes'], "original_id" => $row['original_id'], "type" => $row['invoice_type'], "date" => $row['invoice_date'], "total" => $row['invoice_total'], "subtotal" => $row['invoice_subtotal'], "customer" => $row['customer_id'], "invoice_id" => $row['invoice_id'], "related" => $row['invoice_related'], "due_date" => $row['invoice_due'], "entries" => get_invoice_entries($invoice_id), "payments" => get_payments($invoice_id));

	$conn->close();
	return $return;
}
/**
	Gets all of the entry id's within an invoice from its id
*/
function get_invoice_entries($invoice_id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM `invoice_entries` WHERE `invoice_id`=?;");
	$stmt->bind_param("i",$invoice_id);
	$stmt->execute();

	$result = $stmt->get_result();

	$return = array();

	while($row = $result->fetch_assoc()){
		array_push($return,array("product" => $row['product_id'], "original_id" => $row['original_id'], "unit_count" => $row['unit_count'],"count" => $row['entry_count'],"unit_price" => $row['entry_unit_price'],"notes" => $row['entry_notes'], "unit_discount" => $row['entry_discount'], "due" => $row['due_count']));
	}

	$conn->close();
	return $return;
}

?>
