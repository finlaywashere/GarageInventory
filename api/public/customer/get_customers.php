<?php

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(3);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('search_type') || !req_param('search_param')){
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
$stype = req_get('search_type');
$param = req_get('search_param');

$customers = customer_search($stype, $param, $offset, $limit);
if($customers === NULL){
	die(json_encode(array('success' => false, 'reason' => 'invalid_type')));
}

die(json_encode(array('success' => true, 'customers' => $customers)));

?>

