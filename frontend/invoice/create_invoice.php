<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request(0);
	if($result == 0){
		force_login();
	}
?>
<html>
	<head>
		<title>Internal Inventory Services</title>
		<link rel="stylesheet" type="text/css" href="/inventory/frontend/assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT']."/frontend/header.php");?>
		<div class="subheader" style="display: inline-block;">
			<label>Customer: </label><input id="customer" type="number" min="0">
			<button id="cusLookup">Customer Lookup</button>
			<br>
			<label>Type: </label>
			<select id="type">
				<option value="1">Incoming</option>
				<option value="2">Outgoing</option>
				<?php
					$create_sys = authenticate_request(100);
					if($create_sys){
						echo "<option value=\"0\">System</option>";
					}
				?>
			</select><br>
			<label>Notes: </label><input id="notes" type="text"><br>
			<label>Original ID: </label><input id="orig_id" type="text"><br>
			<label>Date: </label><input id="date" type="date"></br>
			<button id="create">Create</button>
			<button id="exchange">Exchange</button>
			<button id="reset">Reset</button>
			<p id="info"></p>
		</div>
		<div class="content">
			<button id="add">Add Entry</button>
			<button id="prodSearch">Product Search</button>
			<br>
			<table id="entries">
				<tr id="table_header">
					<th>Line</th>
					<th>Product ID</th>
					<th>Original ID</th>
					<th>Count</th>
					<th>Unit Count</th>
					<th>Unit Price</th>
					<th>Unit Discount</th>
					<th>Notes</th>
				</tr>
			</table>
			<br>
			<button id="addPayment">Add Payment</button>
			<label for="taxexempt">Tax Exempt</label>
			<input type="checkbox" id="taxexempt" value="Tax Exempt">
			<table id="payments">
				<tr id="table_header">
					<th>Line</th>
					<th>Type</th>
					<th>Amount</th>
					<th>Identifier</th>
					<th>Notes</th>
				</tr>
			</table>
			<br>
			<h2 style="visibility: hidden;" id="eText">Exchange</h2>
			<table id="eEntries" style="visibility: hidden;">
				<tr id="table_header">
					<th>Line</th>
					<th>Product ID</th>
					<th>Original ID</th>
					<th>Count</th>
					<th>Unit Count</th>
					<th>Unit Price</th>
					<th>Unit Discount</th>
					<th>Notes</th>
				</tr>
			</table>
			<br>
			<table id="ePayments" style="visibility: hidden">
				<tr id="table_header">
					<th>Line</th>
					<th>Type</th>
					<th>Amount</th>
					<th>Identifier</th>
					<th>Notes</th>
				</tr>
			</table>
		</div>
		<div id="cusLookupDiv" class="floating">
			<h2>Customer ID Lookup</h2>
			<label>Search Type: </label>
			<select id="csLType">
				<option value="1">Name</option>
				<option value="2">Phone #</option>
				<option value="3">Email</option>
				<option value="4">Customer ID</option>
			</select><br>
			<label>Search Parameter: </label><input type="text" id="csLParam"><br>
			<button id="csLSearch">Search</button>
			<button id="csLClose">Close</button>
			<p style="color: red;" id="csLError"></p>
			<div id="csLResults"></div>
		</div>
		<div id="prodLookupDiv" class="floating">
			<h2>Product ID Lookup</h2>
			<label>Search Type: </label>
			<select id="prLType">
				<option value="2">Name</option>
				<option value="4">Description</option>
				<option value="3">Location</option>
				<option value="1">ID</option>
			</select><br>
			<label>Search Parameter: </label><input type="text" id="prLParam"><br>
			<button id="prLSearch">Search</button>
			<button id="prLClose">Close</button>
			<p style="color: red;" id="prLError"></p>
			<div id="prLResults"></div>
		</div>
		<div id="confirm" class="floating">
			<h2>Are you sure you would like to continue?</h2>
			<p id="confirminfo"></p>
			<h2>Warnings:</h2>
			<p id="warningsdata"></p>
			<button id="continue">Continue</button>
			<button id="cancel">Cancel</button>
			<p style="color: red;" id="error"></p>
			<p style="color: green" id="success"></p>
		</div>
		<div id="ePopup" class="floating">
			<h2>Exchange Information</h2>
			<label>Invoice ID</label><input type="number" min="0" id="eInvoice"><br>
			<button id="eRetrieve">Retrieve Invoice</button>
			<p id="eError" style="color: red;"></p>
			<br>
			<table id="eData" style="margin: auto;">
				<tr id="table_header">
					<th>Line</th>
					<th>Count</th>
					<th>Max Count</th>
					<th>Product ID</th>
					<th>Product Name</th>
				</tr>
			</table>
			<br>
			<button id="eApply">Apply</button>
			<button id="eCancel">Cancel</button>
		</div>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var createButton = document.getElementById("create");
var resetButton = document.getElementById("reset");
var exchangeButton = document.getElementById("exchange");
var error = document.getElementById("error");
var success = document.getElementById("success");

var eText = document.getElementById("eText");
var ePopup = document.getElementById("ePopup");
var eApply = document.getElementById("eApply");
var eCancel = document.getElementById("eCancel");

var eData = document.getElementById("eData");
var eRetrieve = document.getElementById("eRetrieve");
var eInvoice = document.getElementById("eInvoice");
var eError = document.getElementById("eError");
var intEInvoice;

eApply.addEventListener("click",exchangeApply);
eCancel.addEventListener("click",exchangeCancel);
eRetrieve.addEventListener("click",exchangeRetrieve);

var exch = false;

var eEntries = document.getElementById("eEntries");
var ePayments = document.getElementById("ePayments");

var cusLookup = document.getElementById("cusLookup");

var cusLookupDiv = document.getElementById("cusLookupDiv");

var customer = document.getElementById("customer");
var type = document.getElementById("type");
var notes = document.getElementById("notes");
var orig_id = document.getElementById("orig_id");
var date = document.getElementById("date");
var add = document.getElementById("add");
var prodSearch = document.getElementById("prodSearch");
var entries = document.getElementById("entries");

add.addEventListener("click",addRow);
createButton.addEventListener("click",showConfirm);
exchangeButton.addEventListener("click",exchange);
resetButton.addEventListener("click",reset);
prodSearch.addEventListener("click",productLookup);
cusLookup.addEventListener("click",customerLookup);

var csLType = document.getElementById("csLType");
var csLParam = document.getElementById("csLParam");
var csLSearch = document.getElementById("csLSearch");
var csLClose = document.getElementById("csLClose");
var csLError = document.getElementById("csLError");
var csLResults = document.getElementById("csLResults");

csLSearch.addEventListener("click",customerSearch);
csLClose.addEventListener("click",customerLookupClose);

var prodLookupDiv = document.getElementById("prodLookupDiv");
var prLType = document.getElementById("prLType");
var prLParam = document.getElementById("prLParam");
var prLSearch = document.getElementById("prLSearch");
var prLClose = document.getElementById("prLClose");
var prLError = document.getElementById("prLError");
var prLResults = document.getElementById("prLResults");

prLSearch.addEventListener("click",productSearch);
prLClose.addEventListener("click",productLookupClose);

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

updateTotal();

function exchangeRetrieve(){
	clearTable(eData);
	eError.innerHTML = "";
	intEInvoice = 0;
	var invoice = get_invoice(eInvoice.value);
	if(!invoice.success){
		console.log("Error: failed to retrieve invoice data!");
		eError.innerHTML = "Error: failed to retrieve invoice data! Invoice may be invalid";
		return;
	}
	intEInvoice = eInvoice.value;
	for(let i = 0; i < invoice.invoice.entries.length; i++){
		var entry = invoice.invoice.entries[i];
		var r = document.createElement("tr");
		
		createElement(i+1,r);
		createEditableElement(0,r);
		createElement(entry.count,r);
		createElement(entry.product,r);
		var product = get_product(entry.product);
		if(!product.success){
			console.log("Error: failed to retrieve product data!");
			eError.innerHTML = "Error: failed to retrieve some product data!";
		}
		createElement(product.product.name,r);
		
		eData.appendChild(r);
	}
}

function exchangeCancel(){
	exch = false;
	ePopup.style.visibility = "hidden";
	eEntries.style.visibility = "hidden";
	ePayments.style.visibility = "hidden";
	eText.style.visibility = "hidden";
}
function exchangeApply(){
	eError.innerHTML = "";
	if(intEInvoice == 0){
		eError.innerHTML = "An invoice must be selected to do an exchange!";
		return;
	}
	var invoice = get_invoice(intEInvoice);
	if(!invoice.success){
		eError.innerHTML = "Failed to retrieve invoice data!";
		console.log("Error: Failed to retrieve invoice data!");
		return;
	}
	var subtotal = 0;
	var lCount = 0;
	for(var i = 0; i < eData.childNodes.length; i++){
		var child = eData.childNodes[i];
		if(child.nodeName == "TR"){
			var columns = child.childNodes;
			
			var line = strip(columns[0].innerHTML);
			var count = strip(columns[1].innerHTML);
			var max = strip(columns[2].innerHTML);
			var prod = strip(columns[3].innerHTML);
			if(count > max){
				eError.innerHTML = "Line "+line+" Error: Count is greater than max exchange amount";
				return;
			}
			if(count == 0) continue;
			lCount++;
			
			var r = document.createElement("tr");
			createElement(lCount,r);
			createElement(prod,r);
			var ent = invoice.invoice.entries[line-1];
			createElement(ent.original_id,r);
			createElement(count,r);
			createElement(ent.unit_count,r);
			createElement("$"+(ent.unit_price/100),r);
			createElement("$"+(ent.unit_discount/100),r);
			createElement(ent.notes,r);

			eEntries.appendChild(r);

			subtotal += (count / ent.unit_count) * (ent.unit_price - ent.unit_discount);
		}
	}
	var flags = invoice.invoice.flags;
	if(flags == null)
		flags = 0;
	var total = flags & 1 ? subtotal : subtotal * 1.13;
	var pTotal = 0;
	for(var i = 0; i < invoice.invoice.payments.length; i++){
		var remainder = total-pTotal;
		if(remainder < 0) break;
		var payment = invoice.invoice.payments[i];
		var r = document.createElement("tr");
		
		createElement(i+1,r);
		createElement(payment_type_to_string(payment.type),r);
		if(remainder < payment.amount){
			createElement("$"+(Math.round(remainder)/100),r);
		}else{
			createElement("$"+(Math.round(payment.amount)/100),r);
		}
		createElement(payment.identifier,r);
		createElement(payment.notes,r);

		ePayments.appendChild(r);
		pTotal -= payment.amount;
	}
	updateTotal();
	ePopup.style.visibility = "hidden";
}

function exchange(){
	if(!exch){
		exch = true;
		clearTable(eData);
		eInvoice.value = "0";
		eError.innerHTML = "";
		intEInvoice = 0;
		eEntries.style.visibility = "visible";
		ePayments.style.visibility = "visible";
		eText.style.visibility = "visible";
		ePopup.style.visibility = "visible";
	}else{
		exch = false;
		eEntries.style.visibility = "hidden";
		ePayments.style.visibility = "hidden";
		eText.style.visibility = "hidden";
		clearTable(eEntries);
		clearTable(ePayments);
	}
}

function customerSearch(){
	var customers = get_customers(csLType.value,csLParam.value);
	if(!customers.success){
		console.log("Failed to retrieve data!");
		csLError.innerHTML = "An error occurred while processing your request. Error: "+customers.reason;
		return;
	}
	csLResults.innerHTML = "";
	for(let i = 0; i < customers.customers.length; i++){
		if(i == 4){
			var div = document.createElement("div");
			div.className = "oneline";
			var text = document.createElement("p");
			text.innerHTML = "... and "+(customers.customers.length-4)+" more";
			div.appendChild(text);
			csLResults.appendChild(div);
			break;
		}
		var customer = get_customer(customers.customers[i]);
		if(!customer.success){
			console.log("Failed to retrieve some data!");
			csLError.innerHTML = "An error occurred while processing your request. Error: "+customer.reason;
			return;
		}
		var div = document.createElement("div");
		div.className = "oneline";
		var text = document.createElement("p");
		text.innerHTML = "#"+customers.customers[i]+": "+customer.customer['name'];
		div.appendChild(text);
		var button = document.createElement("button");
		button.innerHTML = "Select";
		button.id = customers.customers[i];
		button.addEventListener("click",customerLookupSelect);
		div.appendChild(button);
		csLResults.appendChild(div);
	}
}
function customerLookupSelect(trigger){
	var button = trigger.target;
	var id = button.id;
	customer.value = id;
	cusLookupDiv.style.visibility = "hidden";
}
function customerLookupClose(){
	cusLookupDiv.style.visibility = "hidden";
}

function customerLookup(){
	cusLookupDiv.style.visibility = "visible";
}
function showConfirm(){
	error.innerHTML = "";
	// Info to display to the user before confirming
	var subtotal = getSubtotal();
	var total = getTotal(subtotal);
	var pTotal = getPaymentTotal();
	
	var stop = false;
	var warn = "";

	if(total != pTotal){
		warn += "Totals do not match!<br>";
		stop = true;
	}

	confirmInfo.innerHTML = "Subtotal: "+subtotal+"<br>Total: "+total+"<br>Payment Total: "+pTotal;
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
	if(pcount == 0){
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
function getExchangePTotal(){
	if(!exch)
		return "0.00";
	return calcPaymentTotal(ePayments);
}
function calcPaymentTotal(root){
	var pTotal = 0.00;
	for(var i = 0; i < root.childNodes.length; i++){
		var child = root.childNodes[i];
		if(child.nodeName == "TR"){
			var map = {};
			var columns = child.childNodes;
			var tot = strip(columns[2].innerHTML);
			if(tot.startsWith("$"))
				tot = tot.substring(1);
			pTotal += tot * 1.00;
		}
	}
	pTotal = pTotal.toFixed(2);
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
	if(Math.abs(getTotal(getSubtotal()) - getPaymentTotal()) > 0.02){
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

</script>
