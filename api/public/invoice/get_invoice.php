<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['invoice_id'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = (int) $_REQUEST['invoice_id'];

$invoice = get_invoice($id);
$array = array("notes" => $invoice[0], "original_id" => $invoice[1], "store" => $invoice[2], "timestamp" => $invoice[3]);
die(json_encode($array));

?>
