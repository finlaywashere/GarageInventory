<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

$auth = authenticate_request(4);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['customer_id']) || !isset($_REQUEST['name']) || !isset($_REQUEST['email']) || !isset($_REQUEST['phone']) || !isset($_REQUEST['address']) || !isset($_REQUEST['type']) || !isset($_REQUEST['notes'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_customer')));
}
$id = $_REQUEST['customer_id'];
$name = $_REQUEST['name'];
$email = $_REQUEST['email'];
$phone = $_REQUEST['phone'];
$address = $_REQUEST['address'];
$type = $_REQUEST['type'];
$notes = $_REQUEST['notes'];

update_customer($id,$name,$email,$phone,$address,$type,$notes);

journal_log(2,"Customer ".$customer." updated",1,0,get_username());

die(json_encode(array('success' => true)));

?>
