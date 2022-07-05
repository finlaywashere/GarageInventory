<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

function location_create($name){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("INSERT INTO `locations` (location_name) VALUES (?);");
	$stmt->bind_param("s",$name);
	$stmt->execute();
	$stmt = $conn->prepare("SELECT LAST_INSERT_ID();");
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$id = $row['LAST_INSERT_ID()'];
	$conn->close();
	return $id;
}

function get_location_entry($ent){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM product_locations WHERE entry_id = ?;");
	$stmt->bind_param("i",$ent);
	$stmt->execute();
	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return array();
	}
	$ret = array();
	
	while($row = $result->fetch_assoc()){
		array_push($ret,array("location" => $row['location_id'], "product" => $row['product_id'], "sublocation" => $row['location_sublocation'], "count" => $row['location_count']));
	}

	$conn->close();
	return $ret;
}

function get_product_locations($prod){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT entry_id FROM product_locations WHERE product_id = ?;");
	$stmt->bind_param("i",$prod);
	$stmt->execute();
	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return array();
	}
	$ret = array();
	
	while($row = $result->fetch_assoc()){
		$entry = get_location_entry($row['entry_id']);
		if($entry['count'] == 0) continue;
		array_push($ret,$entry);
	}

	$conn->close();
	return $ret;
}

function location_find($prod,$loc,$subloc){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT entry_id FROM product_locations WHERE product_id = ? AND location_id = ? AND location_sublocation = ?;");
	$stmt->bind_param("iis",$prod,$loc,$subloc);
	$stmt->execute();
	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return 0;
	}
	$row = $result->fetch_assoc();
	$ret = $row['entry_id'];
	$conn->close();
	return $ret;
}
function entry_count($entry, $count){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE product_locations SET location_count = ? WHERE entry_id = ?;");
	$stmt->bind_param("ii",$entry, $count);
	$stmt->execute();
	$conn->close();
}

function location_modify($prod, $loc, $subloc, $count){
	$id = location_find($prod,$loc,$subloc);
	if($id == 0){
		// Need to create a new entry
		$conn = db_connect("inventory");
		if(!$conn){
			return 0;
		}
		$stmt = $conn->prepare("INSERT INTO product_locations (location_id,product_id,location_sublocation,location_count) VALUES (?,?,?,?);");
		$stmt->bind_param("iisi",$prod,$loc,$subloc,$count);
		$stmt->execute();
	}else{
		entry_count($id,$count);
	}
}

?>
