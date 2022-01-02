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
	$return = array("name" => $row['product_name'],"description" => $row['product_desc'],"count" => $row['stock_count'],"location" => $row['stock_location'],"notes" => $row['stock_notes']);

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
function modify_product($id,$name,$desc,$notes,$location){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE products SET product_name=?,product_desc=?,stock_notes=?,stock_location=? WHERE product_id=?;");
	$stmt->bind_param("ssssi",$name,$desc,$notes,$location,$id);
	$stmt->execute();

	$conn->close();
}
function create_product($name,$desc,$notes,$loc){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}

	$stmt = $conn->prepare("INSERT INTO products (product_name, product_desc, stock_notes, stock_location) VALUES (?,?,?,?)");
	$stmt->bind_param("ssss",$name,$desc,$notes,$loc);
	$stmt->execute();
	$stmt = $conn->prepare("SELECT LAST_INSERT_ID();");
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$id = $row['LAST_INSERT_ID()'];
	$conn->close();
	return $id;
}
function adjust_stock($id, $adj){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT stock_count FROM products WHERE product_id=?;");
	$stmt->bind_param("i",$id);
	$stmt->execute();
	$curr = $stmt->get_result()->fetch_assoc()['stock_count'];
	$new = $curr+$adj;
	$stmt = $conn->prepare("UPDATE products SET stock_count=? WHERE product_id=?;");
	$stmt->bind_param("ii",$new,$id);
	$stmt->execute();
	return $new;
}
?>
