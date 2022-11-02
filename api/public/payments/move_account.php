<?php
// Moves money from an account to another account, can also work in reverse

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('src_id') || !req_param_i('dst_id') || !req_param_i('amount') || !req_param('notes')){
    die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$src = req_get('src_id');
$dst = req_get('dst_id');
$amt = req_get('amount');
$notes = req_get('notes');

$srcAccount = get_account($src);
$dstAccount = get_account($dst);
if(!$srcAccount || !authenticate_request($srcAccount['perms']) || !$dstAccount || !authenticate_request($dstAccount['perms'])){
    die(json_encode(array('success' => false, 'reason' => 'invalid_account')));
}

$result = payment_create(get_user_id(get_username()), 0, $amt, 4, $dst, "Payment from account to account. ".$notes);
if($result){
    die(json_encode(array('success' => false, 'reason' => 'dst_error', 'code' => $result)));
}
$result = payment_create(get_user_id(get_username()), 0, -$amt, 4, $src, "Payment from account to account. ".$notes);
if($result){
    die(json_encode(array('success' => false, 'reason' => 'src_error', 'code' => $result)));
}
journal_log(2,"Money moved from account ".$src." to ".$dst,10,$src,get_username(),$_SERVER['REMOTE_ADDR']);
die(json_encode(array('success' => true)));


?>
