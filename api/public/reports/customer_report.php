<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
	http_response_code(401);
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
$report = customer_report(get_user_id(get_username()));

die(json_encode(array('success' => true, 'report' => $report)));

?>
