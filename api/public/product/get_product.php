<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
	http_response_code(401);
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('product_id')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = req_get('product_id');

$product = get_product($id);
if(!$product){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
die(json_encode(array('success' => true, 'product' => $product)));

?>

