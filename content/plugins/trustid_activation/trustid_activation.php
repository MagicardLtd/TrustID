<?php
	/**
	* @link       			https://trustidsoft.com/
	* @since      			2.0.0
	* @author    		 		John Fieldsend
	* @package          trustid_activation
	*
	* @wordpress-plugin
	* Plugin Name:       TrustID Activation
	* Plugin URI:        https://trustidsoft.com/
	* Description:       New TrustID activation plugin for v3 software
	* Version:           2.0.0
	* Author:            John Fieldsend
	* Author URI:        https://trustidsoft.com/
	* License:           GPL-2.0+
	* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	* Text Domain:       trustidsoft
	* Domain Path:       /languages
	*/

namespace TrustID_Activation;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define Constants
define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );
define( NS . 'PLUGIN_NAME', 'trustid-activation' );
define( NS . 'PLUGIN_VERSION', '2.0.0' );
define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );
define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );
define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( NS . 'PLUGIN_TEXT_DOMAIN', 'trustid-activation' );

// Autoload Classes
require_once( PLUGIN_NAME_DIR . 'inc/libraries/autoloader.php' );

// Register Activation Hooks
register_activation_hook( __FILE__, array( NS . 'Inc\Core\Activator', 'activate' ) );

// Register Deactivation Hooks
register_deactivation_hook( __FILE__, array( NS . 'Inc\Core\Deactivator', 'deactivate' ) );


/**
 * Plugin Singleton Container
 * Maintains a single copy of the plugin app object
 * @since    1.0.0
 */
class TrustID_Activation {

	static $init;
	/**
	 * Loads the plugin
	 * @access    public
	 */
	public static function init() {
		if ( null == self::$init ) {
			self::$init = new Inc\Core\Init();
			self::$init->run();
		}
		return self::$init;
	}
}

 // Begins execution of the plugin
function TrustID_Activation_init() {
	return TrustID_Activation::init();
}

$min_php = '5.6.0';
// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
	TrustID_Activation_init();
}
