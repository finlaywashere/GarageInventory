<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request("inventory/cash");
if(!$auth){
	http_response_code(401);
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('cash_id')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = req_get('cash_id');

$offset = 0;
$limit = 7;
if(req_param_i('cash_offset')){
	$offset = req_get('cash_offset');
}
if(req_param_i('cash_limit')){
	$limit = req_get('cash_limit');
}

$cash = get_cash_counts($id,$offset,$limit);
if(!$cash){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
die(json_encode(array('success' => true,'counts' => $cash)));

?>
