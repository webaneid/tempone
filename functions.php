<?php
/**
 * Tempone theme bootstrap.
 *
 * @package tempone
 */

if ( ! defined( 'TEMPONE_PATH' ) ) {
		define( 'TEMPONE_PATH', get_template_directory() );
}

if ( ! defined( 'TEMPONE_URI' ) ) {
		define( 'TEMPONE_URI', get_template_directory_uri() );
}

if ( ! defined( 'TEMPONE_VERSION' ) ) {
		define( 'TEMPONE_VERSION', wp_get_theme()->get( 'Version' ) );
}

require_once TEMPONE_PATH . '/inc/setup.php';
require_once TEMPONE_PATH . '/inc/image.php';
require_once TEMPONE_PATH . '/inc/assets.php';
require_once TEMPONE_PATH . '/inc/footer.php';
require_once TEMPONE_PATH . '/inc/post.php';
require_once TEMPONE_PATH . '/inc/admin.php';
require_once TEMPONE_PATH . '/inc/admin-dashboard.php';
require_once TEMPONE_PATH . '/inc/admin-customizer.php';
require_once TEMPONE_PATH . '/inc/editor.php';
require_once TEMPONE_PATH . '/inc/updater.php';
require_once TEMPONE_PATH . '/inc/template-tags.php';
require_once TEMPONE_PATH . '/inc/share.php';
require_once TEMPONE_PATH . '/inc/security.php';
require_once TEMPONE_PATH . '/inc/seo.php';
require_once TEMPONE_PATH . '/inc/widget.php';
require_once TEMPONE_PATH . '/inc/acf-layouts.php';
require_once TEMPONE_PATH . '/inc/wordpress.php';
