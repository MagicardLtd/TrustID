<?php
global $wpdb, $table_name, $table_name_activations, $tidActivationResult;
$table_name = $wpdb->prefix."trustid_keys";
$table_name_activations = $wpdb->prefix."trustid_activations";
$tidActivationResult = array();
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://magicard.com
 * @since             1.0.0
 * @package           Trustid_activation_2018
 *
 * @wordpress-plugin
 * Plugin Name:       Trust ID Activation 2018
 * Plugin URI:        https://github.com/johnboy4809/trustid_activation_2018
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            John Fieldsend
 * Author URI:        https://magicard.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       trustid_activation_2018
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_NAME_VERSION', '1.0.0' );

function activate_trustid_activation_2018() {
	global $wpdb, $table_name, $table_name_activations;;
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-trustid_activation_2018-activator.php';
	Trustid_activation_2018_Activator::activate();
}

function tables_message() {
	global $wpdb,$table_name_activations,$table_name;
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-trustid_activation_2018-activator.php';
	echo Trustid_activation_2018_Activator::table_check($table_name);
	// echo Trustid_activation_2018_Activator::table_check($table_name_activations);
}

function deactivate_trustid_activation_2018() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-trustid_activation_2018-deactivator.php';
	Trustid_activation_2018_Deactivator::deactivate();
}

// add_action( 'init', 'set_plugin_globals' );
register_activation_hook( __FILE__, 'activate_trustid_activation_2018' );
add_action( 'admin_notices', 'tables_message');

register_deactivation_hook( __FILE__, 'deactivate_trustid_activation_2018' );

require plugin_dir_path( __FILE__ ) . 'includes/class-trustid_activation_2018.php';

function run_trustid_activation_2018() {
	$plugin = new Trustid_activation_2018();
	$plugin->run();
}
run_trustid_activation_2018();
