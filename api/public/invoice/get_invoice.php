<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('invoice_id')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = req_get('invoice_id');

$invoice = get_invoice($id);
if(!$invoice){
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
die(json_encode(array('success' => true,'invoice' => $invoice)));

?>
