<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request("inventory/cash/admin");
if(!$auth){
	http_response_code(401);
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('cash_id') || !req_param_i('nickels') || !req_param_i('dimes') || !req_param_i('quarters') || !req_param_i('loonies') || !req_param_i('toonies') || !req_param_i('fives') || !req_param_i('tens') || !req_param_i('twenties') || !req_param_i('fifties') || !req_param_i('hundreds')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$id = req_get('cash_id');
$nickels = req_get('nickels');
$dimes = req_get('dimes');
$quarters = req_get('quarters');
$loonies = req_get('loonies');
$toonies = req_get('toonies');
$fives = req_get('fives');
$tens = req_get('tens');
$twenties = req_get('twenties');
$fifties = req_get('fifties');
$hundreds = req_get('hundreds');

if($nickels < 0 || $dimes < 0 || $quarters < 0 || $loonies < 0 || $toonies < 0 || $fives < 0 || $tens < 0 || $twenties < 0 || $fifties < 0 || $hundreds < 0){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_count')));
}
if(!get_cash($id)){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$result = count_cash($id,$nickels,$dimes,$quarters,$loonies,$toonies,$fives,$tens,$twenties,$fifties,$hundreds,get_user_id(get_username()));
if(!$result){
	journal_log(2,"Cash ".$id." counted",11,$id,get_username(),$_SERVER['REMOTE_ADDR']);
	die(json_encode(array('success' => true)));
}else if($result == 1){
	$total = cash_total($nickels,$dimes,$quarters,$loonies,$toonies,$fives,$tens,$twenties,$fifties,$hundreds);
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'different_totals', 'count_total' => $total, 'cash_total' => get_cash($id)['total'])));
}else{
	http_response_code(500);
	die(json_encode(array('success' => false, 'reason' => 'unknown')));
}

?>
