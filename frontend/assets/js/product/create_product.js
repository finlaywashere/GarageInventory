var createButton = document.getElementById("prodCreate");
var resetButton = document.getElementById("prodReset");
var pError = document.getElementById("prodError");
var pSuccess = document.getElementById("prodSuccess");

var nameI = document.getElementById("prodName");
var desc = document.getElementById("prodDesc");
var notes = document.getElementById("prodNotes");
var pType = document.getElementById("prodType");
var loc = document.getElementById("prodLoc");

var close = document.getElementById("prodClose");

createButton.addEventListener("click",create);
resetButton.addEventListener("click",reset);

function bindClose(callback){
	close.addEventListener("click",callback);
}

function deleteClose(){
	close.remove();
}

function reset(){
	pError.innerHTML = "";
	pSuccess.innerHTML = "";
	nameI.value = "";
	desc.value = "";
	notes.value = "";
	loc.value = "";
	type.selectedIndex = 0;
}

function create(){
	if(nameI.value.length == 0) return;
	pError.innerHTML = "";
	pSuccess.innerHTML = "";

	var result = create_product(nameI.value,desc.value,notes.value,pType.value,loc.value);
	if(!result.success){
		console.log("Failed to retrieve data!");
		pError.value = "An error occurred while processing your request. Error: "+result.reason;
		return;
	}
	var id = result.product;
	pSuccess.innerHTML = "Successfully created product with id <a href=/inventory/frontend/product/get_product.php?id="+id+">"+id+"</a>";
}
