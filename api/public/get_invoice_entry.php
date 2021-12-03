<?php
header('Content-Type: application/json');

require_once "../private/db.php";
require_once "../private/inventory.php";
require_once "../private/authentication.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['entry_id'])){
	die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = (int) $_REQUEST['entry_id'];

$entry = get_invoice_entry($id);
$array = array("invoice_id" => $entry[0], "product_id" => $entry[1], "count" => $entry[2], "unit_price" => $entry[3], "notes" => $entry[4]);
die(json_encode($array));

?>
