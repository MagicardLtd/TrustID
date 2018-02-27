<?php

namespace TrustID_Activation\Inc\Core;
	/**
	* Fired during plugin activation
	* This class defines all code necessary to run during the plugin's activation.

	* @link       https://trustidsoft.com/
	* @since      2.0.0
	* @author     John Fieldsend
	*/

class Activator {
	/**
	* @since    2.0.0
	*/
	public static function activate() {
		$min_php = '5.6.0';
		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minmum PHP Version of ' . $min_php );
		}
	}

}
