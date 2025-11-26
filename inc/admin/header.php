<?php
/**
 * Admin Header/Topbar Styling.
 *
 * Custom styling untuk WordPress admin bar (top black bar).
 *
 * @package tempone
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin bar styles now loaded from scss/_admin-header.scss
 * Compiled into css/admin.css
 *
 * All inline styles (395 lines with 142 !important declarations)
 * have been moved to external SCSS for better caching and performance.
 */

/**
 * Enqueue mobile header JavaScript.
 */
function tempone_admin_header_scripts() {
	wp_enqueue_script(
		'tempone-admin-header',
		TEMPONE_URI . '/js/admin-header.js',
		array(),
		TEMPONE_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'tempone_admin_header_scripts' );

/**
 * Customize admin bar menu items.
 *
 * Add custom New Post & New Page buttons, remove unnecessary items.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 */
function tempone_customize_admin_bar( $wp_admin_bar ) {
	// Remove WordPress logo.
	$wp_admin_bar->remove_node( 'wp-logo' );

	// Remove comments.
	$wp_admin_bar->remove_node( 'comments' );

	// Remove new content menu.
	$wp_admin_bar->remove_node( 'new-content' );

	// Remove updates.
	$wp_admin_bar->remove_node( 'updates' );

	// Add New Post button (solid) - direct to top-secondary.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'tempone-new-post',
			'title'  => '<span class="tempone-btn-icon tempone-btn-icon-post"></span>' . __( 'New Post', 'tempone' ),
			'href'   => admin_url( 'post-new.php' ),
			'parent' => 'top-secondary',
			'meta'   => array(
				'class' => 'tempone-action-btn',
			),
		)
	);

	// Add New Page button (outline) - direct to top-secondary.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'tempone-new-page',
			'title'  => '<span class="tempone-btn-icon tempone-btn-icon-page"></span>' . __( 'New Page', 'tempone' ),
			'href'   => admin_url( 'post-new.php?post_type=page' ),
			'parent' => 'top-secondary',
			'meta'   => array(
				'class' => 'tempone-action-btn',
			),
		)
	);

	// Add Dashboard link to site-name submenu.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'tempone-dashboard',
			'title'  => __( 'Dashboard', 'tempone' ),
			'href'   => admin_url( 'admin.php?page=tempone-dashboard' ),
			'parent' => 'site-name',
			'meta'   => array(
				'class' => 'tempone-dashboard-link',
			),
		)
	);
}
add_action( 'admin_bar_menu', 'tempone_customize_admin_bar', 999 );

/**
 * Hide admin bar for non-admin users on frontend.
 *
 * Clean frontend experience untuk subscribers/customers.
 */
function tempone_hide_admin_bar_frontend() {
	if ( ! current_user_can( 'manage_options' ) && ! is_admin() ) {
		show_admin_bar( false );
	}
}
add_action( 'after_setup_theme', 'tempone_hide_admin_bar_frontend' );

/**
 * Add custom CSS class to admin bar.
 *
 * @param string $class Current class.
 * @return string Modified class.
 */
function tempone_admin_bar_class( $class ) {
	$class .= ' tempone-admin-bar';

	// Add role-based class.
	$user = wp_get_current_user();
	if ( ! empty( $user->roles ) ) {
		$class .= ' user-role-' . $user->roles[0];
	}

	return $class;
}
add_filter( 'admin_bar_class', 'tempone_admin_bar_class' );

/**
 * Customize "Howdy" text in admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 */
function tempone_replace_howdy( $wp_admin_bar ) {
	$account = $wp_admin_bar->get_node( 'my-account' );

	if ( ! $account ) {
		return;
	}

	// Replace "Howdy" with "Welcome".
	$account->title = str_replace( 'Howdy,', __( 'Welcome,', 'tempone' ), $account->title );

	$wp_admin_bar->add_node( $account );
}
add_action( 'admin_bar_menu', 'tempone_replace_howdy', 25 );
