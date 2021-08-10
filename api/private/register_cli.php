<?php

require_once "authentication.php";

echo "Enter username: ";
$username = fgets(STDIN);
echo "Enter password: ";
$password = fgets(STDIN);
echo "Enter permission level: ";
$perms = (int) fgets(STDIN);
echo "Enter email: ";
$email = fgets(STDIN);

$result = register($username,$password,$email,$perms);

if($result){
	die("Successfully registered user!");
}else{
	die("Failed to register user, error code ".$result);
}

?>
