<?php
/**
 * Admin Footer Mobile Menu
 *
 * Bottom navigation bar untuk mobile admin dengan 5 menu:
 * Dashboard, Pages, Create Post (center), Settings, Plugins
 *
 * @package tempone
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render mobile footer menu.
 *
 * Only visible on mobile (max-width: 782px).
 */
function tempone_admin_footer_mobile_menu() {
	// Only show on admin pages, not frontend.
	if ( ! is_admin() ) {
		return;
	}

	// Get current screen.
	$current_screen = get_current_screen();
	$current_page   = '';

	// Determine active menu based on current screen.
	if ( $current_screen ) {
		if ( 'dashboard' === $current_screen->id || 'tempone-dashboard' === $current_screen->id ) {
			$current_page = 'dashboard';
		} elseif ( 'edit-page' === $current_screen->id || 'page' === $current_screen->id ) {
			$current_page = 'pages';
		} elseif ( 'post-new' === $current_screen->id ) {
			$current_page = 'create';
		} elseif ( strpos( $current_screen->id, 'tempone' ) !== false ) {
			$current_page = 'settings';
		} elseif ( 'plugins' === $current_screen->id ) {
			$current_page = 'plugins';
		}
	}

	?>
	<div class="tempone-footer-mobile-menu">
		<nav class="tempone-footer-nav">
			<!-- Dashboard -->
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=tempone-dashboard' ) ); ?>"
			   class="tempone-footer-item <?php echo 'dashboard' === $current_page ? 'is-active' : ''; ?>">
				<svg class="tempone-footer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/>
				</svg>
				<span class="tempone-footer-label"><?php esc_html_e( 'Dashboard', 'tempone' ); ?></span>
			</a>

			<!-- Pages -->
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>"
			   class="tempone-footer-item <?php echo 'pages' === $current_page ? 'is-active' : ''; ?>">
				<svg class="tempone-footer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill="currentColor"/>
				</svg>
				<span class="tempone-footer-label"><?php esc_html_e( 'Pages', 'tempone' ); ?></span>
			</a>

			<!-- Create Post (Center - Primary) -->
			<a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"
			   class="tempone-footer-item tempone-footer-create <?php echo 'create' === $current_page ? 'is-active' : ''; ?>">
				<svg class="tempone-footer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" fill="currentColor"/>
				</svg>
				<span class="tempone-footer-label"><?php esc_html_e( 'Create', 'tempone' ); ?></span>
			</a>

			<!-- Settings (Tempone Setup) -->
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=tempone-setup' ) ); ?>"
			   class="tempone-footer-item <?php echo 'settings' === $current_page ? 'is-active' : ''; ?>">
				<svg class="tempone-footer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94L14.4 2.81c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" fill="currentColor"/>
				</svg>
				<span class="tempone-footer-label"><?php esc_html_e( 'Settings', 'tempone' ); ?></span>
			</a>

			<!-- Plugins -->
			<a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>"
			   class="tempone-footer-item <?php echo 'plugins' === $current_page ? 'is-active' : ''; ?>">
				<svg class="tempone-footer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M20.5 11H19V7c0-1.1-.9-2-2-2h-4V3.5C13 2.12 11.88 1 10.5 1S8 2.12 8 3.5V5H4c-1.1 0-1.99.9-1.99 2v3.8H3.5c1.49 0 2.7 1.21 2.7 2.7s-1.21 2.7-2.7 2.7H2V20c0 1.1.9 2 2 2h3.8v-1.5c0-1.49 1.21-2.7 2.7-2.7 1.49 0 2.7 1.21 2.7 2.7V22H17c1.1 0 2-.9 2-2v-4h1.5c1.38 0 2.5-1.12 2.5-2.5S21.88 11 20.5 11z" fill="currentColor"/>
				</svg>
				<span class="tempone-footer-label"><?php esc_html_e( 'Plugins', 'tempone' ); ?></span>
			</a>
		</nav>
	</div>
	<?php
}
add_action( 'admin_footer', 'tempone_admin_footer_mobile_menu' );
