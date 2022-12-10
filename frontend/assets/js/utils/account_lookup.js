var accLookupDiv = document.getElementById("accLookupDiv");

var accLClose = document.getElementById("accLClose");
var accResults = document.getElementById("accLResults");

var accCallback;

function accLookupSetup(callback, button){
	accCallback = callback;
	button.addEventListener("click",accountLookup);
	accLClose.addEventListener("click", accountLookupClose);
}

function accountSearch(){
	var accounts = get_accounts();
	if(!accounts.success){
		console.log("Failed to retrieve data!");
		return;
	}
	accResults.innerHTML = "";
	for(let account in accounts.accounts){
		var acc = accounts.accounts[account];
		var div = document.createElement("div");
		div.className = "oneline";
		var text = document.createElement("p");
		text.innerHTML = "#"+account+": "+acc['name'];
		div.appendChild(text);
		if(accCallback != null){
			var button = document.createElement("button");
			button.innerHTML = "Select";
			button.id = account;
			button.addEventListener("click",accountLookupSelect);
			div.appendChild(button);
		}
		accResults.appendChild(div);
	}
}
function accountLookupSelect(trigger){
	var button = trigger.target;
	var id = button.id;
	accCallback(id);
	accLookupDiv.style.visibility = "hidden";
}
function accountLookupClose(){
	accLookupDiv.style.visibility = "hidden";
}

function accountLookup(){
	accountSearch();
	accLookupDiv.style.visibility = "visible";
}

