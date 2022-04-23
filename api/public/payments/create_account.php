<?php

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(100);
if(!$auth){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}

if(!req_param('name') || !req_param_i('perms')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$name = req_get('name');
$perms = req_get('perms');
$desc = "";
if(req_param('desc'))
	$desc = req_get('desc');

if(!authenticate_request($perms)){
	die(json_encode(array('success' => false, 'reason' => 'invalid_perms')));
}

$result = create_account($name,$perms,$desc);

die(json_encode(array('success' => true, 'account' => $result)));

?>
