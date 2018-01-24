<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://magicard.com
 * @since      1.0.0
 *
 * @package    Trustid_activation_2018
 * @subpackage Trustid_activation_2018/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Trustid_activation_2018
 * @subpackage Trustid_activation_2018/includes
 * @author     John Fieldsend <john.fieldsend@magicard.com>
 */
class Trustid_activation_2018_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'trustid_activation_2018',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
