<?php

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

$auth = authenticate_request(1);
if(!$auth){
    die(json_encode(array('success' => false, 'reason' => 'authorization')));
}

$accounts = get_accounts();

$result = array();

$accs = array_keys($accounts);
foreach ($accs as $acc) {
	if(authenticate_request($accounts[$acc]['perms'])){
		$result[$acc] = $accounts[$acc];
	}
}

die(json_encode(array('success' => true, 'accounts' => $result)));

?>

