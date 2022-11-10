<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

/*

Stock codes:

0 - Stock
1 - Nonstock
2 - Custom (Mass Produced)
3 - Custom (One Off)
4 - Custom (External Vendor)
5 - Pseudo Item
6 - Damaged Item

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
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("i",$product_id);
	if(!$stmt->execute()){ sql_error($conn); }

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return 0;
	}
	$row = $result->fetch_assoc();
	$return = array("name" => $row['product_name'],"description" => $row['product_desc'],"count" => $row['stock_count'],"notes" => $row['stock_notes'], "code" => $row['stock_code'], "damaged" => $row['damaged_count'], "id" => $product_id, "location" => $row['stock_location'], "average_price" => get_avg_price($product_id));

	$conn->close();
	return $return;
}

function get_product_history($id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT `invoice_id` FROM `invoice_entries` WHERE `product_id` = ? ORDER BY invoice_id DESC LIMIT 20;");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("i",$id);
	if(!$stmt->execute()){ sql_error($conn); }

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return array();
	}
	$ret = array();
	while($row = $result->fetch_assoc()){
		array_push($ret,get_invoice($row['invoice_id']));
	}
	return $ret;
}
function get_avg_price($id){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT `entry_unit_price`,`unit_count` FROM `invoice_entries` WHERE `product_id` = ?;");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("i",$id);
	if(!$stmt->execute()){ sql_error($conn); }
	$result = $stmt->get_result();
	$count = mysqli_num_rows($result);
	if(!$count){
		return 0;
	}
	$total = 0;
	while($row = $result->fetch_assoc()){
		$price = $row['entry_unit_price']/$row['unit_count'];
		if($price >= -1 && $price <= 1){
			$count--;
			continue;
		}
		$total += $price;
	}
	if($count == 0){
		return 0;
	}
	return $total/$count;
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
		if(!$stmt){ sql_error($conn); }
		$stmt->bind_param("iii",$param,$offset,$limit);
	}else if($type == 2){
		// Search by name
		$param = "%".$param."%";
		$stmt = $conn->prepare("SELECT product_id FROM products WHERE product_name LIKE ? AND product_id > ? LIMIT ?;");
		if(!$stmt){ sql_error($conn); }
		$stmt->bind_param("sii",$param,$offset,$limit);
	}else if($type == 3){
		// Search by location
		// TODO: Implement this
		return NULL;
	}else if($type == 4){
		// Search by description
		$param = "%".$param."%";
		$stmt = $conn->prepare("SELECT product_id FROM products WHERE product_desc LIKE ? AND product_id > ? LIMIT ?;");
		if(!$stmt){ sql_error($conn); }
		$stmt->bind_param("sii",$param,$offset,$limit);
	}else{
		$conn->close();
		return NULL;
	}
	if(!$stmt->execute()){ sql_error($conn); }

	$result = $stmt->get_result();
	
	$return = array();

	while($row = $result->fetch_assoc()){
		array_push($return,$row['product_id']);
	}
	
	$conn->close();
	return $return;
}
function adjust_stock($id, $adj){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = $conn->prepare("SELECT stock_count FROM products WHERE product_id=?;");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("i",$id);
	if(!$stmt->execute()){ sql_error($conn); }
	$curr = $stmt->get_result()->fetch_assoc()['stock_count'];
	$new = $curr+$adj;
	$stmt = $conn->prepare("UPDATE products SET stock_count=? WHERE product_id=?;");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("ii",$new,$id);
	if(!$stmt->execute()){ sql_error($conn); }
	return $new;
}
function modify_product($id,$name,$desc,$notes,$type,$loc){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE products SET product_name=?,product_desc=?,stock_notes=?,stock_code=?,stock_location=? WHERE product_id=?;");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("sssisi",$name,$desc,$notes,$type,$loc,$id);
	if(!$stmt->execute()){ sql_error($conn); }

	$conn->close();
}
function create_product($name,$desc,$notes,$type,$loc){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}

	$stmt = $conn->prepare("INSERT INTO products (product_name, product_desc, stock_notes, stock_code, stock_location) VALUES (?,?,?,?,?)");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("sssis",$name,$desc,$notes,$type,$loc);
	if(!$stmt->execute()){ sql_error($conn); }
	$stmt = $conn->prepare("SELECT LAST_INSERT_ID();");
	if(!$stmt){ sql_error($conn); }
	if(!$stmt->execute()){ sql_error($conn); }
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
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("ii",$value,$id);
	if(!$stmt->execute()){ sql_error($conn); }
	return 1;
}
function adjust_damaged($id,$count){
	$product = get_product($id);
	return set_damaged($id,$product['damaged']+$count);
}
function set_damaged($id, $value){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("UPDATE products SET damaged_count=? WHERE product_id=?;");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("ii",$value,$id);
	if(!$stmt->execute()){ sql_error($conn); }
	return 1;
}
?>
