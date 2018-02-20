<?php

if (!defined('ABSPATH') || !class_exists('NF_Abstracts_Action'))
    exit;

/**
 * Class NF_Action_InsightlyCRMExample
 */
final class NF_SalesforceCRM_Actions_AddToSalesforce extends NF_Abstracts_Action {

    /**
     * @var string
     */
    protected $_name = 'addtosalesforce'; // child CRM

    /**
     * @var array
     */
    protected $_tags = array();

    /**
     * @var string
     */
    protected $_timing = 'normal';

    /**
     * @var int
     */
    protected $_priority = '10';

    /**
     * The availalble Salesforce fields for mapping
     * @var array
     */
    protected $field_map_array;

    /**
     * The field data from the form submission needed for building the request
     * @var array
     */
    protected $fields_to_extract;

    /**
     * The lookup array built in shared functions, used for dropdown array
     * @var array 
     */
    protected $field_map_lookup;

    /**
     *
     * @var array Request array used to build the Salesforce Request Object
     */
    protected $request_array;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();

        $this->_nicename = __('Add To Salesforce', 'ninja-forms');

        // build the dropdown array
        $this->field_map_array = nfsalesforcecrm_build_salesforce_field_list();

        add_action('admin_init', array($this, 'init_settings'));
        add_action('ninja_forms_builder_templates', array($this, 'builder_templates'));
    }

    /*
     * PUBLIC METHODS
     */

    public function save($action_settings) {

        $keyed_action_settings = $action_settings['salesforce_field_map'];

        // ensure field map is array and is not empty before modifying
        if (!is_array($keyed_action_settings) || empty($keyed_action_settings)) {

            return $action_settings;
        }

        $scrubbed_field_map = $this->scrub_action_settings($keyed_action_settings);

        $action_settings['salesforce_field_map'] = $scrubbed_field_map;

        return $action_settings;
    }

    public function process($action_settings, $form_id, $data) {

        $api_parameter_array = nfsalesforcecrm_retrieve_api_parameters();

        if (!$api_parameter_array) {
            return false;
        }// unsuccessful getting parameter array

        $this->extract_field_data($action_settings);

        if (class_exists('SalesforcePlusBuildRequest')) {

            $request_object = new SalesforcePlusBuildRequest($this->request_array); // only for NF3
        } else {

            $request_object = new SalesforceBuildRequest($this->request_array, $deprecated = false);
        }

        $object_request_list = $request_object->get_object_request_list();

        if (!$object_request_list) {
            return false;
        }

        nfsalesforcecrm_process_object_list($object_request_list, $request_object, $api_parameter_array);

        return $data;
    }

    public function builder_templates() {
        NF_SalesforceCRM::template('custom-field-map-row.html');
    }

    public function init_settings() {

        $settings = NF_SalesforceCRM::config('ActionFieldMapSettings');

        $this->_settings = array_merge($this->_settings, $settings);

        $field_dropdown = $this->build_field_map_dropdown($this->field_map_array);

        $this->_settings['field_map']['columns']['field_map']['options'] = $field_dropdown;

        $special_instructions = NF_SalesforceCRM::config('SpecialInstructions');
        $this->_settings['field_map']['columns']['special_instructions']['options'] = $special_instructions;

        $this->fields_to_extract = NF_SalesforceCRM::config('FieldsToExtract');
    }

    protected function extract_field_data($action_settings) {

        $this->request_array = array();  // initialize

        $field_map_data = $action_settings['salesforce_field_map']; // matches option repeater 'name'

        if (!is_array($field_map_data)) {
            return; // stop if no array
        }

        $this->build_field_map_lookup();

        foreach ($field_map_data as $field_data) {// cycle through each mapped field
            $map_args = array();

            foreach ($this->fields_to_extract as $field_to_extract) { // cycle through each column in the repeater
                if (isset($field_data[$field_to_extract])) {
                    $value = $field_data[$field_to_extract];

                    // for the field map, replace the human readable version with the coded version
                    if ('field_map' == $field_to_extract) {

                        $value = $this->field_map_lookup[$value];
                    }

                    $map_args[$field_to_extract] = $value;
                }
            }

            $this->request_array[] = $map_args;
        }
    }

    /**
     * Build the array of each field to be sent
     * 
     * Uses the reader-friendly name for both label and value.  Processing
     * can look up the programmatic value for mapping the request
     * @param type $field_map_array
     * @return array
     */
    protected function build_field_map_dropdown($field_map_array) {

        $dropdown_array = array();

        foreach ($field_map_array as $array) {

            $dropdown_array[] = array(
                'label' => $array['name'],
                'value' => $array['name'],
            );
        }

        return $dropdown_array;
    }

    /**
     * Remove unused dropdown options stored in specific action settings key
     * @param type $keyed_action_settings
     */
    protected function scrub_action_settings($keyed_action_settings) {

        foreach ($keyed_action_settings as &$field_map_entry) {

            $field_map_entry['options']['field_map'] = array();
            $field_map_entry['settingModel']['columns']['field_map']['options'] = array();

            $field_map_entry['options']['special_instructions'] = array();
            $field_map_entry['settingModel']['columns']['special_instructions']['options'] = array();
        }

        return $keyed_action_settings;
    }

    /**
     * Builds the lookup array for processing.
     * 
     * The dropdown has both label and value of the reader-friendly version.
     * The lookup is keyed on the reader-friendly to lookup the mapping value
     */
    protected function build_field_map_lookup() {

        $this->field_map_lookup = array(); // initialize

        foreach ($this->field_map_array as $array) {

            $this->field_map_lookup[$array['name']] = $array['value'];
        }
    }

}
