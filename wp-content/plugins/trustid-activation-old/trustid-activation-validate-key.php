<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php' );


$Key = $_GET['q']; //the original activation key
$newEd = $_GET['e']; // the edition it's being upgraded to
$BoxNo = $_GET['b']; // the input box number that the $resultText needs to be linked to

$validation = trustid_activate_validateKey($Key, $newEd);

switch ($validation["result"]){
	case "Invalid Key":
		$resultText = '&nbsp;&nbsp;&nbsp;This Key does not exist.';
		break;
		
	case "Invalid edition":
		$editionName = edition_name($validation["keyEdition"]);
		$newedName = edition_name($newEd); 
		$resultText = '&nbsp;&nbsp;&nbsp;This is a <b>'.$editionName.'</b> Key. Cannot upgrade it to '.$newedName.'.';
		break;
		
	case "Already upgraded":
		$editionName = edition_name($validation["upgradeKeyEdition"]);
		$resultText = '&nbsp;&nbsp;&nbsp;This Key has already been upgraded to a <b>'.$editionName.'</b> Key : '.$validation->upgradeKey;
		break;
		
	case "Valid Key":
		$editionName = edition_name($validation["keyEdition"]);
		$resultText = '<strong><span style="color:#0C0;">&nbsp;&nbsp;&nbsp;OK! This is a valid - '.$editionName.' - Key.</span></strong>';
		break;
		
	default:
		$resultText = "&nbsp;&nbsp;&nbsp;Unknown key";
}


echo $resultText;


function edition_name($EditionNo = 0){ //Turns the edition number into plain English
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