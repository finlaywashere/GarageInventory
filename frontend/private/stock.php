<ul>
<h2>Out of Stock Items</h2>
<?php
	require_once "../api/private/inventory.php";
	require_once "../../private/db.php";
	$products = get_products();
	foreach ($products as $index => $id){
		$prod = get_product($id);
		$stock = $prod[3];
		if($stock <= 0){
			echo "<li>".$prod[1].": ".$stock."</li>";
		}
	}
?>
</ul>
