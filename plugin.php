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

/* Example of an action
 *
 * We're going to add an entry to the menu.
 *
 * The menu is drawn by function yourls_html_menu() in file includes/functions-html.php.
 * Right before the function outputs the closing </ul>, notice the following function call:
 * yourls_do_action( 'admin_menu' );
 * This function says: "hey, for your information, I've just done something called 'admin menu', thought I'd let you know..."
 *
 * We're going to hook into this action and add our menu entry
 */
/*
yourls_add_action( 'admin_menu', 'ozh_sample_add_menu' );
/* This says: when YOURLS does action 'admin_menu', call function 'ozh_sample_add_menu'
 */

/*
function ozh_sample_add_menu() {
	echo '<li><a href="http://ozh.org/">Ozh</a></li>';
}
/* And that's it. Activate the plugin and notice the new menu entry.
 */

 

/* Example of a filter
 *
 * We're going to modify the <title> of pages in the admin area
 *
 * The <title> tag is generated by function yourls_html_head() in includes/functions-html.php
 * Notice the following function call:
 * $title = yourls_apply_filter( 'html_title', 'YOURLS: Your Own URL Shortener' );
 * This function means: give $title the value "YOURLS: Your Own URL Shortener", unless a
 * filter modifies this value.
 *
 * We're going to hook into this filter and modify this value.
 */
 
$title = yourls_get_option('title');
if ( $title ) {
yourls_add_filter( 'html_title', 'julian_change_title' );
}
/* This says: when filter 'html_title' is triggered, send its value to function 'ozh_sample_change_title'
 * and use what this function will return.
 */
 
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
	return $value; // a filter *always* has to return a value
}
/* And that's it. Activate the plugin and notice how the page title changes */

yourls_add_action( 'plugins_loaded', 'julian_change_title_page' );
function julian_change_title_page() {
	yourls_register_plugin_page( 'change_title', 'Change the title of YOURLS pages', 'julian_change_title_do_page' );
	// parameters: page slug, page title, and function that will display the page itself
}

// Display admin page
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
		// Validate test_option. ALWAYS validate and sanitize user input.
		// Here, we want an integer
		// $title = string( $title );
		$title = htmlspecialchars($title, ENT_QUOTES);

		// Update value in database
		yourls_update_option( 'title', $title );
		echo '<h2><b>Sucessfull update, new prefix title: <code>' . $title . '</code></b></h2>';
	}
}
