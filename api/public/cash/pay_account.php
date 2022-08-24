<?php
// Moves money from cash to an account, can also work in reverse

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('cash_id') || !req_param_i('account_id') || !req_param_i('cash_amount') || !req_param('notes')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$cid = req_get('cash_id');
$aid = req_get('account_id');
$amt = req_get('cash_amount');
$notes = req_get('notes');

if(strlen(trim($notes) < 4)){
	die(json_encode(array('success' => false, 'reason' => 'invalid_notes')));
}

$cash = get_cash($cid);
if(!$cash){
	die(json_encode(array('success' => false, 'reason' => 'invalid_cash')));
}
$account = get_account($aid);
if(!$account || !authenticate_request($account['perms'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_account')));
}

$result = payment_create(get_user_id(get_username()), 0, $amt, 4, $aid, "Payment from cash to account. ".$notes);
if($result){
	die(json_encode(array('success' => false, 'reason' => 'account_error', 'code' => $result)));
}

if(!adjust_cash($cid, $amt * -1)){
	die(json_encode(array('success' => false, 'reason' => 'cash_error')));
}
journal_log(2,"Cash ".$cid." paid to account ".$aid,11,$cid,get_username(),$_SERVER['REMOTE_ADDR']);
die(json_encode(array('success' => true)));

?>

