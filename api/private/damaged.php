<?php

/*

Damaged codes

0 - New
1 - In progress
5 - Resolved (Repair)
6 - Resolved (As is)
7 - Resolved (RMA)


*/

function damaged_create($product,$desc,$user,$inv = null){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("INSERT INTO damaged (product_id,damaged_desc,invoice_id,user_id) VALUES (?,?,?,?);");
	$stmt->bind_param("isii",$product,$desc,$inv,$user);
	$stmt->execute();
	$stmt = $conn->prepare("SELECT LAST_INSERT_ID();");
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$id = $row['LAST_INSERT_ID()'];
	$conn->close();
	adjust_stock($product,-1);
	adjust_damaged($product,1);
	return $id;
}
function damaged_status($id, $status){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE damaged SET damaged_status=? WHERE damaged_id=?;");
	$stmt->bind_param("ii",$status,$id);
	$stmt->execute();
	$conn->close();
	return 1;
}
function damaged_complete($id, $status, $inv=null){
	if(!damaged_status($id,$status)){
		return 0;
	}
	$conn = db_connect("inventory");
	if(!$conn)
		return 0;
	$stmt = $conn->prepare("UPDATE damaged SET damaged_due=current_timestamp(),damaged_resolution=? WHERE damaged_id=?;");
	$stmt->bind_param("ii",$inv,$id);
	$stmt->execute();
	$conn->close();
	
	$damaged = damaged_get($id);

	if($status == 5 || $status == 7){
		adjust_stock($damaged['product'],1);
		adjust_damaged($damaged['product'],-1);
	}else if($status == 6){
		$product = get_product($damaged['product']);
		adjust_damaged($damaged['product'],-1);
		$id = create_product($product['name']." (Damaged)",$product['description'],"Damaged reference #".$id,"",6);
		adjust_stock($id,1);
		return $id;
	}

	return 1;
}
function damaged_retrieve(){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT damaged_id FROM damaged WHERE damaged_status < 5;");
	$stmt->execute();

	$result = $stmt->get_result();

	$return = array();

	while($row = $result->fetch_assoc()){
		array_push($return,damaged_get($row['damaged_id']));
	}
	$conn->close();
	return $return;
}
function damaged_get($id){
	$conn = db_connect("inventory");
    if(!$conn){
        return 0;
    }
    $stmt = $conn->prepare("SELECT * FROM damaged WHERE damaged_id=?;");
	$stmt->bind_param("i",$id);
    $stmt->execute();

    $result = $stmt->get_result();

	if(!mysqli_num_rows($result)){
		return 0;
	}

	$row = $result->fetch_assoc();
    $return = array("id" => $row['damaged_id'], "product" => $row['product_id'], "due" => $row['damaged_due'], "desc" => $row['damaged_desc'], "status" => $row['damaged_status'], "created" => $row['damaged_creation'], "user" => $row['user_id'], "invoice_id" => $row['invoice_id']);
    $conn->close();
	return $return;
}

?>
