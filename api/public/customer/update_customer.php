<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(4);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('customer_id') || !req_param('name') || !req_param('email') || !req_param('phone') || !req_param('address') || !req_param_i('type') || !req_param('notes')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_customer')));
}
$id = req_get('customer_id');
$name = req_get('name');
$email = req_get('email');
$phone = req_get('phone');
$address = req_get('address');
$type = req_get('type');
$notes = req_get('notes');

update_customer($id,$name,$email,$phone,$address,$type,$notes);

journal_log(2,"Customer ".$customer." updated",1,0,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true)));

?>
