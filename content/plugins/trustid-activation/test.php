<?php
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

  function tid_process_request($web = false) {

    $requestCode = $_POST['RequestCode'];
    require_once dirname(__FILE__).'/trustid-activation-request-response.php';
    $responseCode = generateResponseCode($requestCode);
    $email = 'test@test.com';
    $status = 0;

    header('HTTP/1.0 200 OK');
    header('Content-type: application/xml');
    echo '<?xml version="1.0" encoding="utf-8"?><Activation ';
    if ($requestCode != '') {
      echo 'RequestCode="' . $requestCode . '" ';
    }
    if ($responseCode != '') {
      echo 'ResponseCode="' . $responseCode . '" ';
    }
    if ($email != '') {
      echo 'Email="' . $email . '" ';
    }
    echo 'Status="' . $status . '" />';
  }

?>
