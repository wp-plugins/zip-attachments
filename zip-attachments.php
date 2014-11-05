<?php
/*
Plugin Name: Zip Attachments
Plugin URI: http://wordpress.org/plugins/zip-attachments/
Description: Add a button to create a zip with the post/page file attachments.
Author: Ricard Torres
Version: 1.2
Author URI: http://php.quicoto.com/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*-----------------------------------------------------------------------------------*/
/* Define the url and path */
/*-----------------------------------------------------------------------------------*/

define('zip_attachments_url', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
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

	// We have to return an actual URL, that URL will set the headers to force the download
	echo zip_attachments_url."/download.php?za_filename=".sanitize_title($filename)."&za_file=".$file;

	die();
}

add_action( 'wp_ajax_za_create_zip_file', 'za_create_zip_callback' );
add_action( 'wp_ajax_nopriv_za_create_zip_file', 'za_create_zip_callback' );

/*-----------------------------------------------------------------------------------*/
/* Add the button */
/*-----------------------------------------------------------------------------------*/

function za_show_button($text = 'Download Attachments')
{

	return '<button class="za_download_button" onclick="za_create_zip(\''. get_the_ID() .'\')">'. sanitize_text_field($text) . '</button>';

}


/*-----------------------------------------------------------------------------------*/
/* Create the shortcode */
/*-----------------------------------------------------------------------------------*/

function za_show_download_button_callback( $atts ) {

	// Parameters accepted

	extract( shortcode_atts( array(
		'text' => 'Download Attachments'
	), $atts ) );



	return za_show_button($text);
}

add_shortcode( 'za_show_download_button', 'za_show_download_button_callback' );
