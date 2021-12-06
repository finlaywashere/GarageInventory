<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['search_type']) || !isset($_REQUEST['search_param'])){
    die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$type = (int) $_REQUEST['search_type'];
$value = $_REQUEST['search_param'];

$result = journal_search($type,$value);

if($result === NULL){
	die(json_encode(array('success' => false, 'reason' => 'invalid_type')));
}
die(json_encode(array('success' => true, 'journals' => $result)));

?>