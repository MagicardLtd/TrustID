<?php
class ET_Builder_Module_Map_Extended extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Map Extended', 'et_builder' );
		$this->slug            = 'et_pb_map_extended';
		//$this->fb_support      = true;
		$this->child_slug      = 'et_pb_map_pin_extended';
		$this->child_item_text = esc_html__( 'Pin', 'et_builder' );

		$this->whitelisted_fields = array(
			'address',
			'zoom_level',
			'address_lat',
			'address_lng',
			'map_center_map',
			'mouse_wheel',
			'mobile_dragging',
			'admin_label',
			'module_id',
			'module_class',
			//'use_grayscale_filter',
			//'grayscale_filter_amount',
			//added
			'controls_ui',
			'map_options',
			'map_type',
			'info_window',
			'marker_animation',
			'infowindow_style_title',
			'infowindow_style_content',
		);

		$this->fields_defaults = array(
			'zoom_level'           => array( '18', 'only_default_setting' ),
			'mouse_wheel'          => array( 'on' ),
			'mobile_dragging'      => array( 'on' ),
			//'use_grayscale_filter' => array( 'off' ),
			//added
			'controls_ui'          => array( 'on' ),
			'map_options'		   => array( '1' ),
			'map_type'		   => array( '1' ),
			'info_window'		   => array( 'off' ),
			'marker_animation'		   => array( 'off' ),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'infowindow_style_title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "%%order_class%% .gm-style .gm-style-iw h3",
					),
					'font_size' => array(
						'toggle_slug'  => 'title_styles',
						'default'      => '22px',
						'color' => '#333',
					),
					'font' => array(
						'toggle_slug'  => 'title_styles',
						'color' => '#333',
					),
					'line_height'    => array(
						'toggle_slug'  => 'content_styles',
						'default'      => '1.3em',
					),
					'hide_line_height'    => false,
					'hide_text_color'     => false,
					'hide_letter_spacing' => false,
				),
				'infowindow_style_content' => array(
					'label'    => esc_html__( 'Content', 'et_builder' ),
					'css'      => array(
						'main' => "%%order_class%% .gm-style .gm-style-iw p",
					),
					'font_size' => array(
						'toggle_slug'  => 'content_styles',
						'default'      => '14px',
						'color' => '#333',
					),
					'font' => array(
						'toggle_slug'  => 'content_styles',
						'color' => '#333',
					),
					'line_height'    => array(
						'toggle_slug'  => 'content_styles',
						'default'      => '1.7em',
					),
					'hide_line_height'    => false,
					'hide_text_color'     => false,
					'hide_letter_spacing' => false,
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			//added
			'map_options' => array(
	        'label'           => esc_html__( 'Map Options', 'et_builder' ),
	        'type'            => 'select',
	        'option_category' => 'layout',
	        'options'         => array(
		        '1' 			  => esc_html__( 'Google Default', 'et_builder' ),
		        '2' 			  => esc_html__( 'Greyscale', 'et_builder' ),
		        '3' 			  => esc_html__( 'Shades of Grey', 'et_builder' ),
		        '4'               => esc_html__( 'Blue Water', 'et_builder' ),
		        '5'               => esc_html__( 'MarcusWithman-Map', 'et_builder' ),
		        '6'               => esc_html__( 'Table de Bellefois', 'et_builder' ),
		        '7'               => esc_html__( 'Style 04', 'et_builder' ),
		        '8'               => esc_html__( 'MapaBlanco', 'et_builder' ),
		        '9'               => esc_html__( 'decola', 'et_builder' ),
		        '10'              => esc_html__( 'Flex', 'et_builder' ),
		        '11'              => esc_html__( 'Kent Outdoors', 'et_builder' ),
		        '12'              => esc_html__( 'Transport for London', 'et_builder' ),
		        '13'			  => esc_html__( 'Paper', 'et_builder' ),
		        '14'			  => esc_html__( 'Light Monochrome', 'et_builder' ),
		        '15' 			  => esc_html__( 'Midnight Commander', 'et_builder' ),
		        '16' 			  => esc_html__( 'Avocado World', 'et_builder' ),
		        '17'			  => esc_html__( 'Glasgow MegaSnake', 'et_builder' ),
		        '18'			  => esc_html__( 'Chundo Style', 'et_builder' ),
		        '19'			  => esc_html__( 'Bates Green', 'et_builder' ),
		        '20'			  => esc_html__( 'mikiwat', 'et_builder' ),
		        '21'			  => esc_html__( 'Bright Dessert', 'et_builder' ),
		        '22'			  => esc_html__( 'coy beauty', 'et_builder' ),
		        '23'			  => esc_html__( 'shades of conservation', 'et_builder' ),
		        '24'			  => esc_html__( 'pixmix', 'et_builder' ),
		        '25'			  => esc_html__( 'Icy Blue', 'et_builder' ),
		        '26' 			  => esc_html__( 'even lighter', 'et_builder' ),
		        '27' 			  => esc_html__( 'Bold Black & White', 'et_builder' ),
		        '28'			  => esc_html__( 'Dropoff 3', 'et_builder' ),
		        '29'			  => esc_html__( 'Simply Golden', 'et_builder' ),
		        '30'			  => esc_html__( 'Pirate Map', 'et_builder' ),
		        '31'			  => esc_html__( 'Unsaturated Browns', 'et_builder' ),
		        '32'			  => esc_html__( 'Orange', 'et_builder' ),
		        '33'			  => esc_html__( 'OC', 'et_builder' ),
		        '34'			  => esc_html__( 'Vintage', 'et_builder' ),
		        '35'			  => esc_html__( 'Calver', 'et_builder' ),
		        '36'			  => esc_html__( 'Bright & Bubbly', 'et_builder' ),
		        '37'			  => esc_html__( 'Red & Blue', 'et_builder' ),
		        '38'		      => esc_html__( 'Argo', 'et_builder' ),
		        '39'			  => esc_html__( 'Hopper', 'et_builder' ),
		        '40'			  => esc_html__( 'The Propia Effect', 'et_builder' ),
		        '41'			  => esc_html__( 'Cladme', 'et_builder' ),
		        '42'			  => esc_html__( 'darkdetail', 'et_builder' ),
		        '43'			  => esc_html__( 'Pale Dawn', 'et_builder' ),
		        '44'			  => esc_html__( 'Light Green', 'et_builder' ),
		        '45'			  => esc_html__( 'iovation Map', 'et_builder' ),
		        '46'			  => esc_html__( 'papuportal', 'et_builder' ),
		        '47'			  => esc_html__( 'Savagio Yellow', 'et_builder' ),
		        '48'			  => esc_html__( 'Light Rust', 'et_builder' ),
		        '49' 			  => esc_html__( 'inturlam Style', 'et_builder' ),
		        '50'			  => esc_html__( 'Alibra', 'et_builder' ),
		        '51'			  => esc_html__( 'Dharani', 'et_builder' ),
		        '52'			  => esc_html__( 'Primo', 'et_builder' ),
		        '53'			  => esc_html__( 'Cotton Candy', 'et_builder' ),
		        '54'			  => esc_html__( '035print', 'et_builder' ),
		        '55'			  => esc_html__( 'Retro', 'et_builder' ),
		        '56'			  => esc_html__( 'Avocado World', 'et_builder' ),
		        '57'			  => esc_html__( 'Gowalla', 'et_builder' ),
		        '58'			  => esc_html__( 'Old Timey', 'et_builder' ),
		        '59'			  => esc_html__( 'The Propia Effect', 'et_builder' ),
		        '60'			  => esc_html__( 'Mondrian', 'et_builder' ),
		        '61'			  => esc_html__( 'Neon World', 'et_builder' ),
		        '62'			  => esc_html__( 'Old Map', 'et_builder' ),
		        '63'			  => esc_html__( 'Flat Pale', 'et_builder' ),
		        '64'			  => esc_html__( 'Candy Colours', 'et_builder' ),
		        '65'			  => esc_html__( 'Old-School maps posters', 'et_builder' ),
		        '66'			  => esc_html__( 'Camilo florez estilo de mapa modificado', 'et_builder' ),
		        '67'			  => esc_html__( 'Lemon Tree', 'et_builder' ),
		        '68'			  => esc_html__( 'Grayscale Yellow', 'et_builder' ),
		        '69'			  => esc_html__( 'Presto Map', 'et_builder' ),
		        '70'			  => esc_html__( 'Best Ski Pros', 'et_builder' ),
	       	),        
	        'description'       => esc_html__( 'Choose a map design. This module uses Snazzy Maps data. Snazzy Maps is a repository of different styles for Google Maps aimed towards web designers and developers.', 'et_builder' ),
	        	'tab_slug' => 'advanced',
	     	),
			'map_type' => array(
		        'label'           => esc_html__( 'Map Type', 'et_builder' ),
		        'type'            => 'select',
		        'option_category' => 'layout',
		        'options'         => array(
			        '1' 			  => esc_html__( 'Roadmap (Default)', 'et_builder' ),
			        '2' 			  => esc_html__( 'Satellite', 'et_builder' ),
			        '3' 			  => esc_html__( 'Hybrid', 'et_builder' ),
			        '4'               => esc_html__( 'Terrain', 'et_builder' ),
			    ),
			'description'		  => esc_html__( 'Google Maps provides four types of maps. RoadMap, Satellite, Hybird and Terrian. The default Map type is ROADMAP', 'et_builder' ),
			    'tab_slug' => 'advanced',
	       	),
			'info_window' => array(
				'label'           => esc_html__( 'Show InfoWindow on load', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug' => 'advanced',
			),
			'marker_animation' => array(
				'label'           => esc_html__( 'Bounce Animation on Marker Pin', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug' => 'advanced',
			),
			//ended
			'google_api_key' => array(
				'label'             => esc_html__( 'Google API Key', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'attributes'        => 'readonly',
				'additional_button' => sprintf(
					' <a href="%2$s" target="_blank" class="et_pb_update_google_key button" data-empty_text="%3$s">%1$s</a>',
					esc_html__( 'Change API Key', 'et_builder' ),
					esc_url( et_pb_get_options_page_link() ),
					esc_attr__( 'Add Your API Key', 'et_builder' )
				),
				'additional_button_type' => 'change_google_api_key',
				'class' => array( 'et_pb_google_api_key', 'et-pb-helper-field' ),
				'description'       => et_get_safe_localization( sprintf( __( 'The Maps module uses the Google Maps API and requires a valid Google API Key to function. Before using the map module, please make sure you have added your API key inside the Divi Theme Options panel. Learn more about how to create your Google API Key <a href="%1$s" target="_blank">here</a>.', 'et_builder' ), esc_url( 'http://www.elegantthemes.com/gallery/divi/documentation/map/#gmaps-api-key' ) ) ),
			),
			'address' => array(
				'label'             => esc_html__( 'Map Center Address', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'additional_button' => sprintf(
					' <a href="#" class="et_pb_find_address button">%1$s</a>',
					esc_html__( 'Find', 'et_builder' )
				),
				'class' => array( 'et_pb_address' ),
				'description'       => esc_html__( 'Enter an address for the map center point, and the address will be geocoded and displayed on the map below.', 'et_builder' ),
			),
			'zoom_level' => array(
				'type'    => 'hidden',
				'class'   => array( 'et_pb_zoom_level' ),
			),
			'address_lat' => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_address_lat' ),
			),
			'address_lng' => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_address_lng' ),
			),
			'map_center_map' => array(
				'renderer'              => 'et_builder_generate_center_map_setting',
				'use_container_wrapper' => false,
				'option_category'       => 'basic_option',
			),
			'mouse_wheel' => array(
				'label'           => esc_html__( 'Mouse Wheel Zoom', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'description' => esc_html__( 'Here you can choose whether the zoom level will be controlled by mouse wheel or not.', 'et_builder' ),
			),
			'mobile_dragging' => array(
				'label'           => esc_html__( 'Draggable on Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'description' => esc_html__( 'Here you can choose whether or not the map will be draggable on mobile devices.', 'et_builder' ),
			),
			//added
			'controls_ui' => array(
				'label'           => esc_html__( 'Show Controls', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'description' => esc_html__( 'Here you can choose whether to show controls UI or not.', 'et_builder' ),
			),
			//ended
			// 'use_grayscale_filter' => array(
			// 	'label'           => esc_html__( 'Use Grayscale Filter', 'et_builder' ),
			// 	'type'            => 'yes_no_button',
			// 	'option_category' => 'configuration',
			// 	'options'         => array(
			// 		'off' => esc_html__( 'No', 'et_builder' ),
			// 		'on'  => esc_html__( 'Yes', 'et_builder' ),
			// 	),
			// 	'affects'     => array(
			// 		'#et_pb_grayscale_filter_amount',
			// 	),
			// 	'tab_slug' => 'advanced',
			// ),
			// 'grayscale_filter_amount' => array(
			// 	'label'           => esc_html__( 'Grayscale Filter Amount (%)', 'et_builder' ),
			// 	'type'            => 'range',
			// 	'option_category' => 'configuration',
			// 	'tab_slug'        => 'advanced',
			// ),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$address_lat             = $this->shortcode_atts['address_lat'];
		$address_lng             = $this->shortcode_atts['address_lng'];
		$zoom_level              = $this->shortcode_atts['zoom_level'];
		$mouse_wheel             = $this->shortcode_atts['mouse_wheel'];
		$mobile_dragging         = $this->shortcode_atts['mobile_dragging'];
		//$use_grayscale_filter    = $this->shortcode_atts['use_grayscale_filter'];
		//$grayscale_filter_amount = $this->shortcode_atts['grayscale_filter_amount'];
		//added
		$controls_ui             = $this->shortcode_atts['controls_ui'];
		$map_options			 = $this->shortcode_atts['map_options'];
		$map_type			 = $this->shortcode_atts['map_type'];
		$info_window			 = $this->shortcode_atts['info_window'];
		$marker_animation		 = $this->shortcode_atts['marker_animation'];
		$infowindow_style_title  = $this->shortcode_atts['infowindow_style_title'];

		wp_enqueue_script( 'google-maps-api' );

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$all_pins_content = $this->shortcode_content;

		// $grayscale_filter_data = '';
		// if ( 'on' === $use_grayscale_filter && '' !== $grayscale_filter_amount ) {
		// 	$grayscale_filter_data = sprintf( ' data-grayscale="%1$s"', esc_attr( $grayscale_filter_amount ) );
		// }
		
		$output = sprintf(
			'<div%5$s class="et_pb_module et_pb_map_container_extended%6$s%11$s">
				<div class="et_pb_map" data-center-lat="%1$s" data-center-lng="%2$s" data-zoom="%3$s" data-mouse-wheel="%7$s" data-mobile-dragging="%8$s" data-controls-ui="%12$s"%9$s%10$s%13$s></div>
				%4$s
			</div>',
			esc_attr( $address_lat ),
			esc_attr( $address_lng ),
			esc_attr( $zoom_level ),
			$all_pins_content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			esc_attr( $mouse_wheel ),
			//remove $grayscale_filter_data,
			esc_attr( $mobile_dragging ),
			//added 9&
			( '' !== $map_options ? esc_attr(" data-map-style={$map_options}") : '' ),
			( '' !== $info_window ? esc_attr(" data-info-toggle={$info_window}") : '' ),
			( 'off' !== $marker_animation ? ' marker-animation' : '' ),
			esc_attr( $controls_ui ),
			( '' !== $map_type ? esc_attr(" data-map-type={$map_type}") : '' )
		);
		wp_enqueue_script( 'dwd-maps-extended' );
		//wp_localize_script( 'dwd-maps-extended', 'dwd_map', $mapData );
		return $output;
	}
}
$et_builder_map_extended = new ET_Builder_Module_Map_Extended();
add_shortcode( 'et_pb_map_extended', array($et_builder_map_extended, '_shortcode_callback') );

class ET_Builder_Module_Map_Extended_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Pin', 'et_builder' );
		$this->slug                        = 'et_pb_map_pin_extended';
		//$this->fb_support                  = true;
		$this->type                        = 'child';
		$this->child_title_var             = 'title';
		$this->custom_css_tab              = false;

		$this->whitelisted_fields = array(
			'title',
			'pin_address',
			'zoom_level',
			'pin_address_lat',
			'pin_address_lng',
			'map_center_map',
			'content_new',
			//added
			'pin_on_off',
			'pin_src',
			'pin_widthsize',
			'pin_heightsize',
		);

		$this->fields_defaults = array(
			'pin_on_off'		=> array( 'off' ),
			'pin_widthsize'		=> array( '22' ),
			'pin_heightsize'	=> array( '36' ),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Pin', 'et_builder' );
		$this->settings_text               = esc_html__( 'Pin Settings', 'et_builder' );
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The title will be used within the tab button for this tab.', 'et_builder' ),
			),
			'pin_address' => array(
				'label'             => esc_html__( 'Map Pin Address', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'class'             => array( 'et_pb_pin_address' ),
				'description'       => esc_html__( 'Enter an address for this map pin, and the address will be geocoded and displayed on the map below.', 'et_builder' ),
				'additional_button' => sprintf(
					'<a href="#" class="et_pb_find_address button">%1$s</a>',
					esc_html__( 'Find', 'et_builder' )
				),
			),
			'pin_on_off' => array(
				'label'           => esc_html__( 'Use Custom Icon', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'     => array(
					'#et_pb_pin_src, #et_pb_pin_widthsize, #et_pb_pin_heightsize',
				),
				'tab_slug' => 'advanced',
			),
			'pin_src' => array(
				'label'              => esc_html__( 'Pin Icon URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an Icon', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Icon', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Icon', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired Pin Icon, or type in the URL to the Pin Icon you would like to display.', 'et_builder' ),
				'tab_slug' => 'advanced',
			),
			'pin_widthsize' => array(
				'label'           => esc_html__( 'Pin Icon Width (in PX)', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
			),
			'pin_heightsize' => array(
				'label'           => esc_html__( 'Pin Icon Height (in PX)', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
			),
			'zoom_level' => array(
				'renderer'        => 'et_builder_generate_pin_zoom_level_input',
				'option_category' => 'basic_option',
				'class'           => array( 'et_pb_zoom_level' ),
			),
			'pin_address_lat' => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_pin_address_lat' ),
			),
			'pin_address_lng' => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_pin_address_lng' ),
			),
			'map_center_map' => array(
				'renderer'              => 'et_builder_generate_center_map_setting',
				'option_category'       => 'basic_option',
				'use_container_wrapper' => false,
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can define the content that will be placed within the infobox for the pin.', 'et_builder' ),
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $et_pb_tab_titles;

		$title = $this->shortcode_atts['title'];
		$pin_address_lat = $this->shortcode_atts['pin_address_lat'];
		$pin_address_lng = $this->shortcode_atts['pin_address_lng'];
		//added
		$pin_on_off    			 = $this->shortcode_atts['pin_on_off'];
		$pin_src         = $this->shortcode_atts['pin_src'];
		$pin_widthsize   = $this->shortcode_atts['pin_widthsize'];
		$pin_heightsize  = $this->shortcode_atts['pin_heightsize'];

		$replace_htmlentities = array( '&#8221;' => '', '&#8243;' => '' );

		if ( ! empty( $pin_address_lat ) ) {
			$pin_address_lat = strtr( $pin_address_lat, $replace_htmlentities );
		}
		if ( ! empty( $pin_address_lng ) ) {
			$pin_address_lng = strtr( $pin_address_lng, $replace_htmlentities );
		}

		$src_pin = '';
		if ( $pin_on_off == 'on' && $pin_src <> '' ) {
			$src_pin = $pin_src;
		}
		if ($pin_on_off == 'off' ){
			$src_pin = 'http://www.google.com/mapfiles/marker.png';
		}

		$content = $this->shortcode_content;

		$output = sprintf(
			'<div class="et_pb_map_pin_extended" data-lat="%1$s" data-lng="%2$s" data-title="%5$s" data-pin-src="%6$s" %7$s %8$s>
				<h3 style="margin-top: 10px;">%3$s</h3>
				%4$s
			</div>',
			esc_attr( $pin_address_lat ),
			esc_attr( $pin_address_lng ),
			esc_html( $title ),
			( '' != $content ? sprintf( '<div class="infowindow">%1$s</div>', $content ) : '' ),
			esc_attr( $title ),
			esc_attr( $src_pin ),
			( '' !== $pin_widthsize ? esc_attr(" data-pin-width={$pin_widthsize}") : '' ),
			( '' !== $pin_heightsize ? esc_attr(" data-pin-height={$pin_heightsize}") : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Map_Extended_Item;
$et_builder_map_extended_item = new ET_Builder_Module_Map_Extended_Item();
add_shortcode( 'et_pb_map_pin_extended', array($et_builder_map_extended_item, '_shortcode_callback') );