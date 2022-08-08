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

function getExchangePTotal(){
	if(!exch)
		return 0;
	return calcPaymentTotal(ePayments);
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

			var line = parseInt(strip(columns[0].innerHTML),10);
			var count = parseInt(strip(columns[1].innerHTML),10);
			var max = parseInt(strip(columns[2].innerHTML),10);
			var prod = parseInt(strip(columns[3].innerHTML),10);
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
function getExchangePTotal(){
	if(!exch)
		return 0;
	return calcPaymentTotal(ePayments);
}
