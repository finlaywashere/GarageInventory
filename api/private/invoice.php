<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

/**

Invoice types:
0 = System
1 = Incoming
2 = Outgoing

Invoice flags:
1 - Tax Exempt

Invoice status:
0 - Carry With
1 - Due (Paid, Unfinished)
2 - Due (Paid, Finished)
3 - Due (Paid, Picked up)
4 - Refunded

*/

function invoice_create($subtotal, $total, $customer, $type, $notes, $entries, $orig_id, $date, $payments, $user, $related=0, $due=NULL, $flags=NULL, $status=0){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = $conn->prepare("INSERT INTO invoices (invoice_subtotal, invoice_total, customer_id, invoice_type, invoice_notes, original_id, invoice_date, invoice_related, invoice_due, invoice_flags, invoice_status, invoice_user) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param("iiiisssisiii",$subtotal,$total,$customer,$type,$notes,$orig_id,$date,$related,$due,$flags,$status,$user);
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
	for($i = 0; $i < count($payments); $i++){
		$type = $payments[$i]->{'type'};
		$amount = $payments[$i]->{'amount'};
		$identifier = $payments[$i]->{'identifier'};
		$notes = $payments[$i]->{'notes'};
		payment_create($user,$id,$amount,$type,$identifier,$notes);
	}

	$conn->close();
	return $id;
}

function get_invoices_from_date($date){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT invoice_id FROM invoices WHERE invoice_date = ?;");
	$stmt->bind_param("s",$date);
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
	}else if($stype == 4){
		// Search by status
		$stmt = $conn->prepare("SELECT invoice_id FROM invoices WHERE invoice_status = ? AND invoice_id > ? LIMIT ?;");
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

function invoice_product_search($id, $count){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT `invoice_id` FROM `invoice_entries` WHERE `product_id`=? LIMIT ? ORDER BY invoice_date DESC;");
	$stmt->bind_param("ii",$id,$count);
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
	$return = array("notes" => $row['invoice_notes'], "original_id" => $row['original_id'], "type" => $row['invoice_type'], "date" => $row['invoice_date'], "total" => $row['invoice_total'], "subtotal" => $row['invoice_subtotal'], "customer" => $row['customer_id'], "invoice_id" => $row['invoice_id'], "related" => $row['invoice_related'], "due_date" => $row['invoice_due'], "flags" => $row['invoice_flags'], "status" => $row['invoice_status'], "creation" => $row['invoice_creation'], "entries" => get_invoice_entries($invoice_id), "payments" => get_payments($invoice_id));

	$conn->close();
	return $return;
}
function invoice_refundable($invoice_id, $product, $count){
	$entries = get_invoice_entries($invoice_id);
	for($i = 0; $i < count($entries); $i++){
		$entry = $entries[$i];
		if($entry["product"] == $product){
			// Found matching entry
			$total = $entry['entry_count'] - $entry['refunded_count'];
			$ucount = $entry['unit_count'];
			$count2 = $count / $ucount;
			$mod = $count % $ucount;
			if($mod == 0 && $count2 < $total){
				return 0;
			}else{
				$diff = min($count2,$total);
				$count -= $diff * $ucount;
			}
		}
	}
	return $count;
}
function invoice_refund($invoice_id, $product, $count){
	if(invoice_refundable($invoice_id,$product,$count))
		return 1;
	$entries = get_invoice_entries($invoice_id);
	for($i = 0; $i < count($entries); $i++){
		$entry = $entries[$i];
		if($entry["product"] == $product){
			// Found matching entry
			$total = $entry['entry_count'] - $entry['refunded_count'];
			$ucount = $entry['unit_count'];
			$count2 = $count / $ucount;
			$diff = min($count2,$total);
			invoice_entry_refund($entry['entry_id'],$diff);
			$count -= $diff * $ucount;
			if($count == 0){
				return 0;
			}
		}
	}
}
/**

Checks an invoices status for possible updates
Currently only supports refund status updating and due items tracking

*/
function invoice_check_status($invoice_id){
	$inv = get_invoice($invoice_id);
	$status = $inv['status'];
	$entries = $inv['entries'];
	if($status != 5){
		$found = false;
		for($i = 0; $i < count($entries); $i++){
			$entry = $entries[$i];
			if($entry['refunded_count'] < $entry['entry_count']){
				$found = true;
				break;
			}
		}
		if($found == false){
			invoice_set_status($invoice_id,5);
		}
	}
}
function invoice_set_status($invoice_id, $status){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE invoices SET invoice_status+=? WHERE invoice_id=?;");
	$stmt->bind_param("ii",$status,$invoice_id);
	$stmt->execute();

	$conn->close();
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
		array_push($return,array("product" => $row['product_id'], "original_id" => $row['original_id'], "unit_count" => $row['unit_count'],"count" => $row['entry_count'],"unit_price" => $row['entry_unit_price'],"notes" => $row['entry_notes'], "unit_discount" => $row['entry_discount'], "due" => $row['due_count'], "refunded_count" => $row['entry_refunded'], "entry_id" => $row['entry_id']));
	}

	$conn->close();
	return $return;
}
/**

Refunds a number of items from an invoice entry
A refund invoice must be created seperately from this, it only updates the original invoice
TODO: Figure out how this should integrate with the due file

*/
function invoice_entry_refund($entry_id, $count){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE invoice_entries SET entry_refunded+=? WHERE entry_id=?;");
	$stmt->bind_param("ii",$count,$entry_id);
	$stmt->execute();
	
	$conn->close();
}

?>
