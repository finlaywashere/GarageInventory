<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('product_id')){
    die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$id = req_get('product_id');
$report = product_report($id,get_user_id(get_username()));

if(!$report){
	die(json_encode(array('success' => false, 'reason' => 'invalid_product')));
}

die(json_encode(array('success' => true, 'report' => $report)));

?>
