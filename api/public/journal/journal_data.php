<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}
if(!isset($_REQUEST['journal_uid'])){
    die(json_encode(array('success' => false, 'reason' => 'invalid_request')));
}
$uid = (int) $_REQUEST['journal_uid'];

$result = journal_get($uid);

if($result === NULL){
	die(json_encode(array('success' => false, 'reason' => 'invalid_uid')));
}
die(json_encode(array('success' => true, 'journal' => $result)));

?>