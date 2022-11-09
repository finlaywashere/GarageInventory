<?php

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request("inventory/customer");
if(!$auth){
	http_response_code(401);
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('search_type') || !req_param('search_param')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$offset = 0;
$limit = 20;
if(req_param_i('search_offset')){
	$offset = req_get('search_offset');
}
if(req_param_i('search_limit')){
	$limit = req_get('search_limit');
}
$stype = req_get('search_type'); // Type doesn't need to be validated because customer_search returns NULL with an invalid type
$param = req_get('search_param');

$customers = customer_search($stype, $param, $offset, $limit);
if($customers === NULL){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_type')));
}

die(json_encode(array('success' => true, 'customers' => $customers)));

?>

