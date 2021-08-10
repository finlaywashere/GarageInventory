<?php

require_once "db.php";

function generate_token(
	int $length = 64,
	string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
	if ($length < 1) {
		throw new \RangeException("Length must be a positive integer");
	}
	$pieces = [];
	$max = mb_strlen($keyspace, '8bit') - 1;
	for ($i = 0; $i < $length; ++$i) {
		$pieces []= $keyspace[random_int(0, $max)];
	}
	return implode('', $pieces);
}

function login($user,$password){
	$conn = db_connect();
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT `user_password` FROM `users` WHERE `user_username`=?;");
	$stmt->bind_param("s",$user);
	$stmt->execute();

	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	if(!$row){
		$conn->close();
		return 0;
	}
	if(!password_verify($password,$row['user_password'])){
		$conn->close();
		return 0;
	}
	$token = generate_token();
	$stmt = $conn->prepare("UPDATE `users` SET `user_token`=? WHERE `user_username`=?;");
	$stmt->bind_param("ss",$token,$user);
	$stmt->execute();

	$conn->close();
	return $token;
}
/**
	This function is designed to be used internally by authenticated users and not open to anyone
*/
function register($user,$password,$email,$perms){
	$conn = db_connect();
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT * FROM `users` WHERE `user_username`=?;");
	$stmt->bind_param("s",$user);
	$stmt->execute();

	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	if($row){
		// User exists
		$conn->close();
		return -1;
	}
	$hash = password_hash($password,PASSWORD_DEFAULT);
	$stmt = $conn->prepare("INSERT INTO `users` (user_username,user_password,user_perms,user_email) VALUES (?,?,?,?);");
	$stmt->bind_param("ssis",$user,$hash,$perms,$email);
	$stmt->execute();

	$conn->close();
	return 1;
}
function login_verify($user,$token){
	$conn = db_connect();
	if(!$conn){
		return 0;
	}
	$stmt = $conn->prepare("SELECT `user_token` FROM `users` WHERE `user_username`=?;");
	$stmt->bind_param("s",$user);
	$stmt->execute();

	$result = $stmt->get_result();
	$row = $result->fetch_assoc();

	if(!$row){
		$conn->close();
		return 0;
	}
	if($token != $row['user_token']){
		$conn->close();
		return 0;
	}

	$conn->close();
	return 1;
}

?>
