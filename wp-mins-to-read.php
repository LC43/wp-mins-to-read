<?php

/*
Plugin Name: WP Mins To Read
Plugin URI: https://github.com/twittem/wp-minRead
Description: A simple plugin to generate the minutes to read for WordPress posts. Increase blog readership by qualifying the time commitment needed to read your post.
Version: 1.0.2
Author: Edward McIntyre @twittem
Author URI: http://github.com/twittem/
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-wp-mins-to-read.php' );

//register_activation_hook( __FILE__, array( 'WP_MinsToRead', 'activate' ) );
//register_deactivation_hook( __FILE__, array( 'WP_MinsToRead', 'deactivate' ) );

WP_MinsToRead::get_instance();
