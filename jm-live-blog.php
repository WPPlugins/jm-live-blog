<?php
/*
Plugin Name: JM Live Blog
Plugin URI:  http://www.jacobmartella.com/wordpress/wordpress-plugins/jm-live-blog
Description: Live blogs are the essential tool for keeping readers up to date in any breaking news situation or sporting event. Using the power of AJAX, JM Live Blog allows you to add a live blog to any post with a simple shortcode to keep your readers in the know.
Version:     1.2
Author:      Jacob Martella
Author URI:  http://www.jacobmartella.com
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages
Text Domain: jm-live-blog
*/

$jm_live_blog_plugin_path = plugin_dir_path( __FILE__ );
define('JM_LIVE_BLOG', $jm_live_blog_plugin_path);

/**
 * Enqueue the front end scripts
 */
function jm_live_blog_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jm-live-blog', plugins_url() . '/jm-live-blog/js/jm-live-blog.js' );
	wp_enqueue_style( 'jm-live-blog-css', plugins_url() . '/jm-live-blog/css/jm-live-blog.css' );
	wp_localize_script( 'jm-live-blog', 'jmliveblog', array(
		'post_id'   => get_the_ID(),
		'nonce'     => wp_create_nonce( 'jm-live-blog' ),
		'url'       => admin_url( 'admin-ajax.php' )
	) );

}
add_action( 'wp_enqueue_scripts', 'jm_live_blog_scripts' );
/**
 * Enqueue the back end scripts
 */
function jm_live_blog_admin_scripts() {
	wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'wp-color-picker');
    wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'jm-live-blog-admin', plugins_url() . '/jm-live-blog/js/jm-live-blog-admin.js' );
	wp_enqueue_style( 'jm-live-blog-admin-css', plugins_url() . '/jm-live-blog/css/jm-live-blog-admin.css' );
}
add_action( 'admin_enqueue_scripts', 'jm_live_blog_admin_scripts' );

//* Load the text domain
function jm_live_blog_load_plugin_textdomain() {
	load_plugin_textdomain( 'jm-live-blog', false, dirname(plugin_basename( __FILE__ )) . '/languages/' );
}
add_action( 'plugins_loaded', 'jm_live_blog_load_plugin_textdomain' );

/**
 * Load the admin pages
 */
include_once( JM_LIVE_BLOG . 'admin/jm-live-blog-admin.php' );

/**
 * Register the live blog shortcode
 */
function jm_live_blog_register_shortcode() {
	add_shortcode( 'jm-live-blog', 'jm_live_blog_shortcode' );
}
add_action( 'init','jm_live_blog_register_shortcode' );
/**
 * Returns the html to display the live blog in a post
 *
 * @since 1.0
 * @param $atts
 * @return string, html for live blog
 */
function jm_live_blog_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'title'         => '',
		'description'   => ''
	), $atts ) );

	$html = '';

	if ( get_post_meta( get_the_ID(), 'live_blog_color_scheme', true ) == 'dark' ) {
		$style = 'dark';
	} else {
		$style = '';
	}

    if ( get_post_meta( get_the_ID(), 'live_blog_alert_color', true ) ) {
        $color = 'style="background-color:' . get_post_meta( get_the_ID(), 'live_blog_alert_color', true ) . ';"';
    } else {
        $color = '';
    }

	$html .= '<div id="jm-live-blog" class="jm-live-blog-outer ' . $style . '">';
	$html .= '<div class="jm-live-blog-inner">';
	if ( $title != '' ) {
		$html .= '<h3 class="jm-live-blog-title">' . $title . '</h3>';
	}
	if ( $description != '' ) {
		$html .= '<p class="jm-live-blog-description">' . $description . '</p>';
	}
	$html .= '<div class="jm-live-blog-section-outer">';
	$html .= '<span id="jm-live-blog-new-updates"' . $color . '>' . __( 'New Updates', 'jm-live-blog' ) . '</span>';
	$html .= '<section class="jm-live-blog-section">';
 	$updates = get_post_meta( get_the_ID(), 'live_blog_updates', true );
	$num_update = count ( $updates );
	if ( $updates ) {
		foreach ( $updates as $update ) {
			$content = apply_filters( 'the_content', $update[ 'live_blog_updates_content' ] );
			$html .= '<div id="' . $num_update . '" class="jm-live-blog-update clearfix">';
			$html .= '<div class="live-blog-left">';
			$html .= '<h5 class="live-blog-time">' . $update[ 'live_blog_updates_time' ] . '</h5>';
			$html .= '</div>';
			$html .= '<div class="live-blog-right">';
			$html .= '<h4 class="live-blog-title">' . $update[ 'live_blog_updates_title' ] . '</h4>';
			$html .= '<div class="live-blog-content">' . $content . '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$num_update--;
		}
	}
	$html .= '</section>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';

	return $html;
}

function convert_number_to_words( $number ) {

	$hyphen      = '-';
	$conjunction = ' and ';
	$separator   = ', ';
	$negative    = 'negative ';
	$decimal     = ' point ';
	$dictionary  = array(
		0                   => 'zero',
		1                   => 'one',
		2                   => 'two',
		3                   => 'three',
		4                   => 'four',
		5                   => 'five',
		6                   => 'six',
		7                   => 'seven',
		8                   => 'eight',
		9                   => 'nine',
		10                  => 'ten',
		11                  => 'eleven',
		12                  => 'twelve',
		13                  => 'thirteen',
		14                  => 'fourteen',
		15                  => 'fifteen',
		16                  => 'sixteen',
		17                  => 'seventeen',
		18                  => 'eighteen',
		19                  => 'nineteen',
		20                  => 'twenty',
		30                  => 'thirty',
		40                  => 'fourty',
		50                  => 'fifty',
		60                  => 'sixty',
		70                  => 'seventy',
		80                  => 'eighty',
		90                  => 'ninety',
		100                 => 'hundred',
		1000                => 'thousand',
		1000000             => 'million',
		1000000000          => 'billion',
		1000000000000       => 'trillion',
		1000000000000000    => 'quadrillion',
		1000000000000000000 => 'quintillion'
	);

	if ( ! is_numeric( $number ) ) {
		return false;
	}

	if ( ( $number >= 0 && ( int ) $number < 0 ) || ( int ) $number < 0 - PHP_INT_MAX ) {
		// overflow
		trigger_error(
			'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
			E_USER_WARNING
		);
		return false;
	}

	if ( $number < 0 ) {
		return $negative . convert_number_to_words( abs( $number ) );
	}

	$string = $fraction = null;

	if ( strpos( $number, '.' ) !== false ) {
		list( $number, $fraction ) = explode( '.', $number );
	}

	switch ( true ) {
		case $number < 21:
			$string = $dictionary[ $number ];
			break;
		case $number < 100:
			$tens   = ( ( int ) ( $number / 10 ) ) * 10;
			$units  = $number % 10;
			$string = $dictionary[ $tens ];
			if ( $units ) {
				$string .= $hyphen . $dictionary[ $units ];
			}
			break;
		case $number < 1000:
			$hundreds  = $number / 100;
			$remainder = $number % 100;
			$string = $dictionary[ $hundreds ] . ' ' . $dictionary[ 100 ];
			if ( $remainder ) {
				$string .= $conjunction . convert_number_to_words( $remainder );
			}
			break;
		default:
			$baseUnit = pow( 1000, floor( log( $number, 1000 ) ) );
			$numBaseUnits = ( int ) ($number / $baseUnit);
			$remainder = $number % $baseUnit;
			$string = convert_number_to_words( $numBaseUnits ) . ' ' . $dictionary[ $baseUnit ];
			if ( $remainder ) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= convert_number_to_words( $remainder);
			}
			break;
	}

	if ( null !== $fraction && is_numeric( $fraction ) ) {
		$string .= $decimal;
		$words = array();
		foreach ( str_split( ( string ) $fraction ) as $number ) {
			$words[] = $dictionary[ $number ];
		}
		$string .= implode( ' ', $words );
	}

	return $string;
}
add_action( 'wp_ajax_nopriv_jm_live_blog_ajax', 'jm_live_blog_ajax' );
add_action( 'wp_ajax_jm_live_blog_ajax', 'jm_live_blog_ajax' );
function jm_live_blog_ajax() {
	check_ajax_referer( 'jm-live-blog', 'nonce' );
	$post_id = $_POST[ 'post_id' ];
	$update_ids = $_POST[ 'update_ids' ];
	$updates = get_post_meta( $post_id, 'live_blog_updates', true );
	$num_update = count ( $updates );

	ob_start();

	if ( $updates ) {
		foreach ( $updates as $update ) {
			if ( ! in_array( $num_update, $update_ids ) ) {
				$content = apply_filters( 'the_content', $update[ 'live_blog_updates_content' ] );
				echo '<div id="' . $num_update . '" class="jm-live-blog-update clearfix">';
				echo '<div class="live-blog-left">';
				echo '<h5 class="live-blog-time">' . $update[ 'live_blog_updates_time' ] . '</h5>';
				echo '</div>';
				echo '<div class="live-blog-right">';
				echo '<h4 class="live-blog-title">' . $update[ 'live_blog_updates_title' ] . '</h4>';
				echo '<div class="live-blog-content">' . $content . '</div>';
				echo '</div>';
				echo '</div>';
				$num_update --;
			}
		}
	}
	$data = ob_get_clean();
	wp_send_json_success( $data );
	wp_die();
}

//* Add a button to the TinyMCE Editor to make it easier to add the shortcode
add_action( 'init', 'jm_live_blog_buttons' );
function jm_live_blog_buttons() {
	add_filter( 'mce_external_plugins', 'jm_live_blog_add_buttons' );
	add_filter( 'mce_buttons', 'jm_live_blog_register_buttons' );
}
function jm_live_blog_add_buttons( $plugin_array ) {
	$plugin_array[ 'jm_live_blog' ] = plugin_dir_url(__FILE__) . 'js/jm-live-blog-button.js';
	return $plugin_array;
}
function jm_live_blog_register_buttons( $buttons ) {
	array_push( $buttons, 'jm_live_blog' );
	return $buttons;
}