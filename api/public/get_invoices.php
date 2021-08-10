<?php

header('Content-Type: application/json');

require_once "../private/inventory.php";
require_once "../private/authentication.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
$invoices = get_invoices();

$json = "{\"success\":true,\"invoices\":[";
for($i = 0; $i < count($invoices); $i++){
    $json .= $invoices[$i].",";
}
if(count($invoices)){
    $json = substr($json,0,-1);
}
$json .= "]}";

die($json);

?>

