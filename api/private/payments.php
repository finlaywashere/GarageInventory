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
	if($paid < $total){
		return $total-$paid;
	}
	return 0;
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

function payment_create($user, $invoice, $amount, $type, $customer, $identifier){
	$balance = payment_balance($invoice);
	if($balance < $amount){
		return 1;
	}
	$conn = db_connect("inventory");
	if(!$conn){
		return 2;
	}
	$stmt = $conn->prepare("INSERT INTO payments (user_id,invoice_id,payment_amount,payment_type,customer_id,payment_identifier) VALUES (?,?,?,?,?,?);");
	$stmt->bind_param("iiiiis",$user,$invoice,$amount,$type,$customer,$identifier);
	$stmt->execute();
	$conn->close();
	return 0;
}

?>
