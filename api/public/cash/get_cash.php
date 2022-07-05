<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(100);
if(!$auth){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('cash_id')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = req_get('cash_id');

$cash = get_cash($id);
if(!$cash){
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
die(json_encode(array('success' => true,'cash' => $cash)));

?>
