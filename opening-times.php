<?php
/*
Plugin Name: Opening Times plugin
Description: Opening Times plugin for use with Nebulo Design projects
Version: 0.1
Author: Richard Knight
Author URI: http://www.nebulodesign.com
License: GPLv2
*/

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'opening_times_install'); 

/* Runs on plugin deactivation*/
// register_deactivation_hook( __FILE__, 'stripe_checkout_remove' );

function opening_times_install() {

	$json_files = array(
		'group_54ad5f47f3015.json',
		'group_54af9b82ba978.json',
		'group_54b3a2fe5477e.json',
	);

	$source_path = plugin_dir_path( __FILE__ ) . 'acf-json/';
	$destination_path = get_stylesheet_directory() . '/acf-json/';

	foreach ( $json_files	as $file) {
		copy($source_path.$file, $destination_path.$file);
	}

}

function opening_times_remove() {
/* Deletes the database field */
//delete_option('hello_world_data');
}

if( is_admin() && function_exists('acf_add_options_sub_page') )
{
    acf_add_options_sub_page(array(
				'title' => 'Set Opening Times',
				'menu' => 'Shop Opening Times',
				'parent' => 'options-general.php',
				'capability' => 'edit_others_posts'
    ));
    
}

$plugin_dir_path = plugin_dir_path( __FILE__ );

$date_format = 'Y-m-d';
$today_date = date( $date_format );
$yesterday_date = date( $date_format, ( time()-86400 ) );
$use_default_styling = get_field('default_styling', 'options') ? true : false;

include ('functions/current-opening-times.php');
include ('functions/weekly-opening-times.php');
include ('functions/bank-holiday-opening-times.php');
include ('functions/add-default-styling.php');
