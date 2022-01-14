<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(4);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param('name') || !req_param('desc') || !req_param('notes') || !req_param('loc')){
	die(json_encode(array('success' => false, 'reason' => 'invalid_product')));
}
$name = req_get('name');
$desc = req_get('desc');
$notes = req_get('notes');
$loc = req_get('loc');

$product = create_product($name,$desc,$notes,$loc);

journal_log(1,"Product ".$product." created",3,$product,get_username(),$_SERVER['REMOTE_ADDR']);

die(json_encode(array('success' => true,'product' => $product)));

?>
