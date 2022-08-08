var cusLookupDiv = document.getElementById("cusLookupDiv");

var csLType = document.getElementById("csLType");
var csLParam = document.getElementById("csLParam");
var csLSearch = document.getElementById("csLSearch");
var csLClose = document.getElementById("csLClose");
var csLError = document.getElementById("csLError");
var csLResults = document.getElementById("csLResults");

csLSearch.addEventListener("click",customerSearch);
csLClose.addEventListener("click",customerLookupClose);

var csCallback;

function csLookupSetup(callback, button){
	csCallback = callback;
	button.addEventListener("click",customerLookup);
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
	csCallback(id);
	cusLookupDiv.style.visibility = "hidden";
}
function customerLookupClose(){
	cusLookupDiv.style.visibility = "hidden";
}

function customerLookup(){
	cusLookupDiv.style.visibility = "visible";
}

