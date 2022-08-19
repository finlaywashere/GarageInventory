<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(100);
if(!$auth){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('src_id') || !req_param_i('dst_id') || !req_param_i('amount')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$src = req_get('src_id');
$dst = req_get('dst_id');
$amount = req_get('amount');

$result = cash_move($src,$dst,$amount);
if($result){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}
journal_log(2,"$".$amount." cash moved from ".$src." to ".$dst,11,$src,get_username(),$_SERVER['REMOTE_ADDR']);
die(json_encode(array('success' => true)));

?>
