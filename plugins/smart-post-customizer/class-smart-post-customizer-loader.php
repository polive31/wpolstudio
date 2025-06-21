<?php

// Exit if accessed directly.

if (!defined('ABSPATH')) {
    exit;
}

/*
Plugin Name: Smart Post Customizer
Description: Smart Controls for Post/Page customization
Version: 1.0
Author: WPol Studio
License: GPL2+
*/

/* Send admin notices whenever required plugins or theme aren't found */
add_action('init', function() {
    smpc_start_plugin();
});

function smpc_start_plugin() {
    // Load the plugin class.
    require_once 'includes/class-smart-post-customizer.php';
    require_once 'admin/class-smpc-admin.php';
    require_once 'public/class-smpc-public.php';

    // Initialize the plugin.
    Smart_Post_Customizer::get_instance();
}