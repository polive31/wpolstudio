<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class SMCP_Public
{

    /* Returns class instance (singleton method) */
    private static $instance = null;
    /**
     * get_instance
     *
     * @return SMCP_Public
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * conditionally_hide_featured_image
     *
     * @param  mixed $output
     * @param  mixed $args
     * @return void
     */
    public function conditionally_hide_featured_image()
    {
        if (is_singular() && get_post_meta(get_the_ID(), '_hide_featured_image', true)) {
            remove_action( 'generate_after_header',     'generate_featured_page_header' );
            remove_action( 'generate_before_content',   'generate_featured_page_header_inside_single' );
        }
    }
}
