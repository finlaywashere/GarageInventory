<?php

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}

if(!req_param_i('id')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}

$id = req_get('id');

$acc = get_account($id);

if(!$acc){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}

if(!authenticate_request($acc['perms'])){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}

die(json_encode(array('success' => true, 'account' => $acc)));

?>

