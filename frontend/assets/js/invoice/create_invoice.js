var createButton = document.getElementById("create");
var resetButton = document.getElementById("reset");
var exchangeButton = document.getElementById("exchange");
var error = document.getElementById("error");
var success = document.getElementById("success");

var customer = document.getElementById("customer");
var type = document.getElementById("type");
var notes = document.getElementById("notes");
var orig_id = document.getElementById("orig_id");
var date = document.getElementById("date");
var add = document.getElementById("add");
var prodSearch = document.getElementById("prodSearch");
var prodCreate = document.getElementById("prodCreateB");
var entries = document.getElementById("entries");

add.addEventListener("click",addRow);
createButton.addEventListener("click",showConfirm);
exchangeButton.addEventListener("click",exchange);
resetButton.addEventListener("click",reset);

prodLookupSetup(prodSearch,prodCreate);

var infoText = document.getElementById("info");
var taxExempt = document.getElementById("taxexempt");
var payments = document.getElementById("payments");
var addPayment = document.getElementById("addPayment");

addPayment.addEventListener("click",addPaymentRow);

var confirmDiv = document.getElementById("confirm");
var confirmInfo = document.getElementById("confirminfo");
var warningsData = document.getElementById("warningsdata");
var continueButton = document.getElementById("continue");
var cancelButton = document.getElementById("cancel");

continueButton.addEventListener("click",create);
cancelButton.addEventListener("click",closeConfirm);

var cusLookup = document.getElementById("cusLookup");
var cusCreateB = document.getElementById("cusCreateB");

updateTotal();

csLookupSetup(custCallback,cusLookup,cusCreateB);

function custCallback(id){
	customer.value = id;
}

var pCash = false;

function showConfirm(){
	error.innerHTML = "";
	// Info to display to the user before confirming
	var subtotal = getSubtotal();
	var total = getTotal(subtotal);
	var pTotal = getPaymentTotal() + getExchangePTotal();

	var stop = false;
	var warn = "";
	if(!pCash){
        var precision = 0.001;
		if(Math.abs(total-pTotal) > precision){
			warn += "Totals do not match!<br>";
			stop = true;
		}
	}else{
		if(Math.abs(total-pTotal) > 2){
			warn += "Totals do not match!<br>";
			stop = true;
		}
	}

	confirmInfo.innerHTML = "Subtotal: "+subtotal+"<br>Total: "+total+"<br>Payment Total: "+pTotal.toFixed(2);
	// Show all warnings for missing data / errors / things to double check
	if(taxexempt.checked){
		warn += "Transaction is tax exempt!<br>";
	}
	const today = new Date();
	if(date.value == ""){
		warn += "Transaction has no date!<br>";
	}else{
		var split = date.value.split("-");
		var d = today.getDate();
		var m = today.getMonth()+1;
		var y = today.getFullYear();
		if(!(split[2] == d && split[1] == m && split[0] == y)){
			warn += "Transaction is not from today!<br>";
		}
	}
	var count = 0;
	for(var i = 0; i < entries.childNodes.length; i++){
		var child = entries.childNodes[i];
		if(child.nodeName == "TR"){
			count++;
			var columns = child.childNodes;
			var line = strip(columns[0].innerHTML);
			var product = strip(columns[1].innerHTML);
			var orig = strip(columns[2].innerHTML);
			var count = strip(columns[3].innerHTML);
			var unit_count = strip(columns[4].innerHTML);
			var price = strip(columns[5].innerHTML);
			var discount = strip(columns[6].innerHTML);
			var notes = strip(columns[7].innerHTML);
			if(product == ""){
				warn += "Line "+line+" Error: Invalid product id<br>";
				stop = true;
			}else{
				var prod = get_product(product);
				if(!prod.success){
					if(prod.reason == "invalid_id"){
						warn += "Line "+line+" Error: Invalid product id<br>";
						stop = true;
					}
				}
			}
			if(orig == ""){
				warn += "Line "+line+" Warning: No original id<br>";
			}
			if(count == "" || count < 1 || count == 0){
				warn += "Line "+line+" Error: Invalid count<br>";
				stop = true;
			}
			if(unit_count == "" || unit_count < 1 || unit_count == 0){
				warn += "Line "+line+" Error: Invalid unit count<br>";
				stop = true;
			}
			if(price == "" || price < 0 || price == 0){
				warn += "Line "+line+" Error: Invalid unit price<br>";
				stop = true;
			}
		}
	}
	if(count == 0){
		warn += "No entries in invoice!<br>";
		stop = true;
	}
	var pcount = 0;
	for(var i = 0; i < payments.childNodes.length; i++){
		var child = payments.childNodes[i];
		if(child.nodeName == "TR"){
			pcount++;
			var columns = child.childNodes;
			var line = strip(columns[0].innerHTML);
			var type = columns[1].childNodes[0].value;
			var amount = strip(columns[2].innerHTML);
			if(amount.startsWith("$")){
				amount = amount.substring(1);
			}
			var ident = strip(columns[3].innerHTML);
			var notes = strip(columns[4].innerHTML);
			if(type == 4){
				// Payment is going to an account
				var accounts = get_accounts();
				if(!accounts.success){
					console.log("Error in fetching accounts!");
					warn += "Failed to retrieve account information!<br>";
					continue;
				}
				if(!accounts.accounts.hasOwnProperty(ident)){
					warn += "Payment Line "+line+" Error: Account not found or user has insufficient permissions<br>";
					stop = true;
				}
				var nospaces = notes.replace(/[^0-9a-z]/gi, '');
				if(nospaces.length < 5){
					warn += "Payment Line "+line+" Error: No notes!<br>";
					stop = true;
				}
			}
			if(amount == "" || amount == 0){
				warn += "Payment Line "+line+" Error: Invalid amount!<br>";
				stop = true;
			}
			if(type > 0){
				// If type is not cash, identifier is required
				if(type != 4 && type != 5){
					if(ident.length < 4){
						warn += "Payment Line "+line+" Error: No identifier!<br>";
						stop = true;
					}
				}else{
					if(ident.length < 1){
						warn += "Payment Line "+line+" Error: No identifier!<br>";
						stop = true;
					}
				}
			}
		}
	}
	if(pcount == 0 && !exch){
		warn += "No payments for invoice!<br>";
		stop = true;
	}
	warningsData.innerHTML = warn;
	continueButton.disabled = stop;
	confirmDiv.style.visibility = "visible";
}
function closeConfirm(){
	confirmDiv.style.visibility = "hidden";
}
function productSearch(){
	var products = get_products(prLType.value,prLParam.value);
	if(!products.success){
		console.log("Failed to retrieve data!");
		prLError.innerHTML = "An error occurred while processing your request. Error: "+products.reason;
		return;
	}
	prLResults.innerHTML = "";
	for(let i = 0; i < products.products.length; i++){
		if(i == 4){
			var div = document.createElement("div");
			div.className = "oneline";
			var text = document.createElement("p");
			text.innerHTML = "... and "+(products.products.length-4)+" more";
			div.appendChild(text);
			prLResults.appendChild(div);
			break;
		}
		var product = get_product(products.products[i]);
		if(!product.success){
			console.log("Failed to retrieve some data!");
			prLError.innerHTML = "An error occurred while processing your request. Error: "+product.reason;
			return;
		}
		var div = document.createElement("div");
		div.className = "oneline";
		var text = document.createElement("p");
		text.innerHTML = "#"+products.products[i]+": "+product.product['name'];
		div.appendChild(text);
		prLResults.appendChild(div);
	}
}
function productLookup(){
	prodLookupDiv.style.visibility = "visible";
}
function productLookupClose(){
	prodLookupDiv.style.visibility = "hidden";
}
var line = 0;
function addRow(){
	line++;
	var r = document.createElement("tr");
	for(var i = 0; i < 8; i++){
		var tmp = document.createElement("td");
		tmp.setAttribute("oninput","updateTotal()");
		if(i != 0)
			tmp.setAttribute("contenteditable","true");
		if(i == 0){
			tmp.innerHTML = line;
		}else if(i == 3 || i == 4){
			tmp.innerHTML = "1";
		}else if(i == 5 || i == 6){
			tmp.innerHTML = "$0.00";
		}else{
			tmp.innerHTML = "";
		}
		r.appendChild(tmp);
	}
	entries.appendChild(r);
}
var pLine = 0;
function addPaymentRow(){
	pLine++;
	var r = document.createElement("tr");
	for(var i = 0; i < 5; i++){
		var tmp = document.createElement("td");
		tmp.setAttribute("oninput","updateTotal()");
		if(i > 1){
			tmp.setAttribute("contenteditable","true");
			if(i == 2){
				tmp.innerHTML = "$0.00";
			}else{
				tmp.innerHTML = "";
			}
		}else if (i == 1){
			var dropdown = document.createElement("select");
			var cash = document.createElement("option");
			cash.value = "0";
			cash.innerHTML = "CASH";
			dropdown.appendChild(cash);
			var credit = document.createElement("option");
			credit.value = "1";
			credit.innerHTML = "CREDIT";
			dropdown.appendChild(credit);
			var debit = document.createElement("option");
			debit.value = "2";
			debit.innerHTML = "DEBIT";
			dropdown.appendChild(debit);
			var cheque = document.createElement("option");
			cheque.value = "3";
			cheque.innerHTML = "CHEQUE";
			dropdown.appendChild(cheque);
			var acc = document.createElement("option");
			acc.value = "4";
			acc.innerHTML = "ACCOUNT";
			dropdown.appendChild(acc);
			var virt = document.createElement("option");
			virt.value = "5";
			virt.innerHTML = "VIRTUAL";
			dropdown.appendChild(virt);
			tmp.appendChild(dropdown);
		}else if (i == 0){
			tmp.innerHTML = pLine;
		}
		r.appendChild(tmp);
	}
	payments.appendChild(r);
}
function reset(){
	error.innerHTML = "";
	success.innerHTML = "";

	customer.value = "";
	type.selectedIndex = 0;
	notes.value = "";
	orig_id.value = "";
	date.value = "";
	clearTable(entries);
	clearTable(payments);

	csLType.selectedIndex = 0;
	csLParam.value = "";
	csLResults.innerHTML = "";
	prLType.selectedIndex = 0;
	prLParam.value = "";
	prLResults.innerHTML = "";
	
	taxexempt.checked = false;
	line = 0;
	exch = false;
	eEntries.style.visibility = "hidden";
	ePayments.style.visibility = "hidden";
	eText.style.visibility = "hidden";
	clearTable(eEntries);
	clearTable(ePayments);
	updateTotal();
}
function getSubtotal(){
	return calcSubtotal(entries);
}
function calcSubtotal(root){
	var subtotal = 0.00;
	for(var i = 0; i < root.childNodes.length; i++){
		var child = root.childNodes[i];
		if(child.nodeName == "TR"){
			var columns = child.childNodes;
			var count = strip(columns[3].innerHTML);
			var unit_count = strip(columns[4].innerHTML);
			var price = strip(columns[5].innerHTML);
			if(price.startsWith("$"))
				price = price.substring(1);
			var discount = strip(columns[6].innerHTML);
			if(discount.startsWith("$"))
				discount = discount.substring(1);
			var tmpTotal = count/unit_count * (price - discount);
			subtotal += tmpTotal;
		}
	}
	subtotal = subtotal.toFixed(2);
	return subtotal;
}
function getPaymentTotal(){
	return calcPaymentTotal(payments);
}
function calcPaymentTotal(root){
	pCash = false;
	var pTotal = 0;
	for(var i = 0; i < root.childNodes.length; i++){
		var child = root.childNodes[i];
		if(child.nodeName == "TR"){
			var map = {};
			var columns = child.childNodes;
			if(columns[1].firstChild.value == "0"){
				pCash = true;
			}
			var tot = strip(columns[2].innerHTML);
			if(tot.startsWith("$"))
				tot = tot.substring(1);
			pTotal += tot * 1.00;
		}
	}
	return pTotal;
}
function getTotal(subtotal){
	var total = taxexempt.checked ? subtotal * 1.00 : subtotal * 1.13;
	total = total.toFixed(2);
	return total;
}
function updateTotal(){
	var subtotal = getSubtotal();
	var pTotal = (getPaymentTotal()*1 + getExchangePTotal()*1).toFixed(2);
	var total = getTotal(subtotal);
	info.innerHTML = "Subtotal: "+subtotal+"<br>Total: "+total+"<br>Payment Total: "+pTotal+"<br>Difference: "+Math.abs(total-pTotal).toFixed(2);
}

function create(){
	if(Math.abs(getTotal(getSubtotal()) - getPaymentTotal() - getExchangePTotal()) > 0.02){
		error.innerHTML = "Unable to continue: totals do not match!";
		return;
	}
	error.innerHTML = "";
	success.innerHTML = "";
	var rows = [];
	var subtotal = 0;
	for(var i = 0; i < entries.childNodes.length; i++){
		var child = entries.childNodes[i];
		if(child.nodeName == "TR"){
			var map = {};
			var columns = child.childNodes;
			map["product"] = strip(columns[1].innerHTML);
			map["orig"] = strip(columns[2].innerHTML);
			map["count"] = strip(columns[3].innerHTML);
			map["unit_count"] = strip(columns[4].innerHTML);
			var price = strip(columns[5].innerHTML);
			if(price.startsWith("$"))
				price = price.substring(1);
			price *= 100;
			map["unit_price"] = Math.round(price);
			var discount = strip(columns[6].innerHTML);
			if(discount.startsWith("$"))
				discount = discount.substring(1);
			discount *= 100;
			discount = Math.round(discount);
			map["unit_discount"] = discount;
			map["notes"] = strip(columns[7].innerHTML);
			var lTotal = map['count'] / map['unit_count'] * (price - discount);
			subtotal += lTotal;
			rows.push(map);
		}
	}
	var rows2 = [];
	var pTotal = 0;
	for(var i = 0; i < payments.childNodes.length; i++){
		var child = payments.childNodes[i];
		if(child.nodeName == "TR"){
			var map = {};
			var columns = child.childNodes;
			map["type"] = columns[1].childNodes[0].value;
			var amount = strip(columns[2].innerHTML);
			if(amount.startsWith("$")){
				amount = amount.substring(1);
			}
			map["amount"] = Math.round(amount*100);
			map["identifier"] = strip(columns[3].innerHTML);
			map["notes"] = strip(columns[4].innerHTML);
			pTotal += map["amount"];
			rows2.push(map);
		}
	}
	var map2 = {};
	map2["entries"] = rows;
	map2["payments"] = rows2;
	map2["customer"] = strip(customer.value);
	map2["notes"] = strip(notes.value);
	map2["type"] = strip(type.value);
	map2["subtotal"] = Math.round(subtotal);
	map2["total"] = Math.round(taxexempt.checked ? subtotal * 1.00 : subtotal * 1.13);
	map2["orig_id"] = strip(orig_id.value);
	map2["date"] = date.value;

	var data = JSON.stringify(map2);
	var result = create_invoice(data);
	if(!result.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+result.reason;
		return;
	}
	var id = result.invoice;
	success.innerHTML = "Successfully created invoice with id <a href=/inventory/frontend/invoice/get_invoice.php?id="+id+">"+id+"</a>";
}
