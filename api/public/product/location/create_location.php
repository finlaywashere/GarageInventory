<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(4);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('name')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_location')));
}
$name = req_get('name');

$loc = location_create($name);

journal_log(1,"Location ".$loc." created",3,$loc,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true,'location' => $loc)));

?>
