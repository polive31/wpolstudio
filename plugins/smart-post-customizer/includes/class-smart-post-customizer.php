<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Smart_Post_Customizer
{

    /* Returns class instance (singleton method) */
    private static $instance = null;
    /**
     * get_instance
     *
     * @return Smart_Post_Customizer
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

        $Admin = SMCP_Admin::get_instance();
        add_action( 'add_meta_boxes',                   array( $Admin, 'add_meta_boxes_cb') );
        add_action( 'save_post',                        array( $Admin, 'save_post_cb') );

        $Public = SMCP_Public::get_instance();
        add_action( 'wp',                               array( $Public, 'conditionally_hide_featured_image') );

    }
}
