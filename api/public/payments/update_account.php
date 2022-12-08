<?php

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request("inventory/account/admin");
if(!$auth){
	http_response_code(401);
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}

if(!req_param_i('account')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}

$id = req_get('account');
$name = NULL;
if(req_param('name'))
	$name = req_get('name');
$perms = NULL;
if(req_param_i('perms'))
	$perms = req_get('perms');
$desc = NULL;
if(req_param('desc'))
	$desc = req_get('desc');

if(!authenticate_request(get_account($id)['perms'])){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_perms')));
}

$result = update_account($id, $name,$desc,$perms);

if(!$result){
	http_response_code(500);
	die(json_encode(array('success' => false, 'reason' => 'internal_error')));
}

journal_log(2,"Account ".$id." modified",10,$result,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true)));

?>
