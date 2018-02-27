<!DOCTYPE html>
<html>

<?php
	global $wpdb, $table_name;

	$plugins_url = plugin_dir_url( __FILE__ );
	global $displayKeys;
    if (isset($displayKeys))
        include('trustid-activation-keys.php');

	$url = "/wp/wp-admin/admin.php?";
    foreach($_GET as $key=>$value)
    {
		if (strpos($key,"search") === false)
		{
			if($key == "page")
				$url.= urlencode($key)."=".urlencode($value);
			else
				if($key !== "paged")
					$url.= "&".urlencode($key)."=".urlencode($value);
		}
    }
?>

<style>
	span.hint {color:#aaa;}
	label {font-weight:bold; display:inline-block; width:150px; padding-right:10px;}
	label.error {width:auto; padding-left:10px; color:red;}
	input, select {display:inline-block; width:150px;}
</style>

<div class="wrap">
	<div id="icon-tools" class="icon32"></div><h2>Generate registration keys</h2>
</div>
<div class="wrap">
	<br>
	This form will generate a quantity of registration keys and store them in the key database.<br>
	<form method="POST" id="generate-keys" action="<?php echo $url; ?>">
		<ul>
			<li>
				<label for="qty">Quantity of keys</label>
				<input type="text" name="qty" onkeyup="addInput('qty',this.value);" autocomplete="off">
				<br><span class="hint">How many keys to generate</span>
				<br>&nbsp;<br>
			</li>
			<li>
				<label for="edition">Software edition</label>
				<select id="edition" name="edition" onchange="newEdition(this.value);">
					<option value="" selected  > </option>
					<option value="0" >Classic </option>
					<option value="1" >Premium</option>
					<option value="2" >Pro</option>
					<option value="3" >Pro Smart</option>
				</select>
				<br><span class="hint">Which edition of the software</span>
				<br>&nbsp;<br>
			</li>
			<li>
				<label for="upgrade">Version number</label>
				<input type="text" name="upgrade" value="0" readonly>
				<br><span class="hint">Which version number of the software or upgrade version</span>
				<br>&nbsp;<br>
			</li>
			<li>
				<label for="distributor">Distributor</label>
				<input type="text" name="distributor">
				<br><span class="hint">Distribution company or method for these keys</span>
				<br>&nbsp;<br>
			</li>
			<li>
				<label for="purpose">For Sale or Demo?</label>
				<select name="purpose">
					<option value="" selected > </option>
					<option value="0" >Sale</option>
					<option value="1" >Dealer Demo</option>
				</select>
				<br><span class="hint">For sale or for dealer demonstration/support use?</span>
				<br>&nbsp;<br>
			</li>
			<li>

				<label for="upgrading">Upgrading from Classic or Pro?</label>
				<select name="upgrading" onchange="addInput('upgrading',this.value);">
					<option value="" selected  > </option>
					<option value="0" >No</option>
					<option value="1" >Yes</option>
				</select>
				<br><span class="hint">Upgrading from a lower edition?</span>
				<br>
			</li>

			<div id="originalKeyArea">
			<br>
			</div>

			<br>
			<li>
			<input class="button-primary" type="submit" id="generate" name="generate" value="Generate keys">
			</li>
			<br>

		</ul>
	</form>

	</html>

	<script>

	var quantity = 0; //The quantity of new keys required.
	var upgrading = 0;  //0 = new keys are NOT upgrades, 1 = new keys ARE upgrades.
	var edition = ""; //The required edition of the new keys.
	var counter = 0;  //Holds a count of new input fields that are created for any keys that are to be upgraded.

	function newEdition(val){
		edition = val;  				//If the required edition is changed...
		for (var i=0; i<counter; i++){  //...initiate re-validation of each original key to be upgraded (if any)
			var fieldVal = document.getElementById("originalKey["+i+"]").value;
			checkKey(fieldVal,i);
			document.getElementById("originalKey["+i+"]").focus();
			document.getElementById("edition").focus();
		}
	}

	function addInput(field, val){
	if (field == "qty"){
		quantity = val;
	}
	if (field == "upgrading"){
		upgrading = val;
	}
	var fieldsReq = quantity*upgrading;
	if (fieldsReq > counter) {  //Create new input fields (one for each original activation key that needs to be upgraded)...
		for (var i=counter; i<fieldsReq; i++){
			var newdiv = document.createElement('div');
			newdiv.innerHTML = "<label for='originalKey["+counter+"]' >Original Activation Key "+(counter+1)+":</label> <input type='text' id='originalKey["+counter+"]' name='originalKey["+counter+"]' onkeyup='checkKey(this.value,\""+counter+"\")' autocomplete='off' required='true' validKey='true' > <font for='originalKey["+counter+"]' id='txtHint"+counter+"'>&nbsp;&nbsp;&nbsp;Key info will be listed here...</font><br>";
			newdiv.setAttribute("id", "origKey_" + counter);
			document.getElementById("originalKeyArea").appendChild(newdiv);
			counter++;
		}
		return;
	}else {  //Delete input fields that are no longer required (i.e. if the qty changes downwards)...
		for (var i=counter; i>fieldsReq; i--){
			counter--;
			var child = document.getElementById("origKey_" + counter);
			var parent = document.getElementById("originalKeyArea");
			parent.removeChild(child);
		}
	}
	}

	function checkKey(str,boxNo) {  //Test that the entered value is a valid original activation key...
	if (str == "") {
		document.getElementById("txtHint"+boxNo).innerHTML = ">&nbsp;&nbsp;&nbsp;Key info will be listed here...";
		return;
	} else {
		var pattern = /^([0-9]{4})-([0-9]{4})-([0-9]{4})-([0-9]{4}$)/;
		if (pattern.test(str)){
			if (duplicateCheck(str,boxNo)){
				document.getElementById("txtHint"+boxNo).innerHTML = "&nbsp;&nbsp;&nbsp;You've entered this key multiple times";
				return;
			}
			if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			}
			else {
				// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById("txtHint"+boxNo).innerHTML = xmlhttp.responseText;
				}
			}
			xmlhttp.open("GET","<?php echo $plugins_url; ?>trustid-activation-validate-key.php?q="+str+"&e="+edition+"&b="+boxNo,false);
			xmlhttp.send();
		}
		else {
		pattern = /(^[0-9\-]{1,19}$)/;
			if (pattern.test(str)){
				document.getElementById("txtHint"+boxNo).innerHTML = "&nbsp;&nbsp;&nbsp;keep typing...";
			}
			else{
				document.getElementById("txtHint"+boxNo).innerHTML = "&nbsp;&nbsp;&nbsp;Invalid entry!";
			}
		}
	}
	}

	function duplicateCheck(str,boxNo){
		for (var i=0; i<counter; i++){
			if (i != boxNo){
				var fieldVal = document.getElementById("generate-keys").elements.namedItem("originalKey["+i+"]").value;
				if(fieldVal.search(str) >=0){
					return true;
				}
			}
		}
		return false;
	}


	jQuery.validator.addMethod("validKey",function(value,element,param)
	{  //Add a new validation method to the form validator (to stop the form being submitted if the entered keys aren't valid)
		var fieldID = element.name;
                var posStart = fieldID.search("[")+1;
		var posEnd = fieldID.search("]")-1;
		var fieldNo = fieldID.substring(posStart,posEnd);
		var hint = document.getElementById("txtHint"+fieldNo).innerHTML;
		var str = "OK! This is a valid";
		if(hint.search(str) >=0)
		{
			return true;
		}
		return false;
	},"Please enter a valid Activation Key...");


	jQuery( "#generate-keys" ).validate({
		onkeyup: true,
		rules: {
			qty: {
			required: true,
			digits: true,
			range: [1,100]
			},
			edition: {
			required: true,
			range: [0,3]
			},
			upgrade: {
			required: true,
			digits: true,
			range: [0,31]
			},
			distributor: {
			required: true,
			rangelength: [2,200]
			},
			purpose: {
			required: true,
			digits: true,
			range: [0,1]
			},
			upgrading: {
			required: true,
			digits: true,
			range: [0,1]
			},
			originalKey:{
			required:true,
			validKey:true
			}
		}
	});


	jQuery( "generate" ).click(function() {
		return confirm('Are you sure you want to generate ' + form.qty.value + ' key(s)?')
	});
	</script>
</div>
