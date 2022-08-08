var prodLookupDiv = document.getElementById("prodLookupDiv");
var prodCreateDiv = document.getElementById("prodCreateDiv");

var prLType = document.getElementById("prLType");
var prLParam = document.getElementById("prLParam");
var prLSearch = document.getElementById("prLSearch");
var prLClose = document.getElementById("prLClose");
var prLError = document.getElementById("prLError");
var prLResults = document.getElementById("prLResults");

prLSearch.addEventListener("click",productSearch);
prLClose.addEventListener("click",productLookupClose);

function prodLookupSetup(button, cButton){
	button.addEventListener("click",productLookup);
	cButton.addEventListener("click",productCreate);
	bindClose(productCreateClose);
}

function productCreate(){
	prodCreateDiv.style.visibility = "visible";
}
function productCreateClose(){
	prodCreateDiv.style.visibility = "hidden";
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
