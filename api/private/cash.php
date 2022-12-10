<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

function get_cash($id){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = $conn->prepare("SELECT * FROM cash WHERE cash_id=?;");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("i",$id);
	if(!$stmt->execute()){ sql_error($conn); }

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return NULL;
	}
	$row = $result->fetch_assoc();
	
	$counts = get_cash_counts($id,0,1);
	$last_count = NULL;
	if($counts != NULL && count($counts) > 0)
		$last_count = $counts[0];

	$return = array("name" => $row['cash_name'], "total" => $row['cash_amount'], "last_count" => $last_count);
	$conn->close();
	return $return;
}
function get_cash_counts($id, $offset, $limit){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = $conn->prepare("SELECT * FROM cash_counts WHERE cash_id=? ORDER BY cash_timestamp DESC LIMIT ?,?;");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("iii",$id,$offset,$limit);
	if(!$stmt->execute()){ sql_error($conn); }

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return NULL;
	}
	$ret = array();
	while($row = $result->fetch_assoc()){
		array_push($ret,array("timestamp" => $row['cash_timestamp'], "nickels" => $row['cash_nickels'], "dimes" => $row['cash_dimes'], "quarters" => $row['cash_quarters'], "loonies" => $row['cash_loonies'], "toonies" => $row['cash_toonies'], "fives" => $row['cash_fives'], "tens" => $row['cash_tens'], "twenties" => $row['cash_twenties'], "fifties" => $row['cash_fifties'], "hundreds" => $row['cash_hundreds'], "user" => get_user($row['cash_user'])));
	}
	$conn->close();
	return $ret;
}
function get_cash_locations(){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = $conn->prepare("SELECT cash_id FROM `cash` WHERE 1;");
	if(!$stmt){ sql_error($conn); }
	if(!$stmt->execute()){ sql_error($conn); }
	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return array();
	}
	$ret = array();
	while($row = $result->fetch_assoc()){
		$ret[$row['cash_id']] = get_cash($row['cash_id']);
	}
	$conn->close();
	return $ret;
}
function adjust_cash($id, $amount){
	$cash = get_cash($id);
	$result = $cash['total'] + $amount;
	if($result < 0){
		return 0;
	}
	set_cash($id,$result);
	return 1;
}
function set_cash($id, $total){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = $conn->prepare("UPDATE cash SET cash_amount=? WHERE cash_id=?;");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("ii",$total,$id);
	if(!$stmt->execute()){ sql_error($conn); }
	$conn->close();
}

function cash_total($nickels,$dimes,$quarters,$loonies,$toonies,$fives,$tens,$twenties,$fifties,$hundreds){
	return $nickels * 5 + $dimes * 10 + $quarters * 25 + $loonies * 100 + $toonies * 200 + $fives * 500 + $tens * 1000 + $twenties * 2000 + $fifties * 5000 + $hundreds * 10000;
}
function count_cash($id, $nickels, $dimes, $quarters, $loonies, $toonies, $fives, $tens, $twenties, $fifties, $hundreds, $user){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$cash = get_cash($id);
	$total = cash_total($nickels,$dimes,$quarters,$loonies,$toonies,$fives,$tens,$twenties,$fifties,$hundreds);
	if($cash['total'] != $total){
		return 1;
	}
	$stmt = $conn->prepare("INSERT INTO cash_counts (cash_id, cash_nickels, cash_dimes, cash_quarters, cash_loonies, cash_toonies, cash_fives, cash_tens, cash_twenties, cash_fifties, cash_hundreds, cash_user) VALUES (?,?,?,?,?,?,?,?,?,?,?,?);");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("iiiiiiiiiiii",$id,$nickels,$dimes,$quarters,$loonies,$toonies,$fives,$tens,$twenties,$fifties,$hundreds,$user);
	if(!$stmt->execute()){ sql_error($conn); }
	$conn->close();
	set_cash($id,$total);
	return 0;
}
function cash_create($name){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("INSERT INTO cash (cash_name) VALUES (?);");
	if(!$stmt){ sql_error($conn); }
	$stmt->bind_param("s",$name);
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
function cash_move($src, $dst, $amount){
	if($amount < 0){
		return 1;
	}
	$scash = get_cash($src)['total'];
	$dcash = get_cash($dst)['total'];
	if($scash - $amount < 0){
		return 2;
	}
	if($dcash + $amount < 0){
		return 3;
	}
	set_cash($src,$scash-$amount);
	set_cash($dst,$dcash+$amount);
	return 0;
}

?>
