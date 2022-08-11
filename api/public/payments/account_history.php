<?php

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}

if(!req_param_i('id') || !req_param('start') || !req_param('end')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}

$id = req_get('id');
$start = req_get('start');
$end = req_get('end');

$acc = get_account($id);

if(!$acc){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}

if(!authenticate_request($acc['perms'])){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}

$hist = account_history($id,$start,$end);

die(json_encode(array('success' => true, 'history' => $hist)));

?>

