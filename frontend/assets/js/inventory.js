// From https://www.geeksforgeeks.org/how-to-include-a-javascript-file-in-another-javascript-file/
function include(file) {
	var script  = document.createElement('script');
	script.src  = file;
	script.type = 'text/javascript';
	script.defer = true;
	document.getElementsByTagName('head').item(0).appendChild(script); 
}
include('/assets/js/master.js');
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

function get_invoice_entries(id){
	return json_request("/inventory/api/public/invoice/get_invoice_entries.php", "invoice_id="+id);
}
function get_invoice_entry(id){
	return json_request("/inventory/api/public/invoice/get_invoice_entry.php", "entry_id="+id);
}
function get_product(id){
	return json_request("/inventory/api/public/product/get_product.php", "product_id="+id);
}
function get_invoice(id){
	return json_request("/inventory/api/public/invoice/get_invoice.php", "invoice_id="+id);
}
function get_invoices(type,param){
	return json_request("/inventory/api/public/invoice/get_invoices.php", "search_type="+type+"&search_param="+encode(param));
}
function get_customer(id){
	return json_request("/inventory/api/public/customer/get_customer.php","customer_id="+id);
}
function get_customers(type,param){
	return json_request("/inventory/api/public/customer/get_customers.php", "search_type="+type+"&search_param="+encode(param));
}
function create_customer(name,email,phone,address,notes,type){
	return json_request("/inventory/api/public/customer/create_customer.php", "name="+encode(name)+"&email="+encode(email)+"&phone="+encode(phone)+"&address="+encode(address)+"&notes="+encode(notes)+"&type="+type);
}
function create_invoice(data){
	return json_request("/inventory/api/public/invoice/create_invoice.php","data="+encode(data));
}
function search_journal(type,param){
	return json_request("/inventory/api/public/journal/search_journal.php", "search_type="+type+"&search_param="+encode(param));
}
function get_journal(id){
	return json_request("/inventory/api/public/journal/journal_data.php", "journal_uid="+id);
}
function update_customer(id,name,type,email,phone,address,notes){
	return json_request("/inventory/api/public/customer/update_customer.php", "customer_id="+id+"&name="+encode(name)+"&type="+type+"&email="+encode(email)+"&phone="+encode(phone)+"&address="+encode(address)+"&notes="+encode(notes));
}
function update_product(id,name,desc,notes,loc){
	return json_request("/inventory/api/public/product/update_product.php", "product_id="+id+"&name="+encode(name)+"&desc="+encode(desc)+"&notes="+encode(notes)+"&location="+encode(loc));
}
function get_products(type,param){
	return json_request("/inventory/api/public/product/get_products.php", "search_type="+type+"&search_param="+encode(param));
}
function create_product(name,desc,notes,loc){
	return json_request("/inventory/api/public/product/create_product.php", "name="+encode(name)+"&desc="+encode(desc)+"&notes="+encode(notes)+"&loc="+encode(loc));
}
function adjust_inventory(id,count,notes){
	return json_request("/inventory/api/public/product/adjust_inventory.php", "product_id="+id+"&count="+count+"&notes="+encode(notes));
}
