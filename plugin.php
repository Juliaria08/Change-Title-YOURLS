<?php
/*
Plugin Name: Change Title
Plugin URI: http://happier.allowed.org/change-title
Description: Change Title of all the pages
Version: 0.1
Author: Julian
Author URI: http://jmjl.duckdns.org/
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

$title = yourls_get_option('title');
if ( $title ) {
	yourls_add_filter( 'html_title', 'julian_change_title' );
}
 
function julian_change_title( $value, $context ) {
	//$value = 'Julian\'s Own Url Shortener &mdash;';
	$title = yourls_get_option( 'title' );
	if ( $title ) {
	$value = $title;
	$value = $value . $context;
	} else {
	$value = 'Change-Title-Plugin isnt completly setup.' . $value;
	}
	// return $context; /* Used for debuging :D */
	return $value;
}

yourls_add_action( 'plugins_loaded', 'julian_change_title_page' );
function julian_change_title_page() {
	yourls_register_plugin_page( 'change_title', 'Change the title of YOURLS pages', 'julian_change_title_do_page' );
}

function julian_change_title_do_page() {
	// Check if a form was submitted
	if( isset( $_POST['title'] ) ) {
		// Check nonce
		yourls_verify_nonce( 'change_title' );

		// Process form
		julian_change_title_update();
	}

	// Get value from database
	$title = yourls_get_option( 'title' );

	// Create nonce
	$nonce = yourls_create_nonce( 'change_title' );

	echo <<<HTML
		<h2>Change Title</h2>
		<p>This plugin sets the title of every page on YOURLS</p>
		<form method="post">
		<input type="hidden" name="nonce" value="$nonce" />
		<p><label for="test_option">Enter an title string to prepend to the page title</label> <input type="text" id="title" name="title" value="$title" /></p>
		<p><input type="submit" value="Update value" /></p>
		</form>
HTML;
}

// Update option in database
function julian_change_title_update() {
	$title = $_POST['title'];

	if( $title ) {
		// Validate title.
		$title = htmlspecialchars($title, ENT_QUOTES);

		// Update value in database
		yourls_update_option( 'title', $title );
		echo '<h2><b>Sucessfull update, new prefix title: <code>' . $title . '</code></b></h2>';
	}
}
