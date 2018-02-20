<?php

/*
  Name: Ninja Forms - SalesForce CRM
  URI: http://lb3computingsolutions.com
  Desc: Use Ninja Forms to add entries to SalesForce
  Text Domain: ninja-forms-salesforce-crm
 */
/** @var string Current deprecated Salesforce version that is running */
define( 'NF2SALESFORCECRM_VERSION', '1.2.2' );



/* ----------
  GLOBALS
  ----- */
nfsalesforce_load_globals();


/* ----------
  INCLUDES
  ---------------------------------------------------------------------------------------------------------- */

include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/deprecated/includes/admin/settings-deprecated.php');
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/deprecated/includes/admin/settings-field-output-deprecated.php');
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/deprecated/includes/admin/settings-lib-deprecated.php');

include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/deprecated/includes/form-processing-deprecated.php');

include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/deprecated/includes/field/field-registration-deprecated.php');
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/deprecated/includes/field/mappable-field-extensions-deprecated.php');


include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Admin/salesforce-object-refresh.php'); 
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Admin/salesforce-api-parameters.php'); 
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Admin/build-salesforce-field-list.php'); 

include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Comm/class-salesforce-build-request.php');
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Comm/class-salesforce-communication.php');

include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Comm/authentication/class-salesforce-security-credentials.php');
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Comm/authentication/class-salesforce-get-refresh-token.php');
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Comm/authentication/class-salesforce-access-token.php');
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Comm/authentication/class-salesforce-version.php');


include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Comm/request/class-salesforce-describe-object.php');
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Comm/request/class-salesforce-list-of-objects.php');
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Comm/request/class-salesforce-post-new-record.php');
include_once( NF2SALESFORCECRM_PLUGIN_DIR . '/includes/Comm/request/class-salesforce-check-for-duplicate.php');



/* ----------
  LICENSING
  -------------------------------------------------------------------------------------------------------------- */

add_action( 'admin_init', 'nfsalesforcecrm_extension_setup_license' );

function nfsalesforcecrm_extension_setup_license() {
    if ( class_exists( 'NF_Extension_Updater' ) ) {
        $NF_Extension_Updater = new NF_Extension_Updater( 'SalesForce CRM', NF2SALESFORCECRM_VERSION, 'Stuart Sequeira', __FILE__ );
    }
}

/* ----------
  LANGUAGE
  -------------------------------------------------------------------------------------------------------------- */

add_action( 'plugins_loaded', 'nfsalesforcecrm_extension_load_lang' );

function nfsalesforcecrm_extension_load_lang() {

    /** Set our unique textdomain string */
    $textdomain = 'ninja-forms-salesforce-crm';

    /** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
    $locale = apply_filters( 'plugin_locale', get_locale(), $textdomain );

    /** Set filter for WordPress languages directory */
    $wp_lang_dir = apply_filters(
            'ninja_forms_wp_lang_dir', WP_LANG_DIR . '/' . basename( dirname( __FILE__ ) ) . '/' . $textdomain . '-' . $locale . '.mo'
    );

    /** Translations: First, look in WordPress' "languages" folder = custom & update-secure! */
    load_textdomain( $textdomain, $wp_lang_dir );

    /** Translations: Secondly, look in plugin's "lang" folder = default */
    $plugin_dir = basename( dirname( __FILE__ ) );
    $lang_dir = apply_filters( 'nfsalesforcecrm_extension_lang_dir', $plugin_dir . '/lang/' );
    load_plugin_textdomain( $textdomain, FALSE, $lang_dir );
}

/* ----------
  HOOK INTO THE FLOW
  -------------------------------------------------------------------------------------------------- */

add_action( 'init', 'nfsalesforcecrm_frontend_hook' );

function nfsalesforcecrm_frontend_hook() {

    add_action( 'ninja_forms_post_process', 'nfsalesforcecrm_process_form_to_insert_form_data' );
}

/**
 * When the Salesforce settings page is loaded, check if desired to update
 * account objects;  If yes, then refresh object and fields for specified objects
 * @global array $nfsalesforcecrm_settings
 * 
 */
function nfsalesforcecrm_settings_page_hook() {

    global $nfsalesforcecrm_settings;

    if ( isset( $nfsalesforcecrm_settings[ 'nfsalesforcecrm_refresh_salesforce_objects' ] ) && 'TRUE' == $nfsalesforcecrm_settings[ 'nfsalesforcecrm_refresh_salesforce_objects' ] ) {

        nfsalesforcecrm_refresh_salesforce_objects();
    }
}

add_action( 'load-forms_page_nfsalesforcecrm-site-options', 'nfsalesforcecrm_settings_page_hook' );
