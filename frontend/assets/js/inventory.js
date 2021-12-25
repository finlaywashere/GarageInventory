// From https://stackoverflow.com/questions/5448545/how-to-retrieve-get-parameters-from-javascript/
function getSearchParameters() {
	var prmstr = window.location.search.substr(1);
	return prmstr != null && prmstr != "" ? transformToAssocArray(prmstr) : {};
}

function transformToAssocArray( prmstr ) {
	var params = {};
	var prmarr = prmstr.split("&");
	for ( var i = 0; i < prmarr.length; i++) {
		var tmparr = prmarr[i].split("=");
		params[tmparr[0]] = tmparr[1];
	}
	return params;
}
function clearTable(t){
	var children = t.querySelectorAll('tr')
	for(let i = 0; i < children.length; i++){
		let found = false;
		if(children[i].childNodes != undefined){
			for(let i1 = 0; i1 < children[i].childNodes.length; i1++){
				var child = children[i].childNodes[i1];
				if(child.nodeName == "TH")
					found = true;
			}
		}
		if(found)
			continue;
		var child = children[i];
		var parent = children[i].parentNode;
		parent.removeChild(child);
	}
}
function createElement(text, parent){
	var tmp = document.createElement("td");
	tmp.innerHTML = text;
	parent.appendChild(tmp);
}
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
		return "NOR"
	}else if(type === 2){
		return "BUS";
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
	}else if(type === 4){
		return "ACC";
	}else{
		return "UNK";
	}
}

function json_request(url,args){
	var result = null;
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", url, false);
	xmlhttp.addEventListener("load",function() {
		if (xmlhttp.readyState != 4) return;
		if (xmlhttp.status==200) {
			var json = JSON.parse(this.responseText);
			result = json;
			return null;
		}else{
			return null;
		}
	});
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.send(args);
	return result;
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
	return json_request("/inventory/api/public/invoice/get_invoices.php", "search_type="+type+"&search_param="+encodeURIComponent(param));
}
function get_customer(id){
	return json_request("/inventory/api/public/customer/get_customer.php","customer_id="+id);
}
function create_invoice(data){
	return json_request("/inventory/api/public/invoice/create_invoice.php","data="+encodeURIComponent(data));
}
function search_journal(type,param){
	return json_request("/inventory/api/public/journal/search_journal.php", "search_type="+type+"&search_param="+encodeURIComponent(param));
}
function get_journal(id){
	return json_request("/inventory/api/public/journal/journal_data.php", "journal_uid="+id);
}
