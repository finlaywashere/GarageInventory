<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(4);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('name') || !req_param('email') || !req_param('phone') || !req_param('address') || !req_param_i('type') || !req_param('notes')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_customer')));
}
$name = req_get('name');
$email = req_get('email');
$phone = req_get('phone');
$address = req_get('address');
$type = req_get('type');
$notes = req_get('notes');

$customer = create_customer($name,$email,$phone,$address,$type,$notes);

journal_log(1,"Customer ".$customer." created",2,$customer,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true,'customer' => $customer)));

?>
