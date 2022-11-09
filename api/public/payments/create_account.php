<?php

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(100);
if(!$auth){
	http_response_code(401);
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}

if(!req_param('name') || !req_param_i('perms')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$name = req_get('name');
$perms = req_get('perms');
$desc = "";
if(req_param('desc'))
	$desc = req_get('desc');

if(!authenticate_request($perms)){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_perms')));
}

$result = create_account($name,$perms,$desc);

journal_log(1,"Account ".$result." created",10,$result,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true, 'account' => $result)));

?>
