<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('product_id')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = req_get('product_id');

$history = get_product_history($id);
die(json_encode(array('success' => true, 'history' => $history)));

?>

