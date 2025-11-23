<?php
/**
 * Editor customization - Gutenberg styling.
 *
 * Custom fonts, colors, and styling untuk WordPress Block Editor
 * agar match dengan frontend.
 *
 * @package tempone
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue editor styles.
 */
function tempone_editor_styles() {
	// Google Fonts untuk editor.
	add_editor_style( 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap' );

	// Custom editor CSS.
	add_editor_style( 'css/editor-style.css' );
}
add_action( 'after_setup_theme', 'tempone_editor_styles' );

/**
 * Enqueue block editor assets.
 */
function tempone_block_editor_assets() {
	// Enqueue editor styles untuk Gutenberg.
	wp_enqueue_style(
		'tempone-editor-styles',
		TEMPONE_URI . '/css/editor-style.css',
		array(),
		TEMPONE_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'tempone_block_editor_assets' );
