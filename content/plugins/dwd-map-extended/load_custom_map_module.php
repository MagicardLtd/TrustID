<?php
/**
 * @package DWD_Map_Module_Extended
 * @version 1.2.3
 */
/*
Plugin Name: Divi Map Module Extended
Plugin URI: https://diviwebdesign.com/
Description: A Custom Map Module for Divi Builder. Enhance the Google Map and impress your visitors with customized map designs and pin icons!
Author: Divi Web Design
Version: 1.2.3
Author URI: https://diviwebdesign.com/
*/

function dwd_maps_extended(){
	//css
	wp_enqueue_style('maps-extended-css', plugin_dir_url( __FILE__ ) . 'css/dwd-maps-extended.css');
	//js
	wp_register_script('dwd-maps-extended', plugin_dir_url( __FILE__ ) . 'js/maps-extended.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'dwd_maps_extended');

function dwd_maps_extended_admin() {
    wp_enqueue_style('dwd-maps-style', plugin_dir_url( __FILE__ ) . 'css/admin-maps-extended.css');
}
add_action('admin_enqueue_scripts', 'dwd_maps_extended_admin');

function DWD_Custom_map_module_extended(){
	if(class_exists("ET_Builder_Module")){
		include('custom_map_module.php');
	}
}
add_action('et_builder_ready', 'DWD_Custom_map_module_extended');
