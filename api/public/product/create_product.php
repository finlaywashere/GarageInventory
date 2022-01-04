<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

$auth = authenticate_request(4);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['name']) || !isset($_REQUEST['desc']) || !isset($_REQUEST['notes']) || !isset($_REQUEST['loc'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_product')));
}
$name = $_REQUEST['name'];
$desc = $_REQUEST['desc'];
$notes = $_REQUEST['notes'];
$loc = $_REQUEST['loc'];

$product = create_product($name,$desc,$notes,$loc);

journal_log(1,"Product ".$product." created",3,$product,get_username());

die(json_encode(array('success' => true,'product' => $product)));

?>
