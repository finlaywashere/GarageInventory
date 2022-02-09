<?php
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";
// This code handles generating product/customer/invoice reports

function product_report(int $id, $user){
	$product = get_product($id);
	if(!$product){
		return 0;
	}
	$invoices = invoice_product_search($id);
	$title = "Product Report for #".$id;
	$body = $product['name']." - ".$product['description']." - ".$product['count']." on hand\nLocation: ".$product['location']."\nNotes: ".$product['notes']."\n\n";
	$body .= "Invoices for item:\n";
	$body .= "Invoice\tCustomer\tDate\tType\tCount\n";
	foreach ($invoices as $inv){
		$body .= $inv."\t";
		$invoice = get_invoice($inv);
		$customer = get_customer($invoice['customer']);
		$body .= $customer['name']."\t".$invoice['date']."\t";
		$type = $invoice['type'];
		if($type == 0){
			$body .= "System\t";
		}else if($type == 1){
			$body .= "Incoming\t";
		}else if($type == 2){
			$body .= "Outgoing\t";
		}else{
			$body .= "Unknown\t";
		}
		$entries = get_invoice_entries($inv);
		$count = 0;
		foreach($entries as $entry){
			$ent = get_invoice_entry($entry);
			if($ent['product'] == $id){
				$count += $ent['unit_count'] * $ent['count'];
			}
		}
		$body .= $count."\n";
	}
	return create_report($user, $title, html_encode($body), 0, 0);
}
?>
