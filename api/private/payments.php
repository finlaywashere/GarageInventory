<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

function payment_balance($invoice){
	$conn = db_connect("inventory");
	if(!$conn){
		return -1;
	}
	$stmt = $conn->prepare("SELECT invoice_total FROM invoices WHERE invoice_id = ?;");
	$stmt->bind_param("i",$invoice);
	$stmt->execute();
	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return -1;
	}
	$row = $result->fetch_assoc();
	$total = $row['invoice_total'];
	$stmt = $conn->prepare("SELECT payment_amount FROM payments WHERE invoice_id = ?;");
	$stmt->bind_param("i",$invoice);
	$stmt->execute();
	$result = $stmt->get_result();
	
	$paid = 0;

	while($row = $result->fetch_assoc()){
		$paid += $row['payment_amount'];
	}
	$conn->close();
	if($paid < $total){
		return $total-$paid;
	}
	return 0;
}
function get_payments($invoice){
	$conn = db_connect("inventory");
	if(!$conn){
		return -1;
	}
	$stmt = $conn->prepare("SELECT * FROM payments WHERE invoice_id = ?;");
	$stmt->bind_param("i",$invoice);
	$stmt->execute();
	$result = $stmt->get_result();
	
	$ret = array();

	while($row = $result->fetch_assoc()){
		array_push($ret,array("user" => get_user($row['user_id'])['username'], "date" => $row['payment_date'], "amount" => $row['payment_amount'], "type" => $row['payment_type'], "identifier" => $row['payment_identifier'], "notes" => $row['payment_notes']));
	}
	$conn->close();
	return $ret;
}

function get_accounts(){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM accounts WHERE 1;");
	$stmt->execute();

	$result = $stmt->get_result();
	$ret = array();

	while($row = $result->fetch_assoc()){
		$ret[$row['account_id']] = array("name" => $row['account_name'], "perms" => $row['account_perms'], "desc" => $row['account_desc']);
	}
	$conn->close();
	return $ret;
}

/**

Payment types:

0 - Cash
1 - Credit
2 - Debit
3 - Cheque
4 - Account
5 - Virtual

*/

function payment_create($user, $invoice, $amount, $type, $identifier, $notes=""){
	$balance = payment_balance($invoice);
	if($balance < $amount){
		return 1;
	}
	if($type == 4){
		$accounts = get_accounts();
		if(!isset($accounts[$identifier])){
			return 3;
		}
		if($notes == ""){
			return 4;
		}
	}
	//TODO: Implement virtual accounts (moving money between invoices)
	$conn = db_connect("inventory");
	if(!$conn){
		return 2;
	}
	$stmt = $conn->prepare("INSERT INTO payments (user_id,invoice_id,payment_amount,payment_type,payment_identifier,payment_notes) VALUES (?,?,?,?,?,?);");
	$stmt->bind_param("iiiiss",$user,$invoice,$amount,$type,$identifier,$notes);
	$stmt->execute();
	$conn->close();
	return 0;
}

?>
