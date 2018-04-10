<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'nf_salesforce_plugin_settings', array(

    /*
    |--------------------------------------------------------------------------
    | Client Key and Secret
    |--------------------------------------------------------------------------
    */

    'nfsalesforcecrm_consumer_key' => array(
        'id'    => 'nfsalesforcecrm_consumer_key',
        'type'  => 'textbox',
        'label' => __( 'Consumer Key', 'ninja-forms-salesforce-crm' ),
    ),
    'nfsalesforcecrm_consumer_secret' => array(
        'id'    => 'nfsalesforcecrm_consumer_secret',
        'type'  => 'textbox',
        'label' => __( 'Consumer Secret', 'ninja-forms-salesforce-crm' ),
    ),
    /*
    |--------------------------------------------------------------------------
    | Open Auth - authorization code
    | Retrieved after storing Key and Secret
    |--------------------------------------------------------------------------
    */  
    'nfsalesforcecrm_authorization_code_instructions'=> array(
        'id'    => 'nfsalesforcecrm_authorization_code_instructions',
        'type'  => 'html',
        'label' => __( 'Authorization Code Setup', 'ninja-forms-salesforce-crm' ),
        'html' => '',// created on construction    
    ),
    'nfsalesforcecrm_authorization_code' => array(
        'id'    => 'nfsalesforcecrm_authorization_code',
        'type'  => 'textbox',
        'label' => __( 'Authorization Code', 'ninja-forms-salesforce-crm' ),
    ),   
    'nfsalesforcecrm_refresh_token_instructions'=> array(
        'id'    => 'nfsalesforcecrm_refresh_token_instructions',
        'type'  => 'html',
        'label' => __( 'Generate Refresh Token', 'ninja-forms-salesforce-crm' ),
        'html' => '',// created on construction    
    ),
    /*
     * REFRESH TOKEN, in 3.0 is stored in account data
     * In 2.9, it is stored with settings by using readonly field
     */
    'nfsalesforcecrm_refresh_token' => array(
        'id'    => 'nfsalesforcecrm_refresh_token',
        'type'  => 'html',
        'label' => __( 'Refresh Token', 'ninja-forms-salesforce-crm' ),
        'html' => '',// created on construction
    ),
    /*
    |--------------------------------------------------------------------------
    | List of Objects to refresh
    | 
    |--------------------------------------------------------------------------
    */
   
    'nfsalesforcecrm_refresh_objects_instructions' => array(
        'id'    => 'nfsalesforcecrm_refresh_objects_instructions',
        'type'  => 'html',
        'label' => __( 'Click to refresh Objects', 'ninja-forms-salesforce-crm' ),
        'html' => '',// created on construction
    ),
    
    'nfsalesforcecrm_available_objects' => array(
        'id'    => 'nfsalesforcecrm_available_objects',
        'type'  => 'textbox',
        'label' => __( 'Objects to Retrieve', 'ninja-forms-salesforce-crm' ),
    ), 

     'nfsalesforcecrm_comm_data_status' => array(
        'id'    => 'nfsalesforcecrm_comm_data_status',
        'type'  => 'html',
        'label' => __( 'Status', 'ninja-forms-salesforce-crm' ),
        'html' => '',// created on construction
    ),

    /*
    |--------------------------------------------------------------------------
    | Advanced commands
    |--------------------------------------------------------------------------
    */   
    'nfsalesforcecrm_advanced_codes' => array(
        'id'    => 'nfsalesforcecrm_advanced_codes',
        'type'  => 'textbox',
        'label' =>__( 'Advanced Commands', 'ninja-forms-salesforce-crm' ),
    ),     
    /*
    |--------------------------------------------------------------------------
    | Support Functions
    |--------------------------------------------------------------------------
    */    
    'nfsalesforcecrm_comm_data_debug' => array(
        'id'    => 'nfsalesforcecrm_comm_data_debug',
        'type'  => 'html',
        'label' => __( 'Debug', 'ninja-forms-salesforce-crm' ),
        'html' => '',// created on construction
    ),  
    
    /*
    |--------------------------------------------------------------------------
    | Salesforce Objects and Fields
    |--------------------------------------------------------------------------
    */
    'nfsalesforcecrm_account_data' => array(
        'id'    => 'nfsalesforcecrm_account_data',
        'type'  => 'html',
        'label' => __( 'Availalable Salesforce Fields and Objects', 'ninja-forms-salesforce-crm' ),
        'html' => '',// created on construction 
    ), 
    

    
  
));
