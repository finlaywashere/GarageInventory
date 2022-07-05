<?php

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";

function get_cash($id){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = $conn->prepare("SELECT * FROM cash WHERE cash_id=?;");
	$stmt->bind_param("i",$id);
	$stmt->execute();

	$result = $stmt->get_result();
	if(!mysqli_num_rows($result)){
		return NULL;
	}
	$row = $result->fetch_assoc();
	$nickels = $row['cash_nickels'];
	$dimes = $row['cash_dimes'];
	$quarters = $row['cash_quarters'];
	$loonies = $row['cash_loonies'];
	$toonies = $row['cash_toonies'];
	$fives = $row['cash_fives'];
	$tens = $row['cash_tens'];
	$twenties = $row['cash_twenties'];
	$fifties = $row['cash_fifties'];
	$hundreds = $row['cash_hundreds'];
	
	$total = $nickels * 5 + $dimes * 10 + $quarters * 25 + $loonies * 100 + $toonies * 200 + $fives * 500 + $tens * 1000 + $twenties * 2000 + $fifties * 5000 + $hundreds * 10000;

	$return = array("name" => $row['cash_name'], "total" => $total, "counts" => array("nickels" => $nickels, "dimes" => $dimes, "quarters" => $quarters, "loonies" => $loonies, "toonies" => $toonies, "fives" => $fives, "tens" => $tens, "twenties" => $twenties, "fifties" => $fifties, "hundreds" => $hundreds));
	$conn->close();
	return $return;
}
function set_cash($id, $nickels, $dimes, $quarters, $loonies, $toonies, $fives, $tens, $twenties, $fifties, $hundreds){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = $conn->prepare("UPDATE cash SET cash_nickels=?,cash_dimes=?,cash_quarters=?,cash_loonies=?,cash_toonies=?,cash_fives=?,cash_tens=?,cash_twenties=?,cash_fifties=?,cash_hundreds=? WHERE cash_id=?;");
	$stmt->bind_param("iiiiiiiiiii",$nickels,$dimes,$quarters,$loonies,$toonies,$fives,$tens,$twenties,$fifties,$hundreds,$id);
	$stmt->execute();
	$conn->close();
}
function count_cash($id, $nickels, $dimes, $quarters, $loonies, $toonies, $fives, $tens, $twenties, $fifties, $hundreds, $user){
	$conn = db_connect("inventory");
	if(!$conn){
		return NULL;
	}
	$stmt = $conn->prepare("INSERT INTO cash_counts (cash_id, cash_nickels, cash_dimes, cash_quarters, cash_loonies, cash_toonies, cash_fives, cash_tens, cash_twenties, cash_fifties, cash_hundreds, cash_user) VALUES (?,?,?,?,?,?,?,?,?,?,?,?);");
	$stmt->bind_param("iiiiiiiiiiii",$id,$nickels,$dimes,$quarters,$loonies,$toonies,$fives,$tens,$twenties,$fifties,$hundreds,$user);
	$stmt->execute();
	$conn->close();
	set_cash($id,$nickels,$dimes,$quarters,$loonies,$toonies,$fives,$tens,$twenties,$fifties,$hundreds);
}
function cash_create($name){
	$conn = db_connect("inventory");
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("INSERT INTO cash (cash_name) VALUES (?);");
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
function cash_move($src, $dst, $nickels, $dimes, $quarters, $loonies, $toonies, $fives, $tens, $twenties, $fifties, $hundreds){
	$scash = get_cash($src)['counts'];
	$dcash = get_cash($dst)['counts'];
	if($scash['nickels'] - $nickels < 0 || $scash['dimes'] - $dimes < 0 || $scash['quarters'] - $quarters < 0 || $scash['loonies'] - $loonies < 0 || $scash['toonies'] - $toonies < 0 || $scash['fives'] - $fives < 0 || $scash['tens'] - $tens < 0 || $scash['twenties'] - $twenties < 0 || $scash['fifties'] - $fifties < 0 || $scash['hundreds'] - $hundreds < 0){
		return 0;
	}
	if($dcash['nickels'] + $nickels < 0 || $dcash['dimes'] + $dimes < 0 || $dcash['quarters'] + $quarters < 0 || $dcash['loonies'] + $loonies < 0 || $dcash['toonies'] + $toonies < 0 || $dcash['fives'] + $fives < 0 || $dcash['tens'] + $tens < 0 || $dcash['twenties'] + $twenties < 0 || $dcash['fifties'] + $fifties < 0 || $dcash['hundreds'] + $hundreds < 0){
        return 0;
    }
	set_cash($src,$scash['nickels']-$nickels,$scash['dimes']-$dimes,$scash['quarters']-$quarters,$scash['loonies']-$loonies,$scash['toonies']-$toonies,$scash['fives']-$fives,$scash['tens']-$tens,$scash['twenties']-$twenties,$scash['fifties']-$fifties,$scash['hundreds']-$hundreds);
	set_cash($dst,$dcash['nickels']+$nickels,$dcash['dimes']+$dimes,$dcash['quarters']+$quarters,$dcash['loonies']+$loonies,$dcash['toonies']+$toonies,$dcash['fives']+$fives,$dcash['tens']+$tens,$dcash['twenties']+$twenties,$dcash['fifties']+$fifties,$dcash['hundreds']+$hundreds);
	return 1;
}

?>
