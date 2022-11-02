<?php
// Allows you to make bank deposits/withdrawls to accounts
// Eg having a bank account account and keeping it in sync with pay cheques, etc or for actual deposits of cash to the bank from accounts

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(100);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('account_id') || !req_param_i('amount') || !req_param('notes')){
    die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$aid = req_get('account_id');
$amt = req_get('amount');
$notes = req_get('notes');

$account = get_account($aid);
if(!$account || !authenticate_request($account['perms'])){
    die(json_encode(array('success' => false, 'reason' => 'invalid_account')));
}

$result = payment_create(get_user_id(get_username()), 0, $amt, 4, $aid, "Bank deposit into account. ".$notes);
if($result){
    die(json_encode(array('success' => false, 'reason' => 'account_error', 'code' => $result)));
}
journal_log(2,"Bank deposit into account ".$aid,10,$aid,get_username(),$_SERVER['REMOTE_ADDR']);
die(json_encode(array('success' => true)));


?>
