<?php
/**
 * Theme setup.
 *
 * @package tempone
 */

if ( ! function_exists( 'tempone_setup' ) ) {
	/**
	 * Register theme supports and defaults.
	 */
	function tempone_setup() : void {
		/**
		 * Load theme translations.
		 * Using load_textdomain() instead of load_theme_textdomain() to bypass WordPress
		 * translation caching mechanism that can prevent new translations from loading.
		 * determine_locale() gets fresh locale without cache interference.
		 */
		$locale = determine_locale();
		$mofile = get_template_directory() . "/languages/tempone-{$locale}.mo";

		if ( file_exists( $mofile ) ) {
			load_textdomain( 'tempone', $mofile );
		}
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'css/editor.css' );

		// Custom logo support
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 100,
				'width'       => 400,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		register_nav_menus(
			array(
				'primary'        => __( 'Primary Menu', 'tempone' ),
				'secondary'      => __( 'Secondary Menu', 'tempone' ),
				'footer'         => __( 'Footer Menu', 'tempone' ),
				'media_network'  => __( 'Media Network Menu', 'tempone' ),
				'footer_bottom'  => __( 'Footer Bottom Menu', 'tempone' ),
			)
		);
	}
}
add_action( 'after_setup_theme', 'tempone_setup' );

/**
 * Set global content width.
 */
function tempone_content_width() : void {
	$GLOBALS['content_width'] = 1280;
}
add_action( 'after_setup_theme', 'tempone_content_width', 0 );

/**
 * Register widget areas.
 */
function tempone_register_sidebars() : void {
	register_sidebar(
		array(
			'name'          => __( 'Main Sidebar', 'tempone' ),
			'id'            => 'sidebar-main',
			'description'   => __( 'Appears on blog index, archive, single post, and search pages.', 'tempone' ),
			'before_widget' => '<div id="%1$s" class="widget mb-8 %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title text-xl font-bold mb-4">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Landing Page Sidebar', 'tempone' ),
			'id'            => 'sidebar-landingpage',
			'description'   => __( 'Appears on landing page flexible content section.', 'tempone' ),
			'before_widget' => '<div id="%1$s" class="widget mb-8 %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title text-xl font-bold mb-4">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'tempone_register_sidebars' );

/**
 * Add Open Graph meta tags to head.
 */
add_action( 'wp_head', 'tempone_open_graph_meta_tags', 5 );
