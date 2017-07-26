<?php
/**
 * JM-live-blog-updates-admin.php
 *
 * Creates the custom write panel on the edit post page for the live blog.
 *
 * @package Sports Bench
 * @author Jacob Martella
 * @since 1.0
 * @version 1.2
 */
//* Set the array for the color scheme dropdown
global $color_array;
$color_array = [];
$color_array[ 'light' ] = 'Light';
$color_array[ 'dark' ] = 'Dark';

//* Add the meta box
function jm_live_blog_add_meta_boxes() {
	add_meta_box( 'live-blog-updates-meta', __( 'Live Blog Updates', 'jm-live-blog' ) , 'jm_live_blog_meta_box_display', 'post', 'normal', 'default' );
}
add_action( 'admin_init', 'jm_live_blog_add_meta_boxes' );

//* Create the meta box
function jm_live_blog_meta_box_display() {
	global $post;
	global $color_array;
	$updates = get_post_meta( $post->ID, 'live_blog_updates', true );
	$color_scheme = get_post_meta( $post->ID, 'live_blog_color_scheme', true );
    $alert_color = get_post_meta( $post->ID, 'live_blog_alert_color', true );
	wp_nonce_field( 'live_blog_updates_meta_box_nonce', 'live_blog_updates_meta_box_nonce' );

	echo '<div id="jm-live-blog-repeatable-fieldset-one" width="100%">';

	echo '<table class="jm-live-blog-field">';
	echo '<tr>';
	echo '<td><label for="live_blog_color_scheme">' . __( 'Color Scheme', 'jm-live-blog' ) . '</label></td>';
	echo '<td><select class="live_blog_color_scheme" name="live_blog_color_scheme">';
	foreach ( $color_array as $key => $name ) {
		if ( $key == $color_scheme ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		echo '<option value="' . $key . '" ' . $selected . '>' . $name . '</option>';
	}
	echo '</select></td>';
	echo '</tr>';
    echo '<tr>';
    echo '<td><label for="live_blog_alert_color">' . __( 'New Update Alert Color', 'jm-live-blog' ) . '</label></td>';
    echo '<td><input type="text" name="live_blog_alert_color" id="live_blog_alert_color" value="' . $alert_color . '" class="cpa-color-picker" ></td>';
    echo '</tr>';
	echo '</table>';

	echo '<p><a id="live-blog-add-row" class="button" href="#">' . __( 'Add Update', 'jm-live-blog' ) . '</a></p>';

	//* Set up a hidden group of fields for the jQuery to grab
	echo '<table class="live-blog-empty-row screen-reader-text">';
	echo '<tr>';
	echo '<td><label for="live_blog_updates_title">' . __( 'Update Title', 'jm-live-blog' ) . '</label></td>';
	echo '<td><input class="new-field jm_live_blog_input" disabled="disabled" type="text" name="live_blog_updates_title[]" id="live_blog_updates_title" value="" /></td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td><label for="live_blog_updates_time">' . __( 'Update Time', 'jm-live-blog' ) . '</label></td>';
	echo '<td><input class="new-field jm_live_blog_input" disabled="disabled" type="text" name="live_blog_updates_time[]" id="live_blog_updates_time" value="" /></td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td><label for="live_blog_updates_content">' . __( 'Update Content', 'jm-live-blog' ) . '</label></td>';
	echo '<td>';
	wp_editor( '', 'live_blog_updates_content_hidden', $settings = array( 'textarea_name'=>'live_blog_updates_content[]' ) );
	echo '</td>';
	echo '</tr>';

	echo '<tr><td><a class="button live-blog-remove-row" href="#">' . __( 'Remove Update', 'jm-live-blog' ) . '</a></td></tr>';
	echo '</table>';
	
	//* Check for fields already filled out
	if ( $updates ) {

		$i = 1;

		//* Loop through each link the user has already entered
		
		foreach ( $updates as $update ) {
			$num = convert_number_to_words( $i );
			$num = preg_replace( "/[\s_]/", "_", $num );
			$i++;
			echo '<table class="jm-live-blog-fields">';
			echo '<tr>';
			echo '<td><label for="live_blog_updates_title">' . __( 'Update Title', 'jm-live-blog' ) . '</label></td>';
			echo '<td><input type="text" name="live_blog_updates_title[]" id="live_blog_updates_title" class="jm_live_blog_input" value="' . htmlentities( $update[ 'live_blog_updates_title' ] ) . '" /></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<td><label for="live_blog_updates_time">' . __( 'Update Time', 'jm-live-blog' ) . '</label></td>';
			echo '<td><input type="text" name="live_blog_updates_time[]" id="live_blog_updates_time" class="jm_live_blog_input" value="' . $update[ 'live_blog_updates_time' ] . '" /></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<td><label for="live_blog_updates_content">' . __( 'Update Content', 'jm-live-blog' ) . '</label></td>';
			$update_content = $update['live_blog_updates_content'];
			echo '<td>';
			wp_editor( htmlspecialchars_decode( $update_content ), 'live_blog_updates_content_' . $num, $settings = array( 'textarea_name'=>'live_blog_updates_content[]' ) );
			echo '</td>';
			echo '</p>';

			echo '<tr><td><a class="button live-blog-remove-row" href="#">' . __( 'Remove Update', 'jm-live-blog' ) . '</a></td></tr>';
			echo '</table>';

		} //* End foreach

	} else {
		//* Show a blank set of fields if there are no fields filled in
		echo '<table class="jm-live-blog-fields">';
		echo '<tr>';
		echo '<td><label for="live_blog_updates_title">' . __( 'Update Title', 'jm-live-blog' ) . '</label></td>';
		echo '<td><input type="text" name="live_blog_updates_title[]" id="live_blog_updates_title" class="jm_live_blog_input" value="" /></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><label for="live_blog_updates_time">' . __( 'Update Time', 'jm-live-blog' ) . '</label></td>';
		echo '<td><input type="text" name="live_blog_updates_time[]" id="live_blog_updates_time" class="jm_live_blog_input" value="" /></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><label for="live_blog_updates_content">' . __( 'Update Content', 'jm-live-blog' ) . '</label></td>';
		echo '<td>';
		wp_editor( '', 'live_blog_updates_content', $settings = array( 'textarea_name'=>'live_blog_updates_content[]' ) );
		echo '</td>';
		echo '</tr>';

		echo '<tr><td><a class="button live-blog-remove-row" href="#">' . __( 'Remove Update', 'jm-live-blog' ) . '</a></td></tr>';
		echo '</table>';
	}

	echo '</div>';
}

if ( ! function_exists( 'check_color' ) ) {
    function check_color( $value ) {
        if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #
            return true;
        }
        return false;
    }
}

//* Save the data
add_action( 'save_post', 'jm_live_blog_meta_box_save' );
function jm_live_blog_meta_box_save( $post_id ) {
	global $color_array;
	if ( ! isset( $_POST[ 'live_blog_updates_meta_box_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'live_blog_updates_meta_box_nonce' ], 'live_blog_updates_meta_box_nonce' ) )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( ! current_user_can( 'edit_post', $post_id ) )
		return;

	$old = get_post_meta( $post_id, 'live_blog_updates', true );
	$new = array();

	if ( isset( $_POST[ 'live_blog_updates_title' ] ) ) {
        $title = $_POST[ 'live_blog_updates_title' ];
    } else {
	    $title = [];
    }
    if ( isset( $_POST[ 'live_blog_updates_time' ] ) ) {
        $time = $_POST[ 'live_blog_updates_time' ];
    } else {
	    $time = [];
    }
	$content = $_POST[ 'live_blog_updates_content' ];
	$color = $_POST[ 'live_blog_color_scheme' ];
    $alert_color = $_POST[ 'live_blog_alert_color' ];

	$num = count( $title );

	if ( $color && array_key_exists( $color, $color_array ) ) {
		update_post_meta( $post_id, 'live_blog_color_scheme', wp_filter_nohtml_kses( $_POST[ 'live_blog_color_scheme' ] ) );
	}

    $alert_color = trim( $alert_color );
    $alert_color = strip_tags( stripslashes( $alert_color ) );

    if( TRUE === check_color( $alert_color ) ) {
        update_post_meta( $post_id, 'live_blog_alert_color', $alert_color );
    }

	for ( $i = 0; $i < $num; $i++ ) {
		if ( $content[ $i ] == '' or $content[ $i ] == null ) {
			unset( $content[ $i ] );
			$content = array_values( $content );
		}
	}

	for ( $i = 0; $i < $num; $i++ ) {

		if ( ( isset( $title[ $i ] ) && $title[ $i ] != '' ) ) {

			if ( isset( $title[ $i ] ) ) {
				$new[ $i ][ 'live_blog_updates_title' ] = wp_filter_nohtml_kses( $title[ $i ] );
			}

			if( isset( $time[ $i ] ) ) {
				$new[ $i ][ 'live_blog_updates_time' ] = wp_filter_nohtml_kses( $time[ $i ] );
			}

			$new[ $i ][ 'live_blog_updates_content' ] = $content[ $i ];

		}

	}
	if ( ! empty( $new ) && $new != $old ) {
		update_post_meta( $post_id, 'live_blog_updates', $new );
	} elseif ( empty( $new ) && $old ) {
		delete_post_meta( $post_id, 'live_blog_updates', $old );
	}
}