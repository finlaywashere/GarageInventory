var cashLookupDiv = document.getElementById("cashLookupDiv");

var cashLClose = document.getElementById("cashLClose");
var cashResults = document.getElementById("cashLResults");

var cashCallback;

function cashLookupSetup(callback, button){
	cashCallback = callback;
	button.addEventListener("click",cashLookupF);
	cashLClose.addEventListener("click", cashLookupClose);
}

function cashSearch(){
	var locs = get_cash_locations();
	if(!locs.success){
		console.log("Failed to retrieve data!");
		return;
	}
	cashResults.innerHTML = "";
	for(let cash in locs.locations){
		var c = locs.locations[cash];
		var div = document.createElement("div");
		div.className = "oneline";
		var text = document.createElement("p");
		text.innerHTML = "#"+cash+": "+c['name'];
		div.appendChild(text);
		if(cashCallback != null){
			var button = document.createElement("button");
			button.innerHTML = "Select";
			button.id = cash;
			button.addEventListener("click",cashLookupSelect);
			div.appendChild(button);
		}
		cashResults.appendChild(div);
	}
}
function cashLookupSelect(trigger){
	var button = trigger.target;
	var id = button.id;
	cashCallback(id);
	cashLookupDiv.style.visibility = "hidden";
}
function cashLookupClose(){
	cashLookupDiv.style.visibility = "hidden";
}

function cashLookupF(){
	cashSearch();
	cashLookupDiv.style.visibility = "visible";
}

