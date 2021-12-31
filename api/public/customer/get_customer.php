<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

$auth = authenticate_request(3);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['customer_id'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = (int) $_REQUEST['customer_id'];

$customer = get_customer($id);
if($customer == null){
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
die(json_encode(array('success' => true,'customer' => $customer)));

?>
