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
			<button id="prodCreateB">Create Product</button>
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
		<?php
			require "../utils/customer_lookup.php";
			require "../utils/product_lookup.php";
		?>
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
<script src="/inventory/frontend/assets/js/invoice/exchange.js"></script>
<script src="/inventory/frontend/assets/js/invoice/create_invoice.js"></script>
