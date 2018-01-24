<?php

    global $wpdb, $table_name_activations, $table_name;
    date_default_timezone_set('UTC');

    $sql = "SELECT COUNT(*) FROM 'tid_trustid_activationss'";
    echo "Record count: ".$wpdb->get_var( $sql );

    $results = $wpdb->get_results("SELECT * FROM 'tid_trustid_activations'");
    foreach ($results as $result) {
         echo '<p>' .$result->id. '</p>';
    }

    $sql = "SHOW TABLES LIKE '%'";
    $results = $wpdb->get_results($sql);

    foreach($results as $index => $value) {
        foreach($value as $tableName) {
            echo $tableName . '<br />';
        }
    }
  // $from = "0" ;
  //   $to = time();
  //   $writeSearchField = 'WHERE a.id LIKE %s AND a.activated BETWEEN %d AND %d';
	// 	$searchTerm = "%%";
  //
  //   $display_items = $wpdb->get_results($wpdb->prepare("SELECT
	// 	a.id,
	// 	CASE
	// 		WHEN a.registrationId > 0 THEN k.registrationKey
	// 		ELSE ''
	// 	END AS registrationKey,
	// 	a.registrationId,
	// 	a.fname,
	// 	a.lname,
	// 	-- $restricted_info
  //   a.email,
	// 	a.cname,
	// 	a.country,
	// 	a.ip,
	// 	a.installed,
	// 	FROM_UNIXTIME(a.activated) AS activated,
	// 	a.requestCode,
	// 	a.responseCode,
	// 	CASE
	// 		WHEN a.manualActivate = 1 THEN 'manual'
	// 		WHEN a.manualActivate = 0 THEN 'automatic'
	// 		else a.manualActivate
	// 	END AS manualActivate,
	// 	CASE
	// 		WHEN a.state = 0 THEN 'Success'
	// 		WHEN a.state = 1 THEN 'Server error'
	// 		WHEN a.state = 2 THEN 'Invalid request'
	// 		WHEN a.state = 3 THEN 'Activation denied'
	// 		else a.state
	// 	END AS state
	// 	FROM $table_name_activations a
	// 	LEFT JOIN $table_name k
	// 		ON (k.id = a.registrationId)
	// 	$writeSearchField",$searchTerm,$from,$to), ARRAY_A);
  //
  //   echo "{$writeSearchField} - search: {$searchTerm} from: {$from} To: {$to}";


?>
