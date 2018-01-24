

<style>
	span.hint {color:#aaa;}
	label {font-weight:bold; display:inline-block; width:100px; padding-right:10px;}
	label.error {width:auto; padding-left:10px; color:red;}
	input, select {display:inline-block; width:150px;}
</style>

<div class="wrap">
	<div id="icon-tools" class="icon32"></div><h2>Generated registration keys</h2>
</div>
<div class="wrap">
	<br>
	Here are the keys that have just been generated for use by <b><?= $_POST['distributor']?>.</b><br>
	<ul>
		<?php
		$edition = edition_text(substr($displayKeys[0]["newKey"],1,1));
		echo "<li><b>".$edition." edition keys...</b></li>";
		?> <ol> <?php
		if(empty($displayKeys[0]["originalKey"]))
			foreach($displayKeys as $key) :?>
				<li>
					<?= $key["newKey"] ?>
				</li>
			<?php endforeach;
		else
		{
			$originalEdition = edition_text(substr($displayKeys[0]["originalKey"],1,1));
			foreach($displayKeys as $key) :?>
				<li>
					<?= $key["newKey"]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- upgrade key for original ".$originalEdition." key: ".$key["originalKey"] ?>
				</li>
			<?php endforeach;
		} ?>
		</ol>
	</ul>
</div>

<?php
function edition_text($EditionNo = 0){ //Turns the edition number into plain English
	switch ($EditionNo){
			case 0:
				$edition = "Classic";
				break;
			case 1:
				$edition = "Premium";
				break;
			case 2:
				$edition = "Pro";
				break;
			case 3:
				$edition = "Pro-Smart";
				break;
			default:
				$edition = "unknown";
		}
    return $edition;
}
?>

<?php exit;