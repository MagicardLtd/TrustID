<?php

// Designed to be called by software for automatic activation.
// Use HTTP POST with the following parameters:
//  RequestCode The activation request code.

// Example:
//      https://server/directory/activate.php
//      RequestCode=KAAFBO44BBIAB53F67FA3X

// Result:
//      <*xml version="1.0" encoding="utf-8"*>
//      <Activation RequestCode="KAAFBO44BBIAB53F67FA3X" ResponseCode="BT665BZY5OFMC" Status="0" />
//      (where each "*" character in the XML header above is a "?" character)



require_once(dirname(__FILE__)."/../../../wp-load.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($bypassPostCheck)) {
    tid_process_request();
  }
} else if (!isset($bypassPostCheck)) {
  header('HTTP/1.0 405 Method Not Allowed');
  header('Allow: POST');
  header('Content-type: text/html');
  echo '<!DOCTYPE html><html><body><p>405 Method Not Allowed</p></body></html>';
}


if (!empty($_REQUEST['RequestCode']))
    $requestCode = $_REQUEST['RequestCode'];
else if (!empty($_REQUEST['SignUp']))
    $requestCode = $_REQUEST['SignUp'];

if (!empty($_REQUEST['email']))
    $email = $_REQUEST['email'];
else if (!empty($_REQUEST['Email']))
    $email = $_REQUEST['Email'];

if (!empty($_REQUEST['Fname']))
    $fname = $_REQUEST['Fname'];
else if (!empty($_REQUEST['FirstName']))
    $fname = $_REQUEST['FirstName'];

if (!empty($_REQUEST['Lname']))
    $lname = $_REQUEST['Lname'];
else if (!empty($_REQUEST['LastName']))
    $lname = $_REQUEST['LastName'];

if (!empty($_REQUEST['Cname']))
    $cname = $_REQUEST['Cname'];
else if (!empty($_REQUEST['Organization']))
    $cname = $_REQUEST['Organization'];

if (!empty($_REQUEST['Country']))
    $country = $_REQUEST['Country'];
else if (!empty($_REQUEST['CountryCode']))
    $country = $_REQUEST['CountryCode'];



function tid_process_request($web = false){

    global $wpdb,$table_name_activations,$table_name,$key;


    if (!empty($_REQUEST['RequestCode']))
        $requestCode = $_REQUEST['RequestCode'];
    else if (!empty($_REQUEST['SignUp']))
        $requestCode = $_REQUEST['SignUp'];

    if (!empty($_REQUEST['email']))
        $email = $_REQUEST['email'];
    else if (!empty($_REQUEST['Email']))
        $email = $_REQUEST['Email'];

    if (!empty($_REQUEST['Fname']))
        $fname = $_REQUEST['Fname'];
    else if (!empty($_REQUEST['FirstName']))
        $fname = $_REQUEST['FirstName'];

    if (!empty($_REQUEST['Lname']))
        $lname = $_REQUEST['Lname'];
    else if (!empty($_REQUEST['LastName']))
        $lname = $_REQUEST['LastName'];

    if (!empty($_REQUEST['Cname']))
        $cname = $_REQUEST['Cname'];
    else if (!empty($_REQUEST['Organization']))
        $cname = $_REQUEST['Organization'];

    if (!empty($_REQUEST['Country']))
        $country = $_REQUEST['Country'];
    else if (!empty($_REQUEST['CountryCode']))
        $country = $_REQUEST['CountryCode'];


    $status = 1;
    $responseCode = '';
    $activationId = 0;


    if (!empty($requestCode) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
    {
        require_once dirname(__FILE__).'/trustid-activation-request-response.php';

        $result = validateRequestCode($web,$requestCode,$email,$fname,$lname,$cname,$country);

		$status = $result['status'];
		$installed = $result['installed'];

        if ($status === 0)
            $responseCode = generateResponseCode($requestCode);
        else
        {
            $wpdb->insert(
                $table_name_activations ,
                array(
                    'registrationId' => !empty($key->id) ? $key->id : "",
                    'registrationKeyk' => !empty($key->registrationKey) ? $key->registrationKey : "",
                    'activated' => time(),
                    'email' => !empty($email) ? $email : "",
                    'fname' => !empty($fname) ? $fname : "",
                    'lname' => !empty($lname) ? $lname : "",
                    'cname' => !empty($cname) ? $cname : "",
                    'country' => !empty($country) ? $country : "",
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'installed' => $installed,
                    'requestCode' => $requestCode,
					'manualActivate' => $web,
                    'state' => $status,
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
        }
    }
    else
    {
        $status = 2;

        $wpdb->insert(
            $table_name_activations ,
            array(
                'registrationId' => "",
                'registrationKeyk' => "",
                'activated' => time(),
                'email' => !empty($email) ? $email : "",
                'fname' => !empty($fname) ? $fname : "",
                'lname' => !empty($lname) ? $lname : "",
                'cname' => !empty($cname) ? $cname : "",
                'country' => !empty($country) ? $country : "",
                'ip' => $_SERVER['REMOTE_ADDR'],
                'installed' => "unknown",
                'requestCode' => $requestCode,
				'manualActivate' => $web,
                'state' => $status,
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

    }

    if ($web)
    {
        $response = array();

        if ($requestCode != '')
            $response['RequestCode']=$requestCode;

        if ($responseCode != '')
            $response['ResponseCode']=$responseCode;

        if ($email != '')
            $response['Email']=$email;

        $response['Status']=$status;

        return $response;
    }
    else if (!isset($bypassPostCheck))
    {
        header('HTTP/1.0 200 OK');
        header('Content-type: application/xml');

        echo '<?xml version="1.0" encoding="utf-8 ?>"
    <Activation ';

        if ($requestCode != '')
        {
            echo 'RequestCode="' . $requestCode . '" ';
        }

        if ($responseCode != '')
        {
            echo 'ResponseCode="' . $responseCode . '" ';
        }

        if ($email != '')
        {
            echo 'Email="' . $email . '" ';
        }

        echo 'Status="' . $status . '" />
    ';
    }
}
?>
