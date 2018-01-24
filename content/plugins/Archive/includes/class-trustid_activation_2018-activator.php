<?php

 class Trustid_activation_2018_Activator {

	public static function activate() {
		global $wpdb,$table_name_activations,$table_name;
    $charset_collate = $wpdb->get_charset_collate();

		// Create trustid_keys table if not existing
		$sql = "CREATE TABLE IT NOT EXISTS `$table_name ` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`registrationKey` varchar(19) NOT NULL,
			`distributor` varchar(200) NOT NULL,
			`purpose` tinyint(1) unsigned NOT NULL,
			`issued` varchar(12) NOT NULL,
			`brandId` tinyint(1) unsigned NOT NULL,
			`editionId` tinyint(1) unsigned NOT NULL,
			`upgradeKey` tinyint(1) unsigned NOT NULL,
			`upgradeFromKey` varchar(19) NOT NULL,
			`upgradeId` tinyint(2) unsigned NOT NULL,
			`copyId` bigint(10) unsigned NOT NULL,
			`firstUsed` varchar(10) NOT NULL,
			`activationCount` tinyint(3) unsigned NOT NULL,
			`active` tinyint(1) NULL DEFAULT 1,
			PRIMARY KEY (`id`),
			UNIQUE KEY `copyId` (`copyId`),
			KEY `registrationKey` (`registrationKey`)
		) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    	set_transient( $table_name, true, 5 );
    };

		// $charset_collate = $wpdb->get_charset_collate();
		// $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		// 	`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		// 	`name` text NOT NULL,
		// 	`company` text NOT NULL,
		// 	`position` text NOT NULL,
		// 	`email` text NOT NULL,
		// 	`review` mediumtext NOT NULL,
		// 	`score` mediumint(9) NOT NULL,
		// 	`review_date` datetime NOT NULL,
		// 	`active` int(1) NOT NULL DEFAULT '0',
		// UNIQUE (`id`)
		// ) $charset_collate;";
		// require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		// dbDelta($sql);
    //
		// if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
		// 	set_transient( $table_name, true, 5 );
		// };

		// Create trustid_activatipons table if not existing
		// $sql = "CREATE TABLE IF NOT EXISTS`$table_name_activations` (
		// 	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		// 	`registrationId` int(10) unsigned NOT NULL,
		// 	`registrationKeyk` varchar(19) NOT NULL,
		// 	`activated` varchar(10) NOT NULL,
		// 	`email` varchar(255) NOT NULL,
		// 	`ip` varchar(50) NOT NULL,
		// 	`installed` varchar(50) NOT NULL,
		// 	`requestCode` char(22) NOT NULL,
		// 	`responseCode` char(13) NOT NULL,
		// 	`state` tinyint(1) unsigned NOT NULL,
		// 	`country` char(3) NOT NULL,
		// 	`cname` varchar(255) NOT NULL,
		// 	`lname` varchar(255) NOT NULL,
		// 	`fname` varchar(255) NOT NULL,
		// 	`manualActivate` tinyint(1) NULL DEFAULT NULL,
		// 	PRIMARY KEY (`id`),
		// 	KEY `registrationId` (`registrationId`)
		// ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8";
    //
		// require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		// dbDelta($sql);
		// if($wpdb->get_var("SHOW TABLES LIKE '$table_name_activations'") == $table_name_activations) {
		// 	set_transient( $table_name_activations, true, 5 );
		// };
	}

	public static function table_check($table) {
		if(get_transient($table)){
      $html = '';
      $html .= '<div class="updated notice is-dismissible">';
			$html .= "<p>A new table <strong>{$table}</strong> has been created successfully.</p>";
			$html .= '</div>';
			delete_transient($table);
      return $html;
		}
	}



}
