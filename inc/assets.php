<?php
/**
 * Enqueue scripts and styles.
 *
 * @package tempone
 */

function tempone_enqueue_assets() : void {
	$theme_version = wp_get_theme()->get( 'Version' );

	// Google Fonts.
	wp_enqueue_style(
		'tempone-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap',
		array(),
		null
	);

	// Custom theme styles (SCSS compiled - gradients, post layouts, custom designs).
	wp_enqueue_style(
		'tempone-theme',
		TEMPONE_URI . '/css/tempone.css',
		array( 'tempone-fonts' ),
		$theme_version
	);

	// Tailwind CSS via Play CDN (must be script, not stylesheet).
	wp_enqueue_script(
		'tempone-tailwind',
		'https://cdn.tailwindcss.com',
		array(),
		'3.4',
		false // Load in head, not footer.
	);

	// Disable Tailwind's default container to use our custom one.
	// Changed to 'after' to ensure Tailwind is loaded first.
	wp_add_inline_script(
		'tempone-tailwind',
		'window.tailwindConfig = { corePlugins: { container: false } };',
		'after'
	);

	// Main JavaScript.
	wp_enqueue_script(
		'tempone-main',
		TEMPONE_URI . '/js/main.js',
		array(),
		$theme_version,
		true
	);

	// Expandable search.
	wp_enqueue_script(
		'tempone-search-expand',
		TEMPONE_URI . '/js/search-expand.js',
		array(),
		$theme_version,
		true
	);

	// Menu drag-to-scroll.
	wp_enqueue_script(
		'tempone-menu-drag-scroll',
		TEMPONE_URI . '/js/menu-drag-scroll.js',
		array(),
		$theme_version,
		true
	);

	// Submenu toggle.
	wp_enqueue_script(
		'tempone-submenu-toggle',
		TEMPONE_URI . '/js/submenu-toggle.js',
		array(),
		$theme_version,
		true
	);

	// Localize script data.
	wp_localize_script(
		'tempone-main',
		'temponeSettings',
		array(
			'siteUrl'   => esc_url( home_url( '/' ) ),
			'shareText' => esc_html__( 'Share this article', 'tempone' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'tempone_enqueue_assets' );
