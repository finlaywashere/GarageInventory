<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}

$locs = get_cash_locations();
if(!$locs){
	die(json_encode(array('success' => false, 'reason' => 'error')));
}
die(json_encode(array('success' => true,'locations' => $locs)));

?>
