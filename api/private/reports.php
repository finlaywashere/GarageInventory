<?php
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/db.php";
// This code handles generating product/customer/invoice reports

function product_report(int $id, $user){
	$product = get_product($id);
	if(!$product){
		return 0;
	}
	$invoices = invoice_product_search($id, 50); // Get most recent 50 invoices
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
		foreach($entries as $ent){
			if($ent['product'] == $id){
				$count += $ent['unit_count'] * $ent['count'];
			}
		}
		$body .= $count."\n";
	}
	return create_report($user, $title, html_encode($body), 0, 0);
}
function customer_report($user){
	$date = date("Y-m-d");
	$title = "Customer Report for ".$date;
	$customers = get_customers_from_date($date);
	$body = "New Customers\n";
	$body .= "ID\tName\tType\n";
	for($i = 0; $i < count($customers); $i++){
		$customer = get_customer($customers[$i]);
		$type = "";
		$typeI = $customer['type'];
		if($typeI == 0){
			$type = "SYSTEM";
		}else if($typeI == 1){
			$type = "NORMAL";
		}else if($typeI == 2){
			$type = "BUSINESS";
		}else{
			$type = "UNKNOWN";
		}
		$body .= $customers[$i]."\t".$customer['name']."\t".$type."\n";
	}
	$body .= "\nSpending by Customer\n";
	$body .= "ID\tName\tIn\tOut\tSys\tTotal\n";
	$invoices = get_invoices_from_date($date);
	$incoming = array();
	$outgoing = array();
	$system = array();
	$custs = array();
	for($i = 0; $i < count($invoices); $i++){
		$inv = get_invoice($invoices[$i]);
		$total = $inv['total'];
		$type = $inv['type'];
		$id = $inv['customer'];
		array_push($custs,$id);
		if(!isset($system[$id])){
			$system[$id] = 0;
			$incoming[$id] = 0;
			$outgoing[$id] = 0;
		}
		if($type == 0){
			$system[$id] += $total;
		}else if($type == 1){
			$incoming[$id] += $total;
		}else if($type == 2){
			$outgoing[$id] += $total;
		}
	}
	$custs = array_unique($custs);
	for($i = 0; $i < count($custs); $i++){
		$id = $custs[$i];
		$customer = get_customer($id);
		
		$inc = 0;
		if(isset($incoming[$id]))
			$inc = $incoming[$id];
		$out = 0;
		if(isset($outgoing[$id]))
			$out = $outgoing[$id];
		$sys = 0;
		if(isset($system[$id]))
			$sys = $system[$id];
		// Calculate total amount of money COMING IN to the system
		$tot = $inc - $out + $sys;
		$inc = ($inc / 100);
		$out = ($out / 100);
		$sys = ($sys / 100);
		$tot = ($tot / 100);
		$body .= $id."\t".$customer['name']."\t".$inc."\t".$out."\t".$sys."\t".$tot."\n";
	}
	return create_report($user, $title, html_encode($body), 0, 0);
}
?>
