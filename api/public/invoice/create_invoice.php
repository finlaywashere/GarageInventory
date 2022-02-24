<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(2);
if(!$auth){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('data')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}
// Its ok to directly access the request here because req_get sanitizes stuff and it could break the JSON
$data = json_decode($_REQUEST['data']);
if($data == NULL){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}
if(!json_cont_i($data,'paid',) || !json_cont_i($data,'subtotal') || !json_cont_i($data,'total') || !json_cont($data,'notes') || !json_cont_i($data,'customer') || !json_cont_i($data,'type') || !json_cont($data,'entries') || !json_cont($data,'orig_id') || !json_cont($data,'date')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}
$subtotal = (int) $data->{'subtotal'};
$total = (int) $data->{'total'};
$notes = sanitize($data->{'notes'});
$customer = (int) $data->{'customer'};
$type = (int) $data->{'type'};
$entries = $data->{'entries'};
$date = sanitize($data->{'date'});
$orig_id = sanitize($data->{'orig_id'});
$due_date = sanitize($data->{'due_date'});

for($i = 0; $i < count($entries); $i++){
	if(!json_cont_i($entries[$i],'product') || !json_cont_i($entries[$i],'orig') || !json_cont_i($entries[$i],'count') || !json_cont_i($entries[$i],'unit_count') || !json_cont_i($entries[$i],'unit_price') || !json_cont_i($entries[$i],'unit_discount') || !json_cont($entries[$i],'notes') || !json_cont_i($entries[$i],'due')){
		die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
	}
	$entries[$i]->{'notes'} = sanitize($entries[$i]->{'notes'});
}

$invoice = invoice_create($subtotal,$total,$customer,$type,$notes,$entries,$orig_id,$date);

if(!$invoice){
	die(json_encode(array('success' => false,'reason' => 'internal_error')));
}

journal_log(1,"Invoice ".$invoice." created",1,$invoice,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true,'invoice' => $invoice)));

?>
