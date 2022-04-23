<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(2);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('product_id') || !req_param('name') || !req_param('desc') || !req_param('notes') || !req_param('location') || !req_param_i('type')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$id = req_get('product_id');
$name = req_get('name');
$desc = req_get('desc');
$notes = req_get('notes');
$location = req_get('location');
$type = req_get('type');

modify_product($id,$name,$desc,$notes,$location,$type);

journal_log(2,"Product ".$id." updated",3,$id,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true)));

?>
