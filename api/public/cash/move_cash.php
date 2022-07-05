<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(100);
if(!$auth){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('src_id') || !req_param_i('dst_id') || !req_param_i('nickels') || !req_param_i('dimes') || !req_param_i('quarters') || !req_param_i('loonies') || !req_param_i('toonies') || !req_param_i('fives') || !req_param_i('tens') || !req_param_i('twenties') || !req_param_i('fifties') || !req_param_i('hundreds')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$src = req_get('src_id');
$dst = req_get('dst_id');
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

$result = cash_move($src,$dst,$nickels,$dimes,$quarters,$loonies,$toonies,$fives,$tens,$twenties,$fifties,$hundreds);
if(!$result){
	die(json_encode(array('success' => false, 'reason' => 'invalid_data')));
}
journal_log(2,"Cash moved from ".$src." to ".$dst,11,$src,get_username(),$_SERVER['REMOTE_ADDR']);
die(json_encode(array('success' => true)));

?>
