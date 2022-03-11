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
if(!json_cont_i($data,'subtotal') || !json_cont_i($data,'total') || !json_cont($data,'notes') || !json_cont_i($data,'customer') || !json_cont_i($data,'type') || !json_cont($data,'entries') || !json_cont($data,'orig_id') || !json_cont($data,'date') || !json_cont($data,'payments')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}
$subtotal = sanitize($data->{'subtotal'});
$total = sanitize($data->{'total'});
$notes = sanitize($data->{'notes'});
$customer = sanitize($data->{'customer'});
$type = sanitize($data->{'type'});
$entries = $data->{'entries'};
$payments = $data->{'payments'};
$date = sanitize($data->{'date'});
$orig_id = sanitize($data->{'orig_id'});
if(!json_cont($data,'due_date')){
	$due_date = "";
}else{
	$due_date = sanitize($data->{'due_date'});
}

if($subtotal <= 0){
	die(json_encode(array('success' => false, 'reason' => 'invalid_subtotal')));
}
if($total <= 0 || $total < $subtotal){
    die(json_encode(array('success' => false, 'reason' => 'invalid_total')));
}
if($customer < 0 || !is_customer($customer)){
    die(json_encode(array('success' => false, 'reason' => 'invalid_customer')));
}
if($type < 0 || $type > 2){
    die(json_encode(array('success' => false, 'reason' => 'invalid_subtotal')));
}
if($type == 0 && !authenticate_request(100)){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
for($i = 0; $i < count($entries); $i++){
	if(!json_cont_i($entries[$i],'product') || !json_cont_i($entries[$i],'orig') || !json_cont_i($entries[$i],'count') || !json_cont_i($entries[$i],'unit_count') || !json_cont_i($entries[$i],'unit_price') || !json_cont_i($entries[$i],'unit_discount') || !json_cont($entries[$i],'notes')){
		die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
	}
	if(json_cont($entries[$i],'due')){
		if(!json_cont_i($entries[$i],'due')){
			die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
		}
	}else{
		$entries[$i]->{'due'} = 0;
	}
	$product = json_get($entries[$i],'product');
	$orig = json_get($entries[$i],'orig');
	$count = json_get($entries[$i],'count');
	$unit_count = json_get($entries[$i],'unit_count');
	$price = json_get($entries[$i],'unit_price');
	$discount = json_get($entries[$i],'unit_discount');
	$enotes = json_get($entries[$i],'notes');
	$edue = json_get($entries[$i],'due');
	$entries[$i]->{'notes'} = sanitize($entries[$i]->{'notes'});
}
for($i = 0; $i < count($payments); $i++){
	if(!json_cont_i($payments[$i],'type') || !json_cont_i($payments[$i],'amount') || !json_cont($payments[$i],'identifier')){
		die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
	}
	$ptype = json_get($payments[$i],'type');
	$amount = json_get($payments[$i],'amount');
	$ident = json_get($payments[$i],'identifier');
}

$invoice = invoice_create($subtotal,$total,$customer,$type,$notes,$entries,$orig_id,$date,$payments,get_user_id(get_username()));

if(!$invoice){
	die(json_encode(array('success' => false,'reason' => 'internal_error')));
}

journal_log(1,"Invoice ".$invoice." created",1,$invoice,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true,'invoice' => $invoice)));

?>
