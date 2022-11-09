<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request("inventory/product/create");
if(!$auth){
	http_response_code(401);
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('name')){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_location')));
}
$name = req_get('name');

$loc = location_create($name);

journal_log(1,"Location ".$loc." created",3,$loc,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true,'location' => $loc)));

?>
