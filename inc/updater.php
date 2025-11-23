<?php
/**
 * Theme Auto-Updater
 *
 * Checks for theme updates from GitHub releases and provides
 * automatic update functionality for premium theme distribution.
 *
 * @package tempone
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tempone Theme Updater Class
 */
class Tempone_Updater {

	/**
	 * GitHub repository owner.
	 *
	 * @var string
	 */
	private $github_owner = 'webane';

	/**
	 * GitHub repository name.
	 *
	 * @var string
	 */
	private $github_repo = 'tempone';

	/**
	 * Current theme version.
	 *
	 * @var string
	 */
	private $current_version;

	/**
	 * Theme slug.
	 *
	 * @var string
	 */
	private $theme_slug = 'tempone';

	/**
	 * GitHub API URL for releases.
	 *
	 * @var string
	 */
	private $github_api_url;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$theme = wp_get_theme();
		$this->current_version = $theme->get( 'Version' );
		$this->github_api_url = "https://api.github.com/repos/{$this->github_owner}/{$this->github_repo}/releases/latest";

		// Hook into WordPress update system.
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
		add_filter( 'upgrader_source_selection', array( $this, 'fix_source_folder' ), 10, 3 );

		// Add update checker to admin footer.
		add_action( 'admin_footer', array( $this, 'add_update_checker_notice' ) );
	}

	/**
	 * Check for theme updates.
	 *
	 * @param object $transient Update transient.
	 * @return object Modified transient.
	 */
	public function check_for_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Get latest release from GitHub.
		$remote_version = $this->get_remote_version();

		if ( ! $remote_version ) {
			return $transient;
		}

		// Compare versions.
		if ( version_compare( $this->current_version, $remote_version['version'], '<' ) ) {
			$transient->response[ $this->theme_slug ] = array(
				'theme'       => $this->theme_slug,
				'new_version' => $remote_version['version'],
				'url'         => $remote_version['url'],
				'package'     => $remote_version['package'],
			);
		}

		return $transient;
	}

	/**
	 * Get remote version info from GitHub.
	 *
	 * @return array|false Version info or false on failure.
	 */
	private function get_remote_version() {
		// Check cache first (24 hours).
		$cache_key = 'tempone_update_check';
		$cached = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		// Fetch from GitHub API.
		$response = wp_remote_get(
			$this->github_api_url,
			array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/vnd.github.v3+json',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data['tag_name'] ) || empty( $data['zipball_url'] ) ) {
			return false;
		}

		// Remove 'v' prefix from version.
		$version = ltrim( $data['tag_name'], 'v' );

		$version_info = array(
			'version' => $version,
			'url'     => $data['html_url'],
			'package' => $data['zipball_url'],
		);

		// Cache for 24 hours.
		set_transient( $cache_key, $version_info, DAY_IN_SECONDS );

		return $version_info;
	}

	/**
	 * Fix source folder name after update.
	 *
	 * GitHub downloads come with weird folder names, we need to rename to theme slug.
	 *
	 * @param string $source        Source folder.
	 * @param string $remote_source Remote source.
	 * @param object $upgrader      WP_Upgrader instance.
	 * @return string Fixed source folder.
	 */
	public function fix_source_folder( $source, $remote_source, $upgrader ) {
		global $wp_filesystem;

		// Only for theme updates.
		if ( ! isset( $upgrader->skin->theme ) || $upgrader->skin->theme !== $this->theme_slug ) {
			return $source;
		}

		// Fix the folder name.
		$corrected_source = trailingslashit( $remote_source ) . $this->theme_slug . '/';

		if ( $wp_filesystem->move( $source, $corrected_source, true ) ) {
			return $corrected_source;
		}

		return $source;
	}

	/**
	 * Add update checker notice in admin.
	 */
	public function add_update_checker_notice() {
		$screen = get_current_screen();

		// Only show on themes page.
		if ( 'themes' !== $screen->base ) {
			return;
		}

		$remote_version = $this->get_remote_version();

		if ( ! $remote_version ) {
			return;
		}

		if ( version_compare( $this->current_version, $remote_version['version'], '<' ) ) {
			?>
			<div class="notice notice-info is-dismissible">
				<p>
					<strong><?php esc_html_e( 'Tempone Theme Update Available!', 'tempone' ); ?></strong><br>
					<?php
					printf(
						/* translators: 1: current version, 2: new version */
						esc_html__( 'Version %1$s is available. You have version %2$s. Update now!', 'tempone' ),
						esc_html( $remote_version['version'] ),
						esc_html( $this->current_version )
					);
					?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Force update check (for manual trigger).
	 */
	public static function force_update_check() {
		delete_transient( 'tempone_update_check' );
		delete_site_transient( 'update_themes' );
	}
}

// Initialize updater.
new Tempone_Updater();

/**
 * Add manual update check button to admin.
 */
function tempone_add_manual_update_check() {
	add_action( 'admin_notices', function() {
		$screen = get_current_screen();
		if ( 'themes' === $screen->base && isset( $_GET['tempone_force_check'] ) ) {
			Tempone_Updater::force_update_check();
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Update check completed!', 'tempone' ); ?></p>
			</div>
			<?php
		}
	});
}
add_action( 'admin_init', 'tempone_add_manual_update_check' );
