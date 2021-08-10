<?php

header('Content-Type: application/json');

require_once "../private/inventory.php";
require_once "../private/authentication.php";

$auth = authenticate_request(1);
if(!$auth){
	die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
$products = get_products();

$json = "{\"success\":true,\"products\":[";
for($i = 0; $i < count($products); $i++){
	$json .= $products[$i].",";
}
if(count($products)){
	$json = substr($json,0,-1);
}
$json .= "]}";

die($json);

?>
