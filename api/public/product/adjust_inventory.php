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
$notes = "";
if(isset($_REQUEST['notes']))
	$notes = $_REQUEST['notes'];
set_inventory($id,$count);

journal_log(2,"Product ".$id." inventory changed to ".$count." with notes \"".$notes."\"",3,$id,get_username());

die(json_encode(array('success' => true)));

?>
