<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(3);
if(!$auth){
	http_response_code(401);
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('product') || !req_param('desc')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}

$id = req_get('product');
$desc = req_get('desc');
$inv = null;

if(req_param_i('invoice')){
	$inv = req_get('invoice');
}

$damaged = damaged_create($id,$desc,get_user_id(get_username()),$inv);

if(!$damaged){
	http_response_code(500);
	die(json_encode(array('success' => false, 'reason' => 'internal_error')));
}
journal_log(1,"Damaged entry ".$damaged." created",8,$damaged,get_username(),$_SERVER['REMOTE_ADDR']);
die(json_encode(array('success' => true,'damaged' => $damaged)));

?>
