<?php

// Marks a damaged item entry as being in progress

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request("inventory_damaged");
if(!$auth){
	http_response_code(401);
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('id')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}

$id = req_get('id');

$success = damaged_status($id,1);

if(!$success){
	http_response_code(500);
	die(json_encode(array('success' => false, 'reason' => 'internal_error')));
}

journal_log(2,"Damaged entry ".$id." marked as in progress",8,$id,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true)));

?>
