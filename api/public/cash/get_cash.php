<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
	http_response_code(401);
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('cash_id')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = req_get('cash_id');

$cash = get_cash($id);
if(!$cash){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
die(json_encode(array('success' => true,'cash' => $cash)));

?>
