<?php

/*

Damaged codes

0 - New
1 - In progress
5 - Resolved (Repair)
6 - Resolved (As is)
7 - Resolved (RMA)


*/

function damaged_create($product,$desc){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("INSERT INTO damaged (product_id,damaged_desc) VALUES (?,?);");
	$stmt->bind_param("is",$product,$desc);
	$stmt->execute();
	$stmt = $conn->prepare("SELECT LAST_INSERT_ID();");
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$id = $row['LAST_INSERT_ID()'];
	$conn->close();
	return $id;
}
function damaged_resolve($id, $status){
	if(!damaged_status($id,$status))
		return 1;
	$dmg = get_damaged($id);
	if(!$dmg)
		return 2;
	$prod = get_product($dmg['product']);
	if(!$prod)
		return 3;
	if(!set_damaged($prod['id'],$prod['damaged']-1)
		return 4;
	if(!set_inventory($prod['id'],$prod['count']+1)
		return 5;
	return 0;
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
function damaged_retrieve(){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT damaged_id FROM damaged WHERE damaged_status != 2;"
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
    $stmt = $conn->prepare("SELECT * FROM damaged WHERE damaged_status < 5;"
    $stmt->execute();

    $result = $stmt->get_result();
	$row = $result->fetch_assoc();
    $return = array("id" => $row['damaged_id'], "product" => $row['product_id'], "due" => $row['damaged_due'], "desc" => $row['damaged_desc'], "status" => $row['damaged_status']);
    $conn->close();
	return $return;
}

?>
