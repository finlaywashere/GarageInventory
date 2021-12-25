<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

$auth = authenticate_request(4);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['name']) || !isset($_REQUEST['email']) || !isset($_REQUEST['phone']) || !isset($_REQUEST['address']) || !isset($_REQUEST['type']) || !isset($_REQUEST['notes'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_customer')));
}
$name = $_REQUEST['name'];
$email = $_REQUEST['email'];
$phone = $_REQUEST['phone'];
$address = $_REQUEST['address'];
$type = $_REQUEST['type'];
$notes = $_REQUEST['notes'];

$customer = create_customer($name,$email,$phone,$address,$type,$notes);

die(json_encode(array('success' => true,'customer' => $customer)));

?>
