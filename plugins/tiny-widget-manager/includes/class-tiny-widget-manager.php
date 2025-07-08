<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Tiny_Widget_Manager
{

    /* Returns class instance (singleton method) */
    private static $instance = null;
    /**
     * get_instance
     *
     * @return Tiny_Widget_Manager
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
        $Settings = TWIM_Settings::get_instance();
        // Settings pages
        add_action('admin_menu',     [ $Settings, 'create_option_page' ]);
        add_action('admin_init',     [ $Settings, 'populate_option_page' ]);

        $Hooks = TWIM_Hooks::get_instance();
        // Admin pages
        add_action('enqueue_block_editor_assets',   [ $Hooks, 'enqueue_block_widget_editor_scripts' ]);
        add_action('admin_enqueue_scripts',         [ $Hooks, 'enqueue_scripts' ]);
        add_action('in_widget_form',                [ $Hooks, 'add_visibility_controls' ], 10, 3);
        add_filter('widget_update_callback',        [ $Hooks, 'save_widget_controls' ], 10, 4);

        add_action('wp_ajax_twim_search_posts',      [ $Hooks, 'twim_search_posts_callback' ]);

        // Public pages
        add_action('enqueue_block_editor_assets',   [ $Hooks, 'maybe_display_notice_on_block_widget_page' ]);
        add_action('admin_notices',                 [ $Hooks, 'maybe_display_block_editor_notice' ]);
        add_filter('use_widgets_block_editor',      [ $Hooks, 'maybe_disable_block_editor' ]);
        add_filter('dynamic_sidebar_params',        [ $Hooks, 'hydrate_args' ]);
        add_filter('sidebars_widgets',              [ $Hooks, 'filter_widgets_before_output' ], 10, 3);
        add_filter('dynamic_sidebar_params',        [ $Hooks, 'add_custom_widget_classes' ], 10, 3);
    }
}
