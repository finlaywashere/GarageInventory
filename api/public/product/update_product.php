<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(2);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('product_id') || !req_param('name') || !req_param('desc') || !req_param('notes') || !req_param_i('type') || !req_param('loc')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$id = req_get('product_id');
$name = req_get('name');
$desc = req_get('desc');
$notes = req_get('notes');
$type = req_get('type');
$loc = req_get('loc');

modify_product($id,$name,$desc,$notes,$type,$loc);

journal_log(2,"Product ".$id." updated",3,$id,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true)));

?>
