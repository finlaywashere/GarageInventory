<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request("inventory/customer/update");
if(!$auth){
	http_response_code(401);
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('customer_id')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$id = req_get('customer_id');
$name = "";
if(req_param('name')){
	$name = req_get('name');
	if(!validate_name($name)){
		http_response_code(400);
		die(json_encode(array('success' => false, 'reason' => 'invalid_name')));
	}
}
$email = "";
if(req_param('email')){
	$email = req_get('email');
}
$phone = "";
if(req_param('phone')){
	$phone = req_get('phone');
	if(!validate_phone($phone)){
		http_response_code(400);
		die(json_encode(array('success' => false, 'reason' => 'invalid_phone')));
	}
}
$address = "";
if(req_param('address'))
	$address = req_get('address');
$type = -1;
if(req_param_i('type')){
	$type = req_get('type');
	if($type < 0 || $type > 2){
		http_response_code(400);
		die(json_encode(array('success' => false, 'reason' => 'invalid_type')));
	}
}
$notes = "";
if(req_param('notes'))
	$notes = req_get('notes');

update_customer($id,$name,$email,$phone,$address,$type,$notes);

journal_log(2,"Customer ".$id." updated",2,$id,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true)));

?>
