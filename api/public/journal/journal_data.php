<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
	http_response_code(401);
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!req_param_i('journal_uid')){
	http_response_code(400);
    die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$uid = req_get('journal_uid');

$result = journal_get($uid);

if($result === NULL){
	http_response_code(400);
	die(json_encode(array('success' => false, 'reason' => 'invalid_uid')));
}
die(json_encode(array('success' => true, 'journal' => $result)));

?>
