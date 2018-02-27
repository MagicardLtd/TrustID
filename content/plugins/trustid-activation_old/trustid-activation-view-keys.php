<?php
	global $displayKeys;
    if (isset($displayKeys))
        include('trustid-activation-keys.php');
    ?>

<style>
	span.hint {color:#aaa;}
	label {font-weight:bold; display:inline-block; width:100px; padding-right:10px;}
	label.error {width:auto; padding-left:10px; color:red;}
	input, select {display:inline-block; width:150px;}
</style>

<div class="wrap">
	<div id="icon-tools" class="icon32"></div><h2>View registration keys</h2>
</div>
<div class="wrap">
	<br>

	View or download the list of registration keys or activations.
	<br>&nbsp;<br>
	<h3>Registration Keys</h3>
	<input class="button-primary" type="submit" id="viewkeys" name="viewkeys" value="View  keys">
	<input class="button-secondary" type="submit" id="exportkeys" name="exportkeys" value="Export keys to CSV">
	<h3>Activation Records</h3>
	<input class="button-primary" type="submit" id="viewactivations" name="viewactivations" value="View activations">
	<input class="button-secondary" type="submit" id="exportactivations" name="exportactivations" value="Export activations to CSV">
	<br>&nbsp;<br>
</div>
