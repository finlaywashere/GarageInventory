<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request("inventory/customer/create");
if(!$auth){
	http_response_code(401);
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('name') || !req_param('phone') || !req_param_i('type') || !req_param('notes')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$name = req_get('name');
$email = "";
$address = "";
if(req_param('email'))
	$email = req_get('email');
if(req_param('address'))
	$address = req_get('address');
$phone = req_get('phone');
$type = req_get('type');
$notes = req_get('notes');

if($email != "" && !validate_email($email)){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_email')));
}
if($type == 0 && !authenticate_request("inventory/admin")){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if($type < 0 || $type > 2){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_type')));
}
if(!validate_phone($phone)){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_phone')));
}
if(!validate_name($name)){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_name')));
}

$customer = create_customer($name,$email,$phone,$address,$type,$notes);

journal_log(1,"Customer ".$customer." created",2,$customer,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true,'customer' => $customer)));

?>
