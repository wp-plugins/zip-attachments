<?php
/*
Plugin Name: Zip Attachments
Plugin URI: http://wordpress.org/plugins/zip-attachments/
Description: Add a button to create a zip with the post/page file attachments.
Author: Ricard Torres
Version: 1.4
Author URI: http://php.quicoto.com/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*-----------------------------------------------------------------------------------*/
/* Define the url and path */
/*-----------------------------------------------------------------------------------*/

define('zip_attachments_url', plugins_url() ."/".dirname( plugin_basename( __FILE__ ) ) );
define('zip_attachments_path', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );


/*-----------------------------------------------------------------------------------*/
/* Encue JavaScript */
/*-----------------------------------------------------------------------------------*/

function za_plugin_scripts() {

	wp_enqueue_script( 'za-general', zip_attachments_url . '/js/general.js', array(), '1.0.2', true );
	wp_localize_script( 'za-general', 'za_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'wp_enqueue_scripts', 'za_plugin_scripts' );

/*-----------------------------------------------------------------------------------*/
/* Create Zip */
/*-----------------------------------------------------------------------------------*/

function za_create_zip_callback(){

	$postId = intval(sanitize_text_field($_POST['postId']));

	$filename = get_the_title($postId);

	$args = array(
		'post_type' => 'attachment',
		'posts_per_page' => -1,
		'post_status' =>'any',
		'post_parent' => $postId );

	// Prepare File
	$file = tempnam("tmp", "zip");
	$zip = new ZipArchive();
	$zip->open($file, ZipArchive::OVERWRITE);

	// Loop through the attachments and add them to the file
	$attachments = get_posts( $args );
	if ( $attachments ) {
		foreach ( $attachments as $attachment ) {
			// Get the file name
			$name = explode('/', get_attached_file($attachment->ID) );
			$name = $name[sizeof($name) - 1];
			$zip->addFile(get_attached_file($attachment->ID), $name);
		}
	}

	//Close the file
	$zip->close();

	// Add a download to the Counter

		global $wpdb;
		$meta_name = "_za_counter";

		// Retrieve the meta value from the DB

		$za_download_count = get_post_meta($postId, $meta_name, true) != '' ? get_post_meta($postId, $meta_name, true) : '0';
		$za_download_count = $za_download_count + 1;

		// Update the meta value

		update_post_meta($postId, $meta_name, $za_download_count);

	// We have to return an actual URL, that URL will set the headers to force the download
	echo zip_attachments_url."/download.php?za_filename=".sanitize_title($filename)."&za_file=".$file;

	die();
}

add_action( 'wp_ajax_za_create_zip_file', 'za_create_zip_callback' );
add_action( 'wp_ajax_nopriv_za_create_zip_file', 'za_create_zip_callback' );

/*-----------------------------------------------------------------------------------*/
/* Add the button */
/*-----------------------------------------------------------------------------------*/

function za_show_button($text = 'Download Attachments', $counter = false, $counter_format = '(%)')
{

	$button_counter = '';

	if ($counter === "true"){

		// Build the counter format

		$za_button_counter = ' ';
		$za_button_counter .= $counter_format;

		// Look for the meta data

		global $wpdb;

		$post_ID = get_the_ID();

		$za_download_counter = get_post_meta($post_ID, '_za_counter', true) != '' ? get_post_meta($post_ID, '_za_counter', true) : '0';

		$za_button_counter = str_replace('%', $za_download_counter, $za_button_counter);
	}

	return '<button class="za_download_button" onclick="za_create_zip(\''. get_the_ID() .'\')">'. sanitize_text_field($text) . $za_button_counter .'</button>';

}


/*-----------------------------------------------------------------------------------*/
/* Create the shortcode */
/*-----------------------------------------------------------------------------------*/

function za_show_download_button_callback( $atts ) {

	// Parameters accepted

	extract( shortcode_atts( array(
		'text' => 'Download Attachments',
		'counter' => false,
		'counter_format' => '(%)'
	), $atts ) );


	return za_show_button($text, $counter, $counter_format);
}

add_shortcode( 'za_show_download_button', 'za_show_download_button_callback' );


/*-----------------------------------------------------------------------------------*/
/* Add Download Counter column to the Admin */
/*-----------------------------------------------------------------------------------*/

if  ( ! function_exists( 'za_columns' ) ):

	function za_columns($columns)
	{
		unset($columns['author']);
		return array_merge($columns,
				array('za_counter' =>  __( 'Downloads', 'zip-attachments' )));
	}

	add_filter('manage_posts_columns' , 'za_columns');
	add_filter('manage_pages_columns' , 'za_columns');

endif;


/*-----------------------------------------------------------------------------------*/
/* Add Values to the new Admin columns */
/*-----------------------------------------------------------------------------------*/

if  ( ! function_exists( 'za_columns_values' ) ):

	function za_columns_values( $column, $post_id ) {
		switch ( $column ) {
		case 'za_counter' :
			echo get_post_meta($post_id, '_za_counter', true) != '' ? get_post_meta($post_id, '_za_counter', true) : '0';
		break;

		}
	}

	add_action( 'manage_posts_custom_column' , 'za_columns_values', 10, 2 );
	add_action( 'manage_pages_custom_column' , 'za_columns_values', 10, 2 );

endif;


/*-----------------------------------------------------------------------------------*/
/* Make our columns are sortable */
/*-----------------------------------------------------------------------------------*/

if  ( ! function_exists( 'za_sortable_columns' ) ):

	function za_sortable_columns( $columns )
	{
		$columns[ 'za_counter' ] = 'za_counter';
		return $columns;
	}

	// Apply this to all public post types

	add_action( 'admin_init', 'za_sort_all_public_post_types' );

	function za_sort_all_public_post_types() {

		foreach ( get_post_types( array( 'public' => true ), 'names' ) as $post_type_name ) {

			add_action( 'manage_edit-' . $post_type_name . '_sortable_columns', 'za_sortable_columns' );
		}

		add_filter( 'request', 'za_column_sort_orderby' );
	}

	// Tell WordPress our fields are numeric

	function za_column_sort_orderby( $vars ) {

		if ( isset( $vars['orderby'] ) && 'za_counter' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => '_za_counter',
				'orderby'  => 'meta_value_num'
			) );
		}
		return $vars;
	}

endif;
