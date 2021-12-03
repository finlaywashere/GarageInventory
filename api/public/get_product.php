<?php
header('Content-Type: application/json');

require_once "../private/db.php";
require_once "../private/inventory.php";
require_once "../private/authentication.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['product_id'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = (int) $_REQUEST['product_id'];

$product = get_product($id);
$array = array("original_id" => $product[0], "name" => $product[1], "desc" => $product[2], "count" => $product[3], "location" => $product[4], "notes" => $product[5]);
die(json_encode($array));

?>
