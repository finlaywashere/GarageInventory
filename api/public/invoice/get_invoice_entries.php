<?php

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['invoice_id'])){
    die(json_encode(array('success' => false, 'reason' => 'invalid_id')));
}
$id = (int) $_REQUEST['invoice_id'];

$entries = get_invoice_entries($id);

$json = "{\"success\":true,\"entries\":[";
for($i = 0; $i < count($entries); $i++){
    $json .= $entries[$i].",";
}
if(count($entries)){
    $json = substr($json,0,-1);
}
$json .= "]}";

die($json);

?>

