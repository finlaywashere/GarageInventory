<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

/*
* Journal ids
* 1 = Invoice
* 2 = Customer
* 3 = Product
* 4 = Journal
* 5 = Administrative
* 6 = Security
* 7 = Override
*
* Journal types
* 1 = Create
* 2 = Modify
* 3 = Delete
*
* Note: For all transactions that don't touch an invoice, use the product_id or customer_id for the data
*/

function journal_log($type, $text, $id, $ref, $user){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("INSERT INTO `journal` (journal_type, journal_text, journal_id, journal_ref, journal_user) VALUES (?,?,?,?,?);");
	$stmt->bind_param("isiis",$type,$text,$id,$ref,$user);
	$stmt->execute();
	$conn->close();
}
function journal_get($uid){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM `journal` WHERE `journal_uid` = ?;");
	$stmt->bind_param("i",$uid);
	$stmt->execute();
	
	$result = $stmt->get_result();
    if(!mysqli_num_rows($result)){
        return NULL;
    }
	$row = $result->fetch_assoc();
	$ret = array("date" => $row['journal_date'], "type" => $row['journal_type'], "text" => $row['journal_text'], "journal_id" => $row['journal_id'], "ref" => $row['journal_ref'], "user" => $row['journal_user']);

	$conn->close();
	return $ret;
}
function journal_search($stype, $value){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = NULL;
	if($stype == 1){
		// Search by invoice
		$stmt = $conn->prepare("SELECT journal_uid FROM journal WHERE journal_invoice = ?;");
		$stmt->bind_param("i",$value);
	}else if($stype == 2){
		// Search by date
		$stmt = $conn->prepare("SELECT journal_uid FROM journal WHERE DATE(journal_date) = ?;");
		$stmt->bind_param("s",$value);
	}else if($stype == 3){
		// Search by contents
		$value = "%".$value."%";
		$stmt = $conn->prepare("SELECT journal_uid FROM journal WHERE journal_text LIKE ?;");
		$stmt->bind_param("s",$value);
	}else if($stype == 4){
		// Search by journal type
		$stmt = $conn->prepare("SELECT journal_uid FROM journal WHERE journal_id = ?;");
		$stmt->bind_param("i",$value);
	}else if($stype == 5){
		// Search by user
		$stmt = $conn->prepare("SELECT journal_uid FROM journal WHERE journal_user = ?;");
		$stmt->bind_param("s",$value);
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
		array_push($return,$row['journal_uid']);
	}
	$conn->close();
	return $return;
}
function get_journal($id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM journal WHERE journal_uid=?;");
	$stmt->bind_param("i",$id);
	$stmt->execute();

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return 0;
	}
	$row = $result->fetch_assoc();
	$return = array($row['journal_date'],$row['journal_type'],$row['journal_text'],$row['journal_id'],$row['journal_invoice'],$row['journal_uid']);

	$conn->close();
	return $return;

}

?>
