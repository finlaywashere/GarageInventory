<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(100);
if(!$auth){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('name')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_name')));
}
$name = req_get('name');

$id = cash_create($name);
if($id == 0){
	die(json_encode(array('success' => false, 'reason' => 'error')));
}
die(json_encode(array('success' => true,'id' => $id)));

?>
