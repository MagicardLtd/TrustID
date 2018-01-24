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
	<div id="icon-tools" class="icon32"></div><h2>Download Registration Keys or Activations</h2>
</div>
<div class="wrap">
	<br>

	View or download the list of registration keys or activations.
	<br>&nbsp;<br>
	<h3>Registration Keys</h3>
	<input class="button-primary" type="submit" id="exportkeys" name="exportkeys" value="Export keys to CSV">
	<h3>Activation Records</h3>
	<input class="button-primary" type="submit" id="exportactivations" name="exportactivations" value="Export activations to CSV">
	<br>&nbsp;<br>
</div>
