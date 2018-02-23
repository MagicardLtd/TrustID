<?php

global $source, $template_directory_uri, $template_child_directory_uri;
$template_directory_uri 		    = get_template_directory_uri();
$template_child_directory_uri 	= get_template_directory_uri().'-child';
require_once( 'assets/includes/custom_post_types.php' );

// Theme Setup
function theme_scripts() {
	global $wp_styles, $wp_query;
	if (!is_admin()) {
		wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'trustid-icons', 'https://file.myfontastic.com/LywTUUT52vYm4t8pB54yQH/icons.js', array( 'jquery' ), '', true );
		wp_enqueue_style( 'icon-font', 'https://file.myfontastic.com/LywTUUT52vYm4t8pB54yQH/icons.css' );
		wp_enqueue_style( 'custom-css', get_stylesheet_directory_uri().'/assets/css/custom.min.css' );
	}
	if ( is_page() ) {

	}
}
add_action( 'wp_enqueue_scripts', 'theme_scripts', 999 );

register_nav_menus(
	array(
		'main-nav' => __( 'The Main Menu' ),   // main nav in header
	)
);

// Theme Transients
function banner_data($id) {
	$tansient_name = 'banner_data_'.$id;
	// if ( false === ( $bannerData = get_transient( $tansient_name ) ) ) {
		$bannerData = array(
			'type' 		=> get_field('banner_type', $id),
			'title' 	=> get_field('banner_title', $id),
			'text'		=> get_field('banner_text', $id),
			'button'	=> get_field('button_text', $id),
			'heroImg'	=> get_field('hero_image', $id)
		);
		if (get_field('banner_type', $id) == 'video') {
			$videos = get_field('banner_videos', $id);
			if ($videos) {
				foreach ($videos as $video) {
					$path_parts = pathinfo($video['banner_video']);
					$bannerData['videos'][] = $path_parts['dirname'].'/'.$path_parts['filename'];
				}
 			}
		} else {
			$images = get_field('banner_images', $id);
			if ($images) {
				foreach ($images as $image) {
					$bannerData['images'][] = $image['image'];
				}
			}
		}
		// set_transient( $tansient_name, $bannerData, WEEK_IN_SECONDS );
	// }
	return $bannerData;
}

// Theme functions
function my_et_builder_post_types( $post_types ) {
    $post_types[] = 'editions';
    return $post_types;
}
add_filter( 'et_builder_post_types', 'my_et_builder_post_types' );

add_filter( 'et_pb_show_all_layouts_built_for_post_type', 'sb_et_pb_show_all_layouts_built_for_post_type' );

function sb_et_pb_show_all_layouts_built_for_post_type() {
 return 'page';
}


function getrandomelement($array) {
  $pos=rand(0,sizeof($array)-1);
  $res=$array[$pos];
  if (is_array($res)) return getrandomelement($res);
  else return $res;
}

function home_page_banner($id) {
	$data = banner_data($id);
	$img_src = wp_get_attachment_image_url( $data['heroImg'], 'full' );
	$img_srcset = wp_get_attachment_image_srcset( $data['heroImg'], 'full' );
	$img_size = wp_get_attachment_image_sizes( $data['heroImg'], 'full' );
	$img_alt = get_post_meta( $id, '_wp_attachment_image_alt', true);
	// Video banner
	if ($data['type'] == 'video') {
		wp_enqueue_script( 'vide', 'https://cdnjs.cloudflare.com/ajax/libs/vide/0.5.1/jquery.vide.min.js', array( 'jquery' ), '', true );
		$style = '<div id="banner_'.$data['type'].'" data-vide-bg="'.$data['videos'][0].'">'."\n";
	// Image Banner
	} else {
		$image = getrandomelement($data['images']);
		$style = '<div id="banner_'.$data['type'].'" data-image-src="'.$image.'">'."\n";
	}
	$html = '<div id="banner_container">';
	$html .= $style;
	$html .= '<div id="banner_curve">';
	$html .= '<section id="banner_content">'."\n";
	$html .= '<h1 id="banner_title">'.$data['title'].'</h1>'."\n";
	$html .= $data['text']."\n";
	$html .= '<a id="banner_btn" href="#editions" class="whiteBtn scrollTo">'.$data['button'].'</a>';
	$html .= "</section>\n";
	$html .= '<div class="image_container">'."\n";
	$html .= '<img id="heroImg" src="'.esc_url( $img_src ).'" srcset="'.esc_attr( $img_srcset ).'"'."\n";
	$html .= 'sizes="'.esc_attr( $img_size ).'" alt="'.esc_attr( $img_alt ).'">'."\n";
	$html .= "</div>\n</div>\n</div>\n</div>\n";
	return $html;
}

function page_banner($id) {
	$data = banner_data($id);
	$img_src = wp_get_attachment_image_url( $data['heroImg'], 'full' );
	$img_srcset = wp_get_attachment_image_srcset( $data['heroImg'], 'full' );
	$img_size = wp_get_attachment_image_sizes( $data['heroImg'], 'full' );
	$img_alt = get_post_meta( $id, '_wp_attachment_image_alt', true);
	// Video banner
	if ($data['type'] == 'video') {
		wp_enqueue_script( 'vide', 'https://cdnjs.cloudflare.com/ajax/libs/vide/0.5.1/jquery.vide.min.js', array( 'jquery' ), '', true );
		$style = '<div id="banner_'.$data['type'].'" data-vide-bg="'.$data['videos'][0].'">'."\n";
	// Image Banner
	} else {
		$image = getrandomelement($data['images']);
		$style = '<div id="banner_'.$data['type'].'" data-image-src="'.$image.'">'."\n";
	}
	$html = '<div id="banner_container" class="page_banner">';
	$html .= $style;
	$html .= '<div id="banner_curve">';
	$html .= '<section id="banner_page_content" class="et_pb_row et_pb_row_0">'."\n";
	$html .= '<h1 id="banner_title">'.$data['title'].'</h1>'."\n";
	$html .= '<div class="test_box et_pb_column et_pb_column_1_2">'."\n";
	$html .= '<p id="banner_text">'.$data['text'].'</p>'."\n";
	if ($data['button']) {
		$html .= '<a id="banner_btn" href="#editions" class="whiteBtn scrollTo">'.$data['button'].'</a>';
	}
	$html .= "</div>\n";
	$html .= '<div id="image_container" class="test_box et_pb_column et_pb_column_1_2">'."\n";
	$html .= '<img id="heroImg" src="'.esc_url( $img_src ).'" srcset="'.esc_attr( $img_srcset ).'"'."\n";
	$html .= 'sizes="'.esc_attr( $img_size ).'" alt="'.esc_attr( $img_alt ).'">'."\n";
	$html .= '</div>'."\n";
	$html .= "</section>\n";
	$html .= "</div>\n</div>\n</div>\n";
	return $html;
}

function screen_carasel() {
	global $post;
	$id = get_the_ID();
	$screens = get_field('screens', $id);
	if ($screens) {
		$html = '<div class="screenshots-slider popup-gallery">'."\n";
		$html .= '<div class="slider-container">'."\n";
		$html .= '<div class="slider-content">'."\n";
		foreach ($screens as $screen ) {
			$img_src = wp_get_attachment_image_url( $screen['screen_shot'], 'full' );
			$img_srcset = wp_get_attachment_image_srcset( $screen['screen_shot'], 'full' );
			$img_size = wp_get_attachment_image_sizes( $screen['screen_shot'], 'full' );
			// $img_alt = get_post_meta( $id, '_wp_attachment_image_alt', true);
			$html .= '<div class="slider-single">'."\n";
			$html .= '<div class="slider-single-slide">'."\n";
			$html .= '<h3>'.$screen['screen_title'].'</h3>'."\n";
			$html .= '<p>'.$screen['screen_description'].'</p>'."\n";
			$html .= '<a href="'.esc_url( $img_src ).'" class="popup-img">';
			$html .= '<img src="'.esc_url( $img_src ).'" srcset="'.esc_attr( $img_srcset ).'"'."\n";
			$html .= 'sizes="'.esc_attr( $img_size ).'" alt="">'."\n";
			$html .= '</div>'."\n";
			$html .= '</a>'."\n";
			$html .= "</div>\n";
		}
		$html .= "</div>\n"; // Close slider-content
		$html .= '<a class="slider-left" href="javascript:void(0);"><svg class="icon-left-open-big"><use xlink:href="#icon-left-open-big"></use></svg></a>';
		$html .= '<a class="slider-right" href="javascript:void(0);"><svg class="icon-right-open-big"><use xlink:href="#icon-right-open-big"></use></svg></a>';
		// <i class="fa fa-angle-right"></i>
		$html .= "</div>\n"; // Close slider-container
		$html .= "</div>\n"; // Close slider-container
	}
	return $html;
}

function page_video() {
	global $post;
	$id = get_the_ID();
	$img_src = wp_get_attachment_image_url( get_field('laptop_image', $id), 'full' );
	$img_srcset = wp_get_attachment_image_srcset( get_field('laptop_image', $id), 'full' );
	$img_size = wp_get_attachment_image_sizes( get_field('laptop_image', $id), 'full' );
	$video = get_field( 'youtube', $id );
	$html = '<div class="video_container">';
	$html .= '<img class="center_block" src="'.esc_url( $img_src ).'" srcset="'.esc_attr( $img_srcset ).'"'."\n";
	$html .= 'sizes="'.esc_attr( $img_size ).'" alt="">'."\n";
	$html .= '<a href="'.$video.'" class="videoBtn popup-youtube">v</a>';
	$html .= '<div class="waves">'."\n";
	$html .= '<div class="wave wave-1"></div>'."\n";
	$html .= '<div class="wave wave-2"></div>'."\n";
	$html .= '<div class="wave wave-3"></div>'."\n";
	$html .= '</div>'."\n".'</div>'."\n";
	return $html;
}

function feature_table_data($feature) {
	global $wpdb;
	$data = array();
	$i = 0;
	$args = array(
		'post_type'   => 'editions',
		'post_status' => 'publish',
		'order' 			=> 'ASC',
	);
	$editions = new WP_Query( $args );
	if ( $editions->have_posts() ) :
		while ( $editions->have_posts() ) : $editions->the_post();
			$rows = get_field_object($feature, $editions->ID);
			$data['title'] = $rows['label'];
			$data['edition'][] = get_the_title();
			$data['labels'] = $rows['choices'];
			foreach ($rows['choices'] as $choice_value => $choice_label) {
				if (in_array($choice_value, $rows['value'])) {
					$data['choice'][$i][] = '<svg class="icon-primitive-dot"><use xlink:href="#icon-primitive-dot"></use></svg>';
				} else {
					$data['choice'][$i][] = '';
				}
			}
			$i++;
		endwhile;
		wp_reset_postdata();
	endif;
	return $data;
}

function editions_table($features) {
	extract(shortcode_atts(array('features' =>'*'),$features));
	$data = feature_table_data($features);
	$html = '<table class="">';
	$html .= '<thead>'."\n".'<tr>'."\n".'<th>'.$data['title'].'</th>'."\n";
	foreach ($data['edition'] as $edition) {
		$html .= '<th>'.$edition.'</th>';
	}
	$html .= '</tr>'."\n".'</thead>';
	$html .= '<tbody>';
	for ($i=0; $i<count($data['labels']); $i++) {
		$html .= '<tr>'."\n";
		$html .= '<td>'.$data['labels'][$i].'</td>'."\n";
		for ($ii=0; $ii<count($data['choice']); $ii++) {
			$html .= '<td>'.$data['choice'][$ii][$i].'</td>'."\n";
		}
		$html .= '</tr>'."\n";
	}
	$html .= '</tbody>'."\n".'</table>';
	return $html;
}

function editions_overview() {
	global $post;
	$getPost = (isset($_GET['ver'])) ? $_GET['ver'] : null;
	$class = (isset($_GET['ver'])) ? 'single' : null;
	$html = '';
	$args = array(
		"p"						=> $getPost,
		'post_type'   => 'editions',
		'post_status' => 'publish',
		'order' 			=> 'ASC',
	);
	$editions = new WP_Query( $args );
	if ( $editions->have_posts() ) :
		if (!$getPost) {
			$html .= '<div id="pricing_options">'."\n";
			$html .= '<div id="selectCur">'."\n".'<h3>Select currency</h3>'."\n";
			$html .= '<label class="radio-img">'."\n".'<input class="currency" type="radio" name="currency" value="usd" />'."\n";
			$html .= '<div class="flag flag-us"></div>'."\n".'</label>'."\n";
			$html .= '<label class="radio-img">'."\n".'<input class="currency" type="radio" name="currency" value="gbp" />'."\n";
			$html .= '<div class="flag flag-gb"></div>'."\n".'</label>'."\n";
			$html .= '<label class="radio-img">'."\n".'<input class="currency" type="radio" name="currency" value="eur" />'."\n";
			$html .= '<div class="flag flag-eu"></div>'."\n".'</label>'."\n";
			$html .= '</div>'."\n"; // Close selectCur
			$html .= '<div id="selectSub">'."\n".'<h3>Select subscription</h3>'."\n";
			$html .= '<div class="toggle-input">'."\n";
			$html .= '<span>Monthly</span>'."\n".'<input type="checkbox" id="priceToggle" />'."\n";
			$html .= '<label for="priceToggle">Toggle</label>'."\n".'<span>Annually</span>'."\n";
			$html .= '</div>'."\n"; // Close toggle-input
			$html .= '</div>'."\n"; // Close selectSub
			$html .= '</div>'."\n"; // Close pricing_options
		}
		while ( $editions->have_posts() ) : $editions->the_post();
			$cur = (isset($_GET['cur'])) ? strtoupper($_GET['cur']) : 'USD';
			$sub = (isset($_GET['sub'])) ? $_GET['sub'] : 'monthly';
			$html .= '<section class="edition paper '.$class.'">'."\n";
			$html .= '<header>'."\n";
			$html .= '<span class="edition_price" data-monthly-eur="'.get_field('monthly_EUR').'" data-annualy-eur="'.get_field('annual_EUR').'" data-monthly-usd="'.get_field('monthly_USD').'" data-annualy-usd="'.get_field('annual_USD').'" data-monthly-gbp="'.get_field('monthly_GBP').'" data-annualy-gbp="'.get_field('annual_GBP').'">'."\n";
			if ($sub != 'purchase') {
				$html .= get_field($sub.'_'.$cur).'<small> /'.$sub.'</small>'."\n";
			}
			$html .= "";
			$html .= "</span>\n";
			$html .= '<span class="edition_title">'.get_the_title()."</span>\n";
			$html .= "</header>\n";
			$html .= get_field('edition_overview');
			if (!$getPost) {
				$html .= '<footer>'."\n";
				$trialUrl = get_the_permalink(get_field('trial_form', 'option', false)).'?ver='.get_the_ID().'&verName='.get_the_title().'&cur=usd&sub=monthly';
				$buyUrl = get_the_permalink(get_field('purchase_form', 'option', false)).'?ver='.get_the_ID().'&verName='.get_the_title().'&sub=purchase';
				$html .= '<a href="'.$trialUrl.'" class="blueBtn trialDownload">download FREE trial</a>'."\n";
				$html .= '<span>or</span>'."\n";
				$html .= '<a href="'.$buyUrl.'" class="blueBtn editionDownload">Purchase</a>'."\n";
				$html .= "</footer>\n";
			}
			$html .= "</section>\n";
		endwhile;
		wp_reset_postdata();
	endif;
	return $html;
}

function editions_test_overview() {
	global $post;
	$getPost = (isset($_GET['ver'])) ? $_GET['ver'] : null;
	$class = (isset($_GET['ver'])) ? 'single' : null;
	$html = '';
	$args = array(
		"p"						=> $getPost,
		'post_type'   => 'editions',
		'post_status' => 'publish',
		'order' 			=> 'ASC',
	);
	$editions = new WP_Query( $args );
	if ( $editions->have_posts() ) :
		if (!$getPost) {
			$html .= '<div id="pricing_options">'."\n";
			$html .= '<h3>Pricing and features</h3>';
			$html .= '<div id="selectCur">'."\n".'<h4>Select currency</h4>'."\n";
			$html .= '<label class="radio-img">'."\n".'<input class="currency selected" type="radio" name="currency" value="usd" />'."\n";
			$html .= '<div class="flag flag-us"></div>'."\n".'</label>'."\n";
			$html .= '<label class="radio-img">'."\n".'<input class="currency" type="radio" name="currency" value="gbp" />'."\n";
			$html .= '<div class="flag flag-gb"></div>'."\n".'</label>'."\n";
			$html .= '<label class="radio-img">'."\n".'<input class="currency" type="radio" name="currency" value="eur" />'."\n";
			$html .= '<div class="flag flag-eu"></div>'."\n".'</label>'."\n";
			$html .= '</div>'."\n"; // Close selectCur
			// $html .= '<div id="selectSub">'."\n".'<h3>Select subscription</h3>'."\n";
			// $html .= '<div class="toggle-input">'."\n";
			// $html .= '<span>Monthly</span>'."\n".'<input type="checkbox" id="priceToggle" />'."\n";
			// $html .= '<label for="priceToggle">Toggle</label>'."\n".'<span>Annually</span>'."\n";
			// $html .= '</div>'."\n"; // Close toggle-input
			// $html .= '</div>'."\n"; // Close selectSub
			$html .= '</div>'."\n"; // Close pricing_options
		}
		while ( $editions->have_posts() ) : $editions->the_post();
			$cur = (isset($_GET['cur'])) ? strtoupper($_GET['cur']) : 'USD';
			// $sub = (isset($_GET['sub'])) ? $_GET['sub'] : 'monthly';
			$html .= '<section class="edition paper '.$class.'">'."\n";
			$html .= '<header>'."\n";
			$html .= '<span class="edition_price" data-licence-eur="'.get_field('licence_EUR').'" data-licence-usd="'.get_field('licence_USD').'" data-licence-gbp="'.get_field('licence_GBP').'">'."\n";
			if ($sub != 'purchase') {
				$html .= get_field('licence_'.$cur)."\n";
			}
			$html .= "";
			$html .= "</span>\n";
			$html .= '<small> /per licence</small>'."\n";
			$html .= '<span class="edition_title">'.get_the_title()."</span>\n";
			$html .= "</header>\n";
			$html .= get_field('edition_overview');
			if (!$getPost) {
				$html .= '<footer>'."\n";
				$trialUrl = get_the_permalink(get_field('trial_form', 'option', false)).'?ver='.get_the_ID().'&verName='.get_the_title().'&cur=usd';
				// $buyUrl = get_the_permalink(get_field('purchase_form', 'option', false)).'?ver='.get_the_ID().'&verName='.get_the_title().'&sub=purchase';
				$buyUrl = get_the_permalink(get_field('purchase_form', 'option', false)).'?ver='.get_the_ID().'&verName='.get_the_title();
				$html .= '<a href="'.$trialUrl.'" class="blueBtn trialDownload">download FREE trial</a>'."\n";
				$html .= '<span>or</span>'."\n";
				$html .= '<a href="'.$buyUrl.'" class="blueBtn editionDownload">How to buy</a>'."\n";
				$html .= "</footer>\n";
			}
			$html .= "</section>\n";
		endwhile;
		wp_reset_postdata();
	endif;
	return $html;
}

// Theme Shortcodes
function showmodule_shortcode($moduleid) {
	extract(shortcode_atts(array('id' =>'*'),$moduleid));
	return do_shortcode('[et_pb_section global_module="'.$id.'"][/et_pb_section]');
}
add_shortcode('showmodule', 'showmodule_shortcode');

function blurb_icon($icon) {
	extract(shortcode_atts(array('icon' =>'*'),$icon));
	$html = '<span class="icon_wrap"><svg class="icon-'.$icon.'"><use xlink:href="#icon-'.$icon.'"></use></svg></span>';
	return $html;
}
add_shortcode('showIcon', 'blurb_icon');

add_shortcode('carasel', 'screen_carasel');
add_shortcode('pageVideo', 'page_video');
add_shortcode('compare', 'editions_table');
add_shortcode('editions', 'editions_overview');
add_shortcode('editions_test', 'editions_test_overview');

?>
