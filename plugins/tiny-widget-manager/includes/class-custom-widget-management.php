<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Custom_Widget_Management
{

    /* Returns class instance (singleton method) */
    private static $instance = null;
    /**
     * get_instance
     *
     * @return Custom_Widget_Management
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /* private constructor ensures that the class can only be */
    /* created using the get_instance static function */
    private function __construct()
    {
        $Settings = CWM_Settings::get_instance();
        // Settings pages
        add_action('admin_menu',     array($Settings, 'create_option_page'));
        add_action('admin_init',     array($Settings, 'populate_option_page'));



        $Hooks = CWM_Hooks::get_instance();
        // Admin pages
        add_action('admin_enqueue_scripts',         array($Hooks, 'enqueue_scripts'));
        add_action('in_widget_form',                array($Hooks, 'add_visibility_controls'), 10, 3);
        add_filter('widget_update_callback',        array($Hooks, 'save_widget_controls'), 10, 4);

        // Public pages
        add_action('enqueue_block_editor_assets',   array($Hooks, 'maybe_display_notice_on_block_widget_page'));
        add_action('admin_notices',                 array($Hooks, 'maybe_display_block_editor_notice'));
        add_filter('use_widgets_block_editor',      array($Hooks, 'maybe_disable_block_editor'));
        add_filter('dynamic_sidebar_params',        array($Hooks, 'hydrate_args'));
        add_filter('sidebars_widgets',              array($Hooks, 'filter_widgets_before_output'), 10, 3);
        add_filter('dynamic_sidebar_params',        array($Hooks, 'add_custom_widget_classes'), 10, 3);
    }
}
