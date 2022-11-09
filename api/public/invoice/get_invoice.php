<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request("inventory/invoice");
if(!$auth){
	http_response_code(401);
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('invoice_id')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = req_get('invoice_id');

$invoice = get_invoice($id);
if(!$invoice){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
die(json_encode(array('success' => true,'invoice' => $invoice)));

?>
