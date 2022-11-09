<?php

// Marks a damaged item entry as being in progress

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(3);
if(!$auth){
	http_response_code(401);
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('id') || !req_param_i('status')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}

$id = req_get('id');
$status = req_get('status');

if($status < 5 || $status > 7){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_status')));
}

$inv = null;

if($status == 7){
	if(!req_param_i('invoice')){
		http_response_code(400);
		die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
	}
	$inv = req_get('invoice');
}

$damaged = damaged_get($id);

if(!$damaged){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
if($damaged['status'] >= 5){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_action')));
}

$success = damaged_complete($id,$status,$inv);

if(!$success){
	http_response_code(500);
	die(json_encode(array('success' => false, 'reason' => 'internal_error')));
}

journal_log(3,"Damaged entry ".$id." marked as resolved",8,$id,get_username(),$_SERVER['REMOTE_ADDR']);

if($status != 6)
	die(json_encode(array('success' => true)));
die(json_encode(array('success' => true, 'product_id' => $success)));

?>
