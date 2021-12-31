<?php

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

$auth = authenticate_request(3);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['search_type']) || !isset($_REQUEST['search_param'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$stype = (int) $_REQUEST['search_type'];
$param = $_REQUEST['search_param'];

$customers = customer_search($stype, $param);
if($customers === NULL){
	die(json_encode(array('success' => false, 'reason' => 'invalid_type')));
}

die(json_encode(array('success' => true, 'customers' => $customers)));

?>

