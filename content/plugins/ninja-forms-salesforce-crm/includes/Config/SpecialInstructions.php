<?php

if ( !defined( 'ABSPATH' ) )
    exit;

return array(
    array(
        'label' => __( '-None-', 'ninja-forms-salesforce-crm' ),
        'value' => 'none',
    ),
    array(
        'label' => __( 'Check for duplicates in this field', 'ninja-forms-salesforce-crm' ),
        'value' => 'DuplicateCheck'
    ),
    array(
        'label' => __( 'This is a date interval (ex: 2 days)', 'ninja-forms-salesforce-crm' ),
        'value' => 'DateInterval'
    ),
    array(
        'label' => __( 'Format Date for Salesforce', 'ninja-forms-salesforce-crm' ),
        'value' => 'DateFormat'
    ),
    array(
        'label' => __( 'File Upload', 'ninja-forms-salesforce-crm' ),
        'value' => 'FileUpload'
    ), 
    array(
        'label' => __( 'Format for true/false', 'ninja-forms-salesforce-crm' ),
        'value' => 'ForceBoolean'
    ),    
    array(
        'label' => __( 'Keep ampersands and quotes', 'ninja-forms-salesforce-crm' ),
        'value' => 'KeepCharacters'
    ),
);


