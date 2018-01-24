<?php

// Base 32 alphabet (using RFC 4648)
$base32Alphabet = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567');
$base32InvAlphabet = array_flip($base32Alphabet);


// Verifies that this is a valid request code
// Returns true if request code is accepted, or a string describing the reason for rejection
define('REQ_CODE_LEN', 22);

define('PROD_IDS_START', 0);
define('PROD_IDS_LEN_B32', 1);
define('BRAND_ID_LEN_B10', 1);
define('ED_ID_LEN_B10', 1);

define('UPGRADE_ID_START', 1);
define('UPGRADE_ID_LEN_B32', 1);
define('UPGRADE_ID_LEN_B10', 2);

define('COPY_ID_START', 2);
define('COPY_ID_LEN_B32', 7);
define('COPY_ID_LEN_B10', 10);

define('REG_CHECKSUM_START', 9);
define('REG_CHECKSUM_LEN_B32', 2);
define('REG_CHECKSUM_LEN_B10', 2);

define('TIMESTAMP_START', 11);
define('TIMESTAMP_LEN_B32', 8);

// The timestamp is defined as the number of milliseconds elapsed since // midnight, January 1, 2013 (UTC).
define('TIMESTAMP_EPOCH', '2013-01-01 00:00:00');


/*integer*/ function validateRequestCode($web,$requestCode,$email,$fname,$lname,$cname,$country)
{

	global $wpdb, $table_name, $table_name_activations, $activationId,$key;
	
	$result = array('installed'=>'N/A', 'status' => '');

	// Verify Length
	if (strlen($requestCode) != REQ_CODE_LEN)
	{
		// Request code is the wrong length.
		$result['status'] = 2;
		return $result;
	}

	// Convert from a string to a numeric representation
	$reqCodeArray = base32StringToArray($requestCode);
	if ($reqCodeArray === false)
	{
		// Unrecognized character in request code.
		$result['status'] = 2;
		return $result;
	}

	// Verify checksum
	if (!CheckLuhnChecksum($reqCodeArray, 32))
	{
		// Request code checksum failed; please check for a typo.
		$result['status'] = 2;
		return $result;
	}

	// Extract all the various parts of the registration key
	$productIDs = $reqCodeArray[PROD_IDS_START];	// First character
	$brandID = ($productIDs & 0x18) >> 3;			// Higher 2 bits (of 5)
	$brandID = str_pad($brandID, BRAND_ID_LEN_B10, "0", STR_PAD_LEFT);
	$editionID = ($productIDs & 0x7);				// Lower 3 bits (of 5)
	$editionID = str_pad($editionID, ED_ID_LEN_B10, "0", STR_PAD_LEFT);

	$upgradeID = $reqCodeArray[UPGRADE_ID_START];
	$upgradeID = str_pad($upgradeID, UPGRADE_ID_LEN_B10, "0", STR_PAD_LEFT);

	$copyIDBase32Array = array_slice($reqCodeArray, COPY_ID_START, COPY_ID_LEN_B32);
	$copyIDBase10Array = convertArrayBase($copyIDBase32Array, 32, 10);
	if (count($copyIDBase10Array) > COPY_ID_LEN_B10)
	{
		// Copy ID out of range.
		$result['status'] = 2;
		return $result;
	}
	$copyID = implode($copyIDBase10Array);
	$copyID = str_pad($copyID, COPY_ID_LEN_B10, "0", STR_PAD_LEFT);

	$regChecksumBase32Array = array_slice($reqCodeArray, REG_CHECKSUM_START, REG_CHECKSUM_LEN_B32);
	$regChecksumBase10Array = convertArrayBase($regChecksumBase32Array, 32, 10);
	if (count($regChecksumBase10Array) > REG_CHECKSUM_LEN_B10)
	{
		// Registration checksum out of range.
		$result['status'] = 2;
		return $result;
	}
	$regChecksum = implode($regChecksumBase10Array);
	$regChecksum = str_pad($regChecksum, REG_CHECKSUM_LEN_B10, "0", STR_PAD_LEFT);

	// String them all together into the complete registration key
	$regKey = $brandID . $editionID . $upgradeID . $copyID . $regChecksum;

	// Verify checksum
	if (!CheckLuhnChecksum(str_split($regKey, 2), 100))
	{
		// Registration key checksum failed.
		$result['status'] = 2;
		return $result;
	}

	// You could also extract the installation timestamp and do something with it if you want
	// It's up to 40 bits long, which means it might not fit in an int (depending on hardware)
	$timestampBase32Array = array_slice($reqCodeArray, TIMESTAMP_START, TIMESTAMP_LEN_B32);

    // Extract the base-32 timestamp from the request code, then convert it to // base 10.
	$timestampBase10Array = convertArrayBase($timestampBase32Array, 32, 10);

    // Convert it to a string.
    $timestampBase10Str = implode($timestampBase10Array);

    // Split the string into two parts: one containing the number of whole // seconds elapsed since the epoch began, and the other one containing the 
	// number of leftover milliseconds.
    $timestampSecondsStr = substr($timestampBase10Str, 0, -3);
	$timestampMillisecondsStr = substr($timestampBase10Str, -3);

    // Use a PHP DateTime object to help format the timestamp. Note that // DateTime does not support fractional seconds, so we must initialize it 
	// with just the number of whole seconds elapsed since the epoch began.
    // Warning: Do not use the contents of this DateTime object as the whole 
	// timestamp, since that would lose a huge amount of precision.
    $timestamp = new DateTime(TIMESTAMP_EPOCH);
	$timestamp->add(new DateInterval('PT' . $timestampSecondsStr . 'S'));



    // Format the timestamp, manually tacking on the milliseconds at the end.
    $formattedTimestamp = $timestamp->format('Y-m-d H:i:s') . '.' .$timestampMillisecondsStr;
	
	$result['installed'] = $formattedTimestamp;


	// At this point, the final implementation should compare the registration key to the
	// master list and ensure that it's a valid key in good standing (doesn't have too
	// many activation attempts, hasn't been reported stolen, etc.). If any of those tests
	// fail then this function should return 3 for activation failure.



	// Check if the copyid exists in the db
	$key = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name  WHERE copyId = %s",$copyID));

	// No record of this being a valid copy id
	if (empty($key))
	{
		$result['status'] = 3;
		return $result;
	}


	// Brand mismatch
	if (!isset($key->brandId) || $key->brandId != $brandID)
	{
		$result['status'] = 3;
		return $result;
	}

	// Edition Mismatch
	if (!isset($key->editionId) || $key->editionId != $editionID)
	{
		$result['status'] = 3;
		return $result;
	}

	// Upgrade mismatch
	if (!isset($key->upgradeId) || $key->upgradeId != $upgradeID)
	{
		$result['status'] = 3;
		return $result;
	}

	// Check if activation is allowed with this key (blocked or already used)
	if (!isset($key->active) || $key->active != 1)
	{
		$result['status'] = 3;
		return $result;
	}

	// OK everything looks good so store the activation in the database.

	$wpdb->insert(
		$table_name_activations ,
		array(
			'registrationId' => $key->id,
			'registrationKeyk' => $key->registrationKey,
			'activated' => time(),
			'email' => !empty($email) ? $email : "",
			'fname' => !empty($fname) ? $fname : "",
			'lname' => !empty($lname) ? $lname : "",
			'cname' => !empty($cname) ? $cname : "",
			'country' => !empty($country) ? $country : "",
			'ip' => $_SERVER['REMOTE_ADDR'],
			'installed' => $formattedTimestamp,
			'requestCode' => $requestCode,
			'manualActivate' => $web,
			'state' => 0,
		),
		array(
			'%s',
			'%s',
			'%d',
			'%s',
            '%s',
            '%s',
            '%s',
            '%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s'
		)
	);

	// *******
	// Temporary solution to ease problems caused by the 'random installation date' future dates currently occurring in the Trust ID application - JH
	//Status will be set to blocked (0) only if the installation date is less than 30 days in the future
	$now = date('Y-m-d H:i:s', time());
	$now1 = new DateTime($now);
	$interval = $now1->diff($timestamp); // get the difference between now and the reported installation time & date
	$daysDiff = $interval->format('%R%a'); //turn it into number of days difference
	
	if($daysDiff < 30)  
		$wpdb->query($wpdb->prepare("UPDATE $table_name SET active = 0 WHERE id = %d",$key->id));
	
	//$wpdb->query($wpdb->prepare("UPDATE $table_name SET active = 0 WHERE id = %d",$key->id));
	// ********

	// Set the firstUsedDate for this key if not already set
	//if (!isset($key->firstUsed))
	//$wpdb->query($wpdb->prepare("UPDATE $table_name SET firstUsed = time() WHERE id = %d",$key->id));
	
	// Increment the number of activations for this key
	$activations = $key->activationCount;
	$activations = ++$activations;
	$wpdb->query($wpdb->prepare("UPDATE $table_name SET activationCount =  $activations WHERE id = %d",$key->id));
	
	$activationId = $wpdb->insert_id;

	// If no problems have been encountered up to this point, the request code appears valid
	
	$result['status'] = 0;
	return $result;
	
}

// Generates an appropriate response code for the given registration key and request code
// This function does not check the validity of the request code; use validateRequestCode() for that
// Returns the response code
define('RESPONSE_CODE_SALT', 'N78Q234H8ZVS34TD');
define('RESPONSE_CODE_LENGTH', 13);
define('CHUNK_SIZE', 2);	// 2 hex nibbles = 8 bits (chosen in case of byte-ordering issues)
/*string*/ function generateResponseCode(/*string*/ $requestCode)
{
	global $wpdb,$table_name_activations ,$activationId;

	// Concatenate registration key, request code, and secret salt and hash them
	$hashInput = $requestCode . RESPONSE_CODE_SALT;
	$hashOutput = hash('sha256', $hashInput);		// Note: output is a hexadecimal string

	// Hash output is longer than we'd like to force the user to type, so shorten it
	// by splitting it into 4 pieces and XOR-ing them together
	// We're also converting from a hex string to an array of bytes because we need
	// a numeric type for the XOR and it's more convenient for the base change later
	$hashChunks = str_split($hashOutput, CHUNK_SIZE);
	$finalChunkCount = count($hashChunks) / 4;
	$shortHashArray = array();
	for ($i = 0; $i < $finalChunkCount; ++$i)
	{
		$first =	hexdec($hashChunks[$i]);
		$second =	hexdec($hashChunks[$i + $finalChunkCount]);
		$third =	hexdec($hashChunks[$i + $finalChunkCount * 2]);
		$fourth =	hexdec($hashChunks[$i + $finalChunkCount * 3]);
		$shortHashArray[$i] = $first ^ $second ^ $third ^ $fourth;
	}

	// Convert output to base-32 by peeling off 5 bits at a time
	$bitBucket = 0;
	$bitCount = 0;
	$currentChunk = $finalChunkCount - 1;
	$shortHashBase32 = array();
	for ($i = 0; $i < RESPONSE_CODE_LENGTH; ++$i)
	{
		// Ensure we have enough bits in the bucket
		if ($bitCount < 5 && $currentChunk >= 0)
		{
			$bitBucket |= $shortHashArray[$currentChunk] << $bitCount;
			--$currentChunk;
			$bitCount += 4 * CHUNK_SIZE;
		}
		// Take 5 bits from the bucket
		$shortHashBase32[] = $bitBucket & 0x1f;		// Lowest 5 bits from bucket
		$bitBucket = $bitBucket >> 5;
		$bitCount -= 5;
	}
	$shortHashBase32 = array_reverse($shortHashBase32);	// Make it big-endian

	// Convert from numbers to text
	$responseCode = base32ArrayToString($shortHashBase32);

	if (!empty($activationId))
	{
		$wpdb->update(
			$table_name_activations ,
			array(
				'responseCode' => $responseCode,
			),
			array( 'id' => $activationId ),
			array(
				'%s',
			),
			array( '%d' )
		);
	}

	return $responseCode;
}



// Takes an array of integer "digits" in base 32 and converts them into a base-32 encoded string
// Returns false if the array contains an invalid value
/*string*/ function base32ArrayToString(/*array*/ $data)
{
	global $base32Alphabet;
	$result = "";
	for ($i = 0; $i < count($data); ++$i)
	{
		$digit = (int)$data[$i];	// PHP is lax with data types; make sure this is treated as numeric
		if (array_key_exists($digit, $base32Alphabet)) 	$result .= $base32Alphabet[$digit];
		else return false;
	}
	return $result;
}

// Takes a base-32 encoded string and turns it into an array of integer "digits"
// Returns false if the string contains an invalid character
/*array*/ function base32StringToArray(/*string*/ $str)
{
	global $base32InvAlphabet;
	$result = array();
	foreach(str_split($str) as $letter)
	{
		if (array_key_exists($letter, $base32InvAlphabet))	$result[] = $base32InvAlphabet[$letter];
		else return false;
	}
	return $result;
}



// Converts a number represented as an array of integer "digits" into a different base
// Input and output are big-endian
/*array*/ function convertArrayBase(/*array*/ $number, /*int*/ $fromBase, /*int*/ $toBase)
{
	$output = array();
	$digitsLeft = true;
	while ($digitsLeft)
	{
		$digitsLeft = false;		// Done after this iteration UNLESS we leave something behind

		// Perform a long division
		$remainder = 0;
		for ($j = 0; $j < count($number); ++$j)
		{
			$number[$j] += $remainder * $fromBase;
			$remainder = $number[$j] % $toBase;
			$number[$j] = (int)($number[$j] / $toBase);
			if ($number[$j] > 0) $digitsLeft = true;
		}
		$output[] = $remainder;
	}
	return array_reverse($output);	// Make it big-endian
}


