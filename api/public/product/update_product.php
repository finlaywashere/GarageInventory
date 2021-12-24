<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

$auth = authenticate_request(2);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['product_id']) || !isset($_REQUEST['name']) || !isset($_REQUEST['orig_id']) || !isset($_REQUEST['desc']) || !isset($_REQUEST['notes']) || !isset($_REQUEST['location'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$id = (int) $_REQUEST['product_id'];
$name = $_REQUEST['name'];
$desc = $_REQUEST['desc'];
$orig_id = $_REQUEST['orig_id'];
$notes = $_REQUEST['notes'];
$location = $_REQUEST['location'];

modify_product($id,$name,$desc,$orig_id,$notes,$location);

die(json_encode(array('success' => true)));

?>
