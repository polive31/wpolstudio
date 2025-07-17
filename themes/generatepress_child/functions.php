<?php
/**
 * GeneratePress child theme functions and definitions.
 *
 * Add your custom PHP in this file.
 * Only edit this file if you have direct access to it on your server (to fix errors if they happen).
 */

// Copyright filter
add_filter( 'generate_copyright', function($copyright) {
	$copyright = '<span class="copyright">&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '</span>';
	return $copyright;
} );

