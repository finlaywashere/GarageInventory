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
		$ret[$row['account_id']] = array("name" => $row['account_name'], "perms" => $row['account_perms'], "desc" => $row['account_desc'], "balance" => get_account_balance($row['account_id']));
	}
	$conn->close();
	return $ret;
}
function get_account($id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM accounts WHERE account_id=?;");
	$stmt->bind_param("i",$id);
	$stmt->execute();

	$result = $stmt->get_result();
	
	if(!mysqli_num_rows($result)){
        return 0;
    }

	$row = $result->fetch_assoc();
	$ret = array("name" => $row['account_name'], "perms" => $row['account_perms'], "desc" => $row['account_desc'], "balance" => get_account_balance($row['account_id']));
	
	$conn->close();
	return $ret;
}
function account_history($id, $start, $end){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT invoice_id,user_id,payment_amount,payment_date FROM payments WHERE payment_type=4 AND payment_identifier=? AND payment_date BETWEEN ? AND ? ORDER BY payment_date;");
	$stmt->bind_param("iss",$id,$start,$end);
	$stmt->execute();

	$result = $stmt->get_result();
	$ret = array();

	while($row = $result->fetch_assoc()){
		array_push($ret,array("invoice" => get_invoice($row['invoice_id']), "user" => $row['user_id'], "amount" => $row['payment_amount'], "date" => $row['payment_date']));
	}
	return $ret;
}

function create_account($name,$perms,$desc){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("INSERT INTO accounts (account_name,account_perms,account_desc) VALUES (?,?,?);");
	$stmt->bind_param("sis",$name,$perms,$desc);
	$stmt->execute();
	$stmt = $conn->prepare("SELECT LAST_INSERT_ID();");
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$id = $row['LAST_INSERT_ID()'];
	$conn->close();
	return $id;
}
function get_account_balance($id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT payment_amount,invoice_id FROM payments WHERE payment_type=4 AND payment_identifier=?;");
	$stmt->bind_param("s",$id);
	$stmt->execute();
	$bal = 0;
	$result = $stmt->get_result();

	while($row = $result->fetch_assoc()){
		$inv = get_invoice($row['invoice_id']);
		if($inv['type'] == 1)
			$bal += $row['payment_amount'];
		else if($inv['type'] == 2)
			$bal -= $row['payment_amount'];
	}
	$conn->close();
	return $bal;
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
	if($type != 0){
		if($balance < $amount){
			return 1;
		}
	}else{
		if(abs($balance - $amount) > 2){
			return 1;
		}
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
	if($type == 0){
		// Paying with cash
		if(get_cash($identifier) == NULL){
			return 3; // Not coming from a valid location
		}
		if(!adjust_cash($identifier, $amount * -1)){
			return 4; // Insufficient balance in location
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
