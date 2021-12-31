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
if(!isset($data->{'paid'}) || !isset($data->{'subtotal'}) || !isset($data->{'total'}) || !isset($data->{'notes'}) || !isset($data->{'customer'}) || !isset($data->{'type'}) || !isset($data->{'entries'}) || !isset($data->{'orig_id'}) || !isset($data->{'date'})){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}
$paid = $data->{'paid'};
$subtotal = $data->{'subtotal'};
$total = $data->{'total'};
$notes = $data->{'notes'};
$customer = $data->{'customer'};
$type = $data->{'type'};
$entries = $data->{'entries'};
$date = $data->{'date'};
$orig_id = $data->{'orig_id'};

for($i = 0; $i < count($entries); $i++){
	if(!isset($entries[$i]->{'product'}) || !isset($entries[$i]->{'orig'}) || !isset($entries[$i]->{'count'}) || !isset($entries[$i]->{'unit_count'}) || !isset($entries[$i]->{'unit_price'}) || !isset($entries[$i]->{'unit_discount'}) || !isset($entries[$i]->{'notes'})){
		die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
	}
}

$invoice = invoice_create($subtotal,$total,$customer,$type,$notes,$entries,$paid, $orig_id, $date);

journal_log(1,"Invoice ".$invoice." created",1,0,get_username());

die(json_encode(array('success' => true,'invoice' => $invoice)));

?>
