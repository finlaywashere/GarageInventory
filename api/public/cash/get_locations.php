<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
	http_response_code(401);
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}

$locs = get_cash_locations();
if(!$locs){
	http_response_code(500);
	die(json_encode(array('success' => false, 'reason' => 'error')));
}
die(json_encode(array('success' => true,'locations' => $locs)));

?>
