<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(20);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('product_id') || !req_param_i('count')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$id = req_get('product_id');
$count = req_get('count');
$notes = "";
if(req_param('notes'))
	$notes = req_get('notes';
set_inventory($id,$count);

journal_log(2,"Product ".$id." inventory changed to ".$count." with notes \"".$notes."\"",3,$id,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true)));

?>
