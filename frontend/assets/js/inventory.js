function invoice_type_to_string(type){
	if(type === 0){
		return "SYS";
	}else if(type === 1){
		return "INC"
	}else if(type === 2){
		return "OUT";
	}else{
		return "UNK";
	}
}
function customer_type_to_string(type){
	if(type === 0){
		return "SYS";
	}else if(type === 1){
		return "NRM"
	}else if(type === 2){
		return "BUS";
	}else{
		return "UNK";
	}
}
function journal_id_to_string(type){
	if(type === 0){
		return "SYSTEM";
	}else if(type === 1){
		return "INVOICE";
	}else if(type === 2){
		return "CUSTOMER";
	}else if(type === 3){
		return "PRODUCT";
	}else if(type === 4){
		return "JOURNAL";
	}else if(type === 5){
		return "ADMINISTRATIVE";
	}else if(type === 6){
		return "SECURITY";
	}else if(type === 7){
		return "OVERRIDE";
	}else if(type === 8){
		return "DAMAGED";
	}else if(type === 9){
		return "PROJECT";
	}else if(type === 10){
		return "ACCOUNT";
	}else if(type === 11){
		return "CASH";
	}else{
		return "UNK";
	}
}
function journal_type_to_string(type){
	if(type === 0){
		return "SYS";
	}else if(type === 1){
		return "CRE";
	}else if(type === 2){
		return "MOD";
	}else if(type === 3){
		return "DEL";
	}else{
		return "UNK";
	}
}
function string_to_customer_type(str){
	var tmp = str.toUpperCase();
	if(tmp === "BUS"){
		return 2;
	}else if(tmp === "SYS"){
		return 0;
	}else if(tmp === "NRM"){
		return 1;
	}else{
		return -1;
	}
}
function payment_type_to_string(type){
	if(type == 0){
		return "CASH";
	}else if(type == 1){
		return "CREDIT";
	}else if(type == 2){
		return "DEBIT";
	}else if(type == 3){
		return "CHEQUE";
	}else if(type == 4){
		return "ACCOUNT";
	}else if(type == 5){
		return "VIRTUAL";
	}
}
function product_type_to_string(type){
	if(type == 0){
		return "STOCK";
	}else if(type == 1){
		return "NONSTOCK";
	}else if(type == 2){
		return "CUSTOM (MP)";
	}else if(type == 3){
		return "CUSTOM (OO)";
	}else if(type == 4){
		return "CUSTOM (EV)";
	}else if(type == 5){
		return "PSEUDO";
	}
}
function product_string_to_type(string){
	var lower = string.toUpperCase();
	if(lower == "STOCK"){
		return 0;
	}else if(lower == "NONSTOCK"){
		return 1;
	}else if(lower == "CUSTOM (MP)"){
		return 2;
	}else if(lower == "CUSTOM (OO)"){
		return 3;
	}else if(lower == "CUSTOM (EV)"){
		return 4;
	}else if(lower == "PSEUDO"){
		return 5;
	}
}

function get_product(id){
	return json_request("/inventory/api/public/product/get_product.php", "product_id="+id);
}
function get_product_history(id){
	return json_request("/inventory/api/public/product/product_history.php", "product_id="+id);
}
function get_invoice(id){
	return json_request("/inventory/api/public/invoice/get_invoice.php", "invoice_id="+id);
}
function get_invoices(type,param,offset){
	return json_request("/inventory/api/public/invoice/get_invoices.php", "search_type="+type+"&search_param="+encode(param)+"&search_offset="+offset);
}
function get_customer(id){
	return json_request("/inventory/api/public/customer/get_customer.php","customer_id="+id);
}
function get_customers(type,param,offset){
	return json_request("/inventory/api/public/customer/get_customers.php", "search_type="+type+"&search_param="+encode(param)+"&search_offset="+offset);
}
function create_customer(name,email,phone,address,notes,type){
	return json_request("/inventory/api/public/customer/create_customer.php", "name="+encode(name)+"&email="+encode(email)+"&phone="+encode(phone)+"&address="+encode(address)+"&notes="+encode(notes)+"&type="+type);
}
function create_invoice(data){
	return json_request("/inventory/api/public/invoice/create_invoice.php","data="+encode(data));
}
function search_journal(type,param,offset){
	return json_request("/inventory/api/public/journal/search_journal.php", "search_type="+type+"&search_param="+encode(param)+"&search_offset="+offset);
}
function get_journal(id){
	return json_request("/inventory/api/public/journal/journal_data.php", "journal_uid="+id);
}
function update_customer(id,name,type,email,phone,address,notes){
	return json_request("/inventory/api/public/customer/update_customer.php", "customer_id="+id+"&name="+encode(name)+"&type="+type+"&email="+encode(email)+"&phone="+encode(phone)+"&address="+encode(address)+"&notes="+encode(notes));
}
function update_product(id,name,desc,notes,type,loc){
	return json_request("/inventory/api/public/product/update_product.php", "product_id="+id+"&name="+encode(name)+"&desc="+encode(desc)+"&notes="+encode(notes)+"&type="+type+"&loc="+encode(loc));
}
function get_products(type,param,offset){
	return json_request("/inventory/api/public/product/get_products.php", "search_type="+type+"&search_param="+encode(param)+"&search_offset="+offset);
}
function create_product(name,desc,notes,type,loc){
	return json_request("/inventory/api/public/product/create_product.php", "name="+encode(name)+"&desc="+encode(desc)+"&notes="+encode(notes)+"&type="+encode(type)+"&loc="+encode(loc));
}
function adjust_inventory(id,count,notes){
	return json_request("/inventory/api/public/product/adjust_inventory.php", "product_id="+id+"&count="+count+"&notes="+encode(notes));
}
function get_accounts(){
	return json_request("/inventory/api/public/payments/get_accounts.php","");
}
function create_account(name,perms,desc){
	return json_request("/inventory/api/public/payments/create_account.php","name="+encode(name)+"&perms="+perms+"&desc="+encode(desc));
}
function account_history(id,start,end){
	return json_request("/inventory/api/public/payments/account_history.php","id="+id+"&start="+encode(start)+"&end="+encode(end));
}
function get_account(id){
	return json_request("/inventory/api/public/payments/get_account.php","id="+id);
}
function create_cash(name){
	return json_request("/inventory/api/public/cash/create_cash.php", "name="+encode(name));
}
function count_cash(id,nickels,dimes,quarters,loonies,toonies,fives,tens,twenties,fifties,hundreds){
	return json_request("/inventory/api/public/cash/count_cash.php", "cash_id="+id+"&nickels="+nickels+"&dimes="+dimes+"&quarters="+quarters+"&loonies="+loonies+"&toonies="+toonies+"&fives="+fives+"&tens="+tens+"&twenties="+twenties+"&fifties="+fifties+"&hundreds="+hundreds);
}
function get_cash(id){
	return json_request("/inventory/api/public/cash/get_cash.php", "cash_id="+id);
}
function pay_account(cid, aid, amount, notes){
	return json_request("/inventory/api/public/cash/pay_account.php", "cash_id="+cid+"&account_id="+aid+"&cash_amount="+amount+"&notes="+encode(notes));
}
function update_account(aid,name,desc,perms){
	return json_request("/inventory/api/public/payments/update_account.php", "account="+aid+"&perms="+perms+"&name="+encode(name)+"&desc="+encode(desc));
}
