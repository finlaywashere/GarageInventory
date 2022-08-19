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
	if(!json_cont_i($entries[$i],'product') || !json_cont($entries[$i],'orig') || !json_cont_i($entries[$i],'count') || !json_cont_i($entries[$i],'unit_count') || !json_cont_i($entries[$i],'unit_price') || !json_cont_i($entries[$i],'unit_discount') || !json_cont($entries[$i],'notes')){
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
	if(!get_product($product)){
		die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
	}
	$orig = json_get($entries[$i],'orig');
	$count = json_get($entries[$i],'count');
	if($count < 0 || $count == 0){
		die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
	}
	$unit_count = json_get($entries[$i],'unit_count');
	if($unit_count < 0 || $unit_count == 0){
		die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
	}
	$price = json_get($entries[$i],'unit_price');
	$discount = json_get($entries[$i],'unit_discount');
	$enotes = json_get($entries[$i],'notes');
	$edue = json_get($entries[$i],'due');
	$entries[$i]->{'notes'} = sanitize($entries[$i]->{'notes'});
}
$ptotal = 0;
$pcash = false;
for($i = 0; $i < count($payments); $i++){
	if(!json_cont_i($payments[$i],'type') || !json_cont_i($payments[$i],'amount') || !json_cont($payments[$i],'identifier') || !json_cont($payments[$i],'notes')){
		die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
	}
	// Payment types:
	// 0 - Cash
	// 1 - Credit
	// 2 - Debit
	// 3 - Cheque
	// 4 - Account
	// 5 - Virtual

	$ptype = json_get($payments[$i],'type');
	if($ptype < 0 || $ptype > 5){
		die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
	}
	$amount = json_get($payments[$i],'amount');
	if($amount == 0){
		die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
	}
	$ident = json_get($payments[$i],'identifier');
	$notes = json_get($payments[$i],'notes');
	$ptotal += $amount;
	if($ptype == 4){
		$accounts = get_accounts();
		if(!isset($accounts[$ident])){
			die(json_encode(array('success' => false, 'reason' => 'invalid_account')));
		}
		$acc = $accounts[$ident];
		if(!authenticate_request($acc['perms'])){
			die(json_encode(array('success' => false, 'reason' => 'invalid_account')));
		}
		if($notes == ""){
			$nospaces = preg_replace("/[^A-Za-z0-9]/","",$notes);
			$len = strlen($nospaces);
			if($len < 5){
				die(json_encode(array('success' => false, 'reason' => 'invalid_notes')));
			}
		}
	}
	if($ptype != 0){
		$nospaces = preg_replace("/[^A-Za-z0-9]/","",$ident);
		$len = strlen($nospaces);
		if($ptype > 3){
			if($len < 1){
				die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
			}
		}else{
			if($len < 4){
				die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
			}
		}
	}
	if($ptype == 0){
		$pcash = true;
		// Check to make sure it is a valid location
		if(get_cash($ident) == NULL){
			die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
		}
		$cash = get_cash($ident)['total'];
		if($type == 1){
			if($cash-$amount < 0){
				die(json_encode(array('success' => false, 'reason' => 'invalid_funds')));
			}
		}
	}
}
if(!$pcash){
	if($ptotal != $total){
		die(json_encode(array('success' => false, 'reason' => 'invalid_totals')));
	}
}else{
	$diff = abs($ptotal - $total);
	if($diff > 2){
		die(json_encode(array('success' => false, 'reason' => 'invalid_totals')));
	}
}

$invoice = invoice_create($subtotal,$total,$customer,$type,$notes,$entries,$orig_id,$date,$payments,get_user_id(get_username()));

if(!$invoice){
	die(json_encode(array('success' => false,'reason' => 'internal_error')));
}

journal_log(1,"Invoice ".$invoice." created",1,$invoice,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true,'invoice' => $invoice)));

?>
