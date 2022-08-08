var createButton = document.getElementById("cusCreate");
var resetButton = document.getElementById("cusReset");
var cusError = document.getElementById("cusError");
var cusSuccess = document.getElementById("cusSuccess");
var cusCloseButton = document.getElementById("cusClose");

var cusNameI = document.getElementById("cusName");
var cusEmail = document.getElementById("cusEmail");
var cusPhone = document.getElementById("cusPhone");
var cusAddress = document.getElementById("cusAddress");
var cusNotes = document.getElementById("cusNotes");
var cusType = document.getElementById("cusType");


createButton.addEventListener("click",cusCreate);
resetButton.addEventListener("click",cusReset);

var cusCallback;

function deleteCusClose(){
	cusCloseButton.remove();
}
function cusCreateSetup(hook, closeCallback){
	cusCallback = hook;
	cusCloseButton.addEventListener("click",closeCallback);
}

function cusReset(){
	cusError.innerHTML = "";
	cusSuccess.innerHTML = "";
	cusNameI.value = "";
	cusEmail.value = "";
	cusPhone.value = "";
	cusAddress.value = "";
	cusNotes.value = "";
	cusType.selectedIndex = 0;
}

function cusCreate(){
	if(cusNameI.value.length == 0) return;
	cusError.innerHTML = "";
	cusSuccess.innerHTML = "";
	var result = create_customer(cusNameI.value,cusEmail.value,cusPhone.value,cusAddress.value,cusNotes.value,cusType.value);
	if(!result.success){
		console.log("Failed to retrieve data!");
		cusError.value = "An error occurred while processing your request. Error: "+result.reason;
		return;
	}
	var id = result.customer;
	cusSuccess.innerHTML = "Successfully created customer with id <a href=/inventory/frontend/customer/get_customer.php?id="+id+">"+id+"</a>";
	if(cusCallback != undefined){
		cusCallback(id);
	}
}
