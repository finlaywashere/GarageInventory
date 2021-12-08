<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

/**
	Gets all the information on file about a product from its product id
*/
function get_product($product_id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM `products` WHERE `product_id`=?;");
	$stmt->bind_param("i",$product_id);
	$stmt->execute();

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return 0;
	}
	$row = $result->fetch_assoc();
	$return = array("original_id" => $row['original_id'],"name" => $row['product_name'],"description" => $row['product_desc'],"count" => $row['stock_count'],"location" => $row['stock_location'],"notes" => $row['stock_notes']);

	$conn->close();
	return $return;
}
/**
	Gets the product id's of every product in the database (with search params)
*/
function get_products($type, $param){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = NULL;
	if($type == 1){
		// Search by ID
		$stmt = $conn->prepare("SELECT product_id FROM products WHERE product_id=?;");
		$stmt->bind_param("i",$param);
	}else if($type == 2){
		// Search by name
		$param = "%".$param."%";
		$stmt = $conn->prepare("SELECT product_id FROM products WHERE product_name LIKE ?;");
		$stmt->bind_param("s",$param);
	}else if($type == 3){
		// Search by location
		$param = "%".$param."%";
		$stmt = $conn->prepare("SELECT product_id FROM products WHERE stock_location LIKE ?;");
		$stmt->bind_param("s",$param);
	}else if($type == 4){
		// Search by description
		$param = "%".$param."%";
		$stmt = $conn->prepare("SELECT product_id FROM products WHERE product_desc LIKE ?;");
		$stmt->bind_param("s",$param);
	}else{
		$conn->close();
		return NULL;
	}
	$stmt->execute();

	$result = $stmt->get_result();
	
	$return = array();

	while($row = $result->fetch_assoc()){
		array_push($return,$row['product_id']);
	}
	
	$conn->close();
	return $return;
}

/**
	Helper function to update product values
*/
function update_product($product_id, $column_name, $column_value){
	return update_value("products","product_id",$product_id,$column_name,$column_value);
}

/**
	A whole bunch of helper functions to update various fields
*/
function set_product_info($product_id, $name,$desc){
	$result = update_product($product_id,"product_name",$name);
	if(!$result){
		return $result;
	}
	return update_product($product_id,"product_desc",$desc);
}
function set_stock_info($product_id, $count,$location,$notes=null){
	$result = update_product($product_id,"stock_count",$count);
	if(!$result){
		return $result;
	}
	$result = update_product($product_id,"stock_location",$location);
	if(!result){
		return $result;
	}
	if(isset($notes)){
		return update_product($product_id,"stock_notes",$notes);
	}else{
		return $result;
	}
}


?>
