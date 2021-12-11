<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

$auth = authenticate_request(2);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['data'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}
$data = json_decode($_REQUEST['data']);
$subtotal = $data->{'subtotal'};
$total = $data->{'total'};
$notes = $data->{'notes'};
$customer = $data->{'customer'};
$type = $data->{'type'};
$entries = $data->{'entries'};

$invoice = invoice_create($subtotal,$total,$customer,$type,$notes,$entries);
die(json_encode(array('success' => true,'invoice' => $invoice)));

?>
