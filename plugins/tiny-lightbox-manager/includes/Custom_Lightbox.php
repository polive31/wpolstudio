<?php

/* CustomLightbox class
--------------------------------------------*/


// Exit if accessed directly.
if (! defined('ABSPATH')) {
	exit;
}


class CustomLightbox
{

	// Post types where lightbox shall be active
	const POST_TYPES = array(
		'post',
		'page',
	);

	const PLUGIN_VERSION = "1.0";

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;

	private static $instance = NULL;
	public static function get_instance()
	{
		if (NULL === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __construct()
	{
		self::$PLUGIN_PATH = plugin_dir_path(dirname(__FILE__));
		self::$PLUGIN_URI = plugin_dir_url(dirname(__FILE__));

		add_action('wp_enqueue_scripts', array($this, 'enqueue_image_lightbox_scripts_styles'));
		// add_action('wp_enqueue_scripts', array($this, 'enqueue_image_lightbox_css'));

		add_action('casm_styles_async', function ($styles) {
			$styles[] = 'custom-lightbox';
			return $styles;
		});
	}

	/**
	 * enqueue_image_lightbox_scripts_styles
	 *
	 * @return void
	 */
	public function enqueue_image_lightbox_scripts_styles()
	{
		$enqueue = is_singular( self::POST_TYPES );
		// $enqueue = apply_filters('clb_activate_lightbox', $enqueue);
		if (!$enqueue) return;

		// Common vars
		$args = array(
			'in_footer'	=> true,
			'strategy'	=> 'async',
		);

		// Enqueue scripts
		$handle = 'image-lightbox-plugin';
		$src = self::$PLUGIN_URI . 'vendor/imagelightbox.min.js';
		wp_enqueue_script($handle, $src, ['jquery'], self::PLUGIN_VERSION, $args );

		$handle = 'custom-lightbox';
		$src = self::$PLUGIN_URI . 'assets/js/lightbox.js';
		wp_enqueue_script($handle, $src, ['jquery','jquery-touch-punch'], self::PLUGIN_VERSION, $args );

		// wp_enqueue_script('jquery-touch-punch', true);

		// Enqueue styles
		$handle = 'custom-lightbox';
		$src = self::$PLUGIN_URI . 'assets/css/lightbox.css';
		wp_enqueue_style( $handle, $src, [], self::PLUGIN_VERSION );
	}
}
