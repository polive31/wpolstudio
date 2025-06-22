<?php
/*
Plugin Name: Tiny Lightbox Manager
Plugin URI: https://joyeuxgourmet.fr/
Description: Adds a lightbox effect to selected images & galleries
Version: 1.0
Author: WPol Studio
Author URI: https://wpolstudio.com
License: GPL2+
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/* Includes
------------------------------------*/
add_action( 'wp', 'clb_start_plugin');
function clb_start_plugin() {
	if (is_singular()) {
		require_once 'includes/Custom_Lightbox.php';
		CustomLightbox::get_instance();
	}
}









