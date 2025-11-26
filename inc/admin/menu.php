<?php
/**
 * Admin Menu Styling & Customization.
 *
 * Modern sidebar menu dengan dynamic brand colors dan smooth animations.
 *
 * @package tempone
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin menu styles now loaded from scss/_admin-menu.scss
 * Compiled into css/admin.css
 *
 * All inline styles (467 lines with 109 !important declarations)
 * have been moved to external SCSS for better caching and performance.
 */

/**
 * Enqueue admin menu JavaScript.
 */
function tempone_admin_menu_scripts() {
	wp_enqueue_script(
		'tempone-admin-menu',
		TEMPONE_URI . '/js/admin-menu.js',
		array(),
		TEMPONE_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'tempone_admin_menu_scripts' );

/**
 * Customize admin menu order.
 *
 * Reorder menu items untuk better UX.
 */
function tempone_custom_menu_order( $menu_order ) {
	if ( ! $menu_order ) {
		return true;
	}

	return array(
		'index.php',                   // Dashboard.
		'tempone-dashboard',           // Custom Dashboard (if using).
		'separator1',                  // First separator.
		'edit.php',                    // Posts.
		'upload.php',                  // Media.
		'edit.php?post_type=page',     // Pages.
		'edit-comments.php',           // Comments.
		'separator2',                  // Second separator.
		'themes.php',                  // Appearance.
		'plugins.php',                 // Plugins.
		'users.php',                   // Users.
		'tools.php',                   // Tools.
		'options-general.php',         // Settings.
		'separator-last',              // Last separator.
	);
}
// Uncomment to enable custom menu order:
// add_filter( 'custom_menu_order', '__return_true' );
// add_filter( 'menu_order', 'tempone_custom_menu_order' );

/**
 * Remove unnecessary admin menu items for clients.
 *
 * Simplify admin interface untuk non-admin users.
 * Uncomment items you want to hide.
 */
function tempone_remove_admin_menus() {
	// Only for non-admin users.
	if ( ! current_user_can( 'manage_options' ) ) {
		// remove_menu_page( 'tools.php' );           // Tools.
		// remove_menu_page( 'options-general.php' ); // Settings.
		// remove_menu_page( 'edit-comments.php' );   // Comments.
	}
}
add_action( 'admin_menu', 'tempone_remove_admin_menus', 999 );

/**
 * Add custom CSS classes to admin body.
 *
 * Allows targeting specific conditions dengan CSS.
 *
 * @param string $classes Current body classes.
 * @return string Modified body classes.
 */
function tempone_admin_body_class( $classes ) {
	// Add custom admin class.
	$classes .= ' tempone-admin';

	// Add role-based class.
	$user = wp_get_current_user();
	if ( ! empty( $user->roles ) ) {
		$classes .= ' user-role-' . $user->roles[0];
	}

	// Add mobile class.
	if ( wp_is_mobile() ) {
		$classes .= ' tempone-mobile-admin';
	}

	return $classes;
}
add_filter( 'admin_body_class', 'tempone_admin_body_class' );

/**
 * Customize admin menu icon for custom post type.
 *
 * Example: Change dashicon untuk specific menu item.
 *
 * @param array $icon_data Icon data array.
 * @param string $icon_file Icon file path.
 * @param int $menu_id Menu ID.
 * @return array Modified icon data.
 */
function tempone_custom_menu_icon( $icon_data, $icon_file, $menu_id ) {
	// Example: Change icon untuk specific menu.
	// if ( $menu_id === 'edit.php' ) {
	// $icon_data = 'dashicons-admin-post';
	// }

	return $icon_data;
}
// add_filter( 'wp_menu_image', 'tempone_custom_menu_icon', 10, 3 );
