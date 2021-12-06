<?php

require_once $_SERVER['DOCUMENT_ROOT']."/private/authentication.php";

// Interface with authentication module
function inv_authenticate_request(int $min_perms){
	return authenticate_request($min_perms);
}

?>
