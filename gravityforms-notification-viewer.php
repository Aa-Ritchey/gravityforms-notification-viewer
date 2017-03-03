<?php
/*
 * Plugin Name: Gravity Forms Notification Viewer &beta;
 * Plugin URL: https://github.com/Aa-Ritchey/gravityforms-notification-viewer/
 * Description: Viewing who receives notifications when a form is filled out. This is an unofficial extension.
 * Version: 0.8
 * Author: Aaron Ritchey
 * Author URI: http://aaronritchey.com/
 * License: GPL3
 *
 * Remaining Features:
 * - check that plugin meets best standards
 * - remove unnecessary code and comments
 */

// Code for checking whether this plugin will work.
require_once('inc/check-for-plugin.php');

// Code for building the object which will interface
//   with Gravity Forms.
require_once('inc/gfnv-object.php');



////
// Set up main page
//
function gfnv_admin_menu() {
    add_submenu_page(
    	'gf_edit_forms',
    	'Gravity Forms Notification Viewer',
    	
    	// Adding emphasis so we don't forget
    	//   this isn't a standard feature.
    	'<em>Notification Viewer</em>',
    	
    	1,
    	'gfnv_view_notifications',
    	'gfnv_call_view_notifications'
    );
}

// Putting at lower priority to allow Gravity Forms to create
//   its admin menu items first.
add_action('admin_menu', 'gfnv_admin_menu', $priority=11);


function gfnv_call_view_notifications() {

	if ( gfnv_is_gravityforms_active() ) {
		require('pages/view_notifications.php');
	} else {
		require('pages/no_gravity_forms_plugin.php');
	}
	
}

function gfnv_add_action_links ( $links ) {
	$mylinks = array(
		'<a href="' . admin_url( 'admin.php?page=gfnv_view_notifications' ) . '">View Notifications</a>',
	);
	return array_merge( $mylinks, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'gfnv_add_action_links' );

function gfnv_plugin_not_installed_text() {
	return '<strong>Gravity Forms Notification Viewer</strong> will not work unless <em>Gravity Forms</em> is installed.';
}

function gfnv_load_custom_css($hook) {

	if($hook == 'forms_page_gfnv_view_notifications') {
		wp_enqueue_style( 'gfnv_custom_admin_css', plugins_url('css/view_notifications.css', __FILE__) );
	}
	
}
add_action( 'admin_enqueue_scripts', 'gfnv_load_custom_css' );

