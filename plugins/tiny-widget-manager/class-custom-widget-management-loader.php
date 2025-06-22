<?php

// Exit if accessed directly.

if (!defined('ABSPATH')) {
    exit;
}

/*
Plugin Name: Tiny Widget Manager
Description: Manage widget visibility.
Version: 1.0
Author: WPol Studio
Author URI: https://wpolstudio.com
License: GPL2+
*/

/* Send admin notices whenever required plugins or theme aren't found */
add_action('init', function() {
    cwm_start_plugin();
});

function cwm_start_plugin() {
    // Load the plugin class.
    require_once 'includes/class-custom-widget-management.php';
    require_once 'includes/class-cwm-hooks.php';
    require_once 'includes/class-cwm-settings.php';

    // Initialize the plugin.
    Custom_Widget_Management::get_instance();
}