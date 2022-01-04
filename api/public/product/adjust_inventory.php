<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

$auth = authenticate_request(20);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['product_id']) || !isset($_REQUEST['count'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$id = (int) $_REQUEST['product_id'];
$count = (int) $_REQUEST['count'];

set_inventory($id,$count);

journal_log(5,"Product ".$id." inventory changed to ".$count,1,$id,get_username());

die(json_encode(array('success' => true)));

?>
