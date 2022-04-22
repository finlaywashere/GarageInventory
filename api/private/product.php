<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

/*

Stock codes:

0 - Stock
1 - Nonstock
2 - Custom (Mass Produced)
3 - Custom (One Off)
4 - Custom (External Vendor)

*/

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
	$return = array("name" => $row['product_name'],"description" => $row['product_desc'],"count" => $row['stock_count'],"location" => $row['stock_location'],"notes" => $row['stock_notes'], "code" => $row['stock_code'], "damaged" => $row['damaged_count'], "id" => $product_id);

	$conn->close();
	return $return;
}

function get_product_history($id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT `unit_count`,`entry_unit_price`,`entry_discount`,`invoice_id` FROM `invoice_entries` WHERE `product_id` = ?;");
	$stmt->bind_param("i",$id);
	$stmt->execute();

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return 0;
	}
	
	$max = 0;
	$maxi = 0;
	$mini = 0;
	$min = 9999999999;
	while($row = $result->fetch_assoc()){
		$price = $row['entry_unit_price']-$row['entry_discount'];
		$price = (int) $price/$row['unit_count'];
		if($price > $max){
			$max = $price;
			$maxi = $row['invoice_id'];
		}else if($price < $min){
			$min = $price;
			$mini = $row['invoice_id'];
		}
	}
	$maxinv = get_invoice($maxi);
	$mininv = get_invoice($mini);
	$return = array('max' => array('price' => $max, 'date' => $maxinv['date'], 'invoice' => $maxi, 'customer' => get_customer($maxinv['customer'])), 'min' => array('price' => $min, 'date' => $mininv['date'], 'invoice' => $mini, 'customer' => get_customer($mininv['customer'])));
	return $return;
}
function is_product($id){
	return get_product($id) > 0;
}
/**
	Gets the product id's of every product in the database (with search params)
*/
function get_products($type, $param, $offset, $limit){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = NULL;
	if($type == 1){
		// Search by ID
		$stmt = $conn->prepare("SELECT product_id FROM products WHERE product_id=? AND product_id > ? LIMIT ?;");
		$stmt->bind_param("iii",$param,$offset,$limit);
	}else if($type == 2){
		// Search by name
		$param = "%".$param."%";
		$stmt = $conn->prepare("SELECT product_id FROM products WHERE product_name LIKE ? AND product_id > ? LIMIT ?;");
		$stmt->bind_param("sii",$param,$offset,$limit);
	}else if($type == 3){
		// Search by location
		$param = "%".$param."%";
		$stmt = $conn->prepare("SELECT product_id FROM products WHERE stock_location LIKE ? AND product_id > ? LIMIT ?;");
		$stmt->bind_param("sii",$param,$offset,$limit);
	}else if($type == 4){
		// Search by description
		$param = "%".$param."%";
		$stmt = $conn->prepare("SELECT product_id FROM products WHERE product_desc LIKE ? AND product_id > ? LIMIT ?;");
		$stmt->bind_param("sii",$param,$offset,$limit);
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
function set_inventory($id, $value){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE products SET stock_count=? WHERE product_id=?;");
	$stmt->bind_param("ii",$value,$id);
	$stmt->execute();
	return 1;
}
function set_damaged($id, $value){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE products SET damaged_count=? WHERE product_id=?;");
	$stmt->bind_param("ii",$value,$id);
	$stmt->execute();
	return 1;
}
?>
