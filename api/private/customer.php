<?php
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

function get_customer($id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM `customers` WHERE `customer_id`=?;");
	$stmt->bind_param("i",$id);
	$stmt->execute();

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return 0;
	}
	$row = $result->fetch_assoc();
	$return = array("notes" => $row['customer_notes'], "name" => $row['customer_name'], "email" => $row['customer_email'], "phone" => $row['customer_phone'], "address" => $row['customer_address'], "type" => $row['customer_type']);

	$conn->close();
	return $return;
}
function create_customer($name, $email, $phone, $address, $type, $notes){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("INSERT INTO `customers` (customer_name,customer_email,customer_phone,customer_address,customer_type,customer_notes) VALUES (?,?,?,?,?,?);");
	$stmt->bind_param("ssssss",$name,$email,$phone,$address,$type,$notes);
	$stmt->execute();
	$stmt = $conn->prepare("SELECT LAST_INSERT_ID();");
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$id = $row['LAST_INSERT_ID()'];
	$conn->close();
	return $id;
}
function update_customer($id, $name, $email, $phone, $address, $type, $notes){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE `customers` SET customer_name=?,customer_email=?,customer_phone=?,customer_address=?,customer_type=?,customer_notes=? WHERe customer_id=?;");
	$stmt->bind_param("ssssssi",$name,$email,$phone,$address,$type,$notes,$id);
	$stmt->execute();
	$conn->close();
}

?>
