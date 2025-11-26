<?php
/**
 * Admin pages & styling for Tempone options.
 *
 * @package tempone
 */

/**
 * Return all Tempone admin sections.
 */
function tempone_get_admin_sections() : array {
	$sections = array(
		'tempone-setup'          => array(
			'title'      => __( 'Tempone Setup', 'tempone' ),
			'menu_title' => __( 'Tempone Setup', 'tempone' ),
			'badge'      => __( 'Control Center', 'tempone' ),
			'tagline'    => __( 'Main panel to configure identity, colors, and Tempone theme utilities.', 'tempone' ),
			'location'   => __( 'Use this page as a summary and shortcut to ACF sub-menus.', 'tempone' ),
			'cards'      => array(
				array(
					'label'       => __( 'ACF Options', 'tempone' ),
					'title'       => __( 'General Setting', 'tempone' ),
					'description' => __( 'Brand identity, default hero, fallback copy, and basic SEO meta.', 'tempone' ),
					'link'        => admin_url( 'admin.php?page=tempone-general-setting' ),
					'link_label'  => __( 'Open General Setting', 'tempone' ),
				),
				array(
					'label'       => __( 'Workflow', 'tempone' ),
					'title'       => __( 'Customer Care', 'tempone' ),
					'description' => __( 'Manage editorial contact data, CS, WhatsApp, and help CTA.', 'tempone' ),
					'link'        => admin_url( 'admin.php?page=tempone-customer-care' ),
					'link_label'  => __( 'Open Customer Care', 'tempone' ),
				),
				array(
					'label'       => __( 'SEO & News', 'tempone' ),
					'title'       => __( 'SEO & News Setup', 'tempone' ),
					'description' => __( 'Google News sitemap, AI crawler optimization, and news website SEO guide.', 'tempone' ),
					'link'        => admin_url( 'admin.php?page=tempone-seo-news' ),
					'link_label'  => __( 'Open SEO & News', 'tempone' ),
				),
			),
		),
		'tempone-seo-news'       => array(
			'title'      => __( 'SEO & News Setup', 'tempone' ),
			'menu_title' => __( 'SEO & News', 'tempone' ),
			'badge'      => __( 'Google News Ready', 'tempone' ),
			'tagline'    => __( 'Complete guide for Google News submission, AI crawler optimization, and news website SEO.', 'tempone' ),
			'location'   => __( 'Enhance Yoast SEO Free with NewsArticle schema, Google News sitemap, and AI-friendly metadata.', 'tempone' ),
		),
		'tempone-general-setting' => array(
			'title'      => __( 'General Setting', 'tempone' ),
			'menu_title' => __( 'General Setting', 'tempone' ),
			'badge'      => __( 'Brand Identity', 'tempone' ),
			'tagline'    => __( 'Configure brand identity, logo, tagline, and fallback content for hero blocks.', 'tempone' ),
		),
		'tempone-customer-care'    => array(
			'title'      => __( 'Customer Care', 'tempone' ),
			'menu_title' => __( 'Customer Care', 'tempone' ),
			'badge'      => __( 'Support Channel', 'tempone' ),
			'tagline'    => __( 'All communication channels: editorial email, hotline, WhatsApp, and operating hours.', 'tempone' ),
		),
	);

	return apply_filters( 'tempone/admin/sections', $sections );
}

/**
 * Register options pages via ACF.
 */
function tempone_register_acf_options_pages() : void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	$sections = tempone_get_admin_sections();

	acf_add_options_page(
		array(
			'page_title' => $sections['tempone-setup']['title'],
			'menu_title' => $sections['tempone-setup']['menu_title'],
			'menu_slug'  => 'tempone-setup',
			'capability' => 'manage_options',
			'icon_url'   => 'dashicons-admin-customizer',
			'position'   => 59,
			'redirect'   => false,
		)
	);

	$subpages = array(
		'tempone-general-setting',
		'tempone-customer-care',
	);

	foreach ( $subpages as $slug ) {
		if ( empty( $sections[ $slug ] ) ) {
			continue;
		}

		acf_add_options_sub_page(
			array(
				'page_title'  => $sections[ $slug ]['title'],
				'menu_title'  => $sections[ $slug ]['menu_title'],
				'menu_slug'   => $slug,
				'parent_slug' => 'tempone-setup',
				'capability'  => 'manage_options',
			)
		);
	}
}
add_action( 'acf/init', 'tempone_register_acf_options_pages' );

/**
 * Register SEO & News submenu separately (uses custom render, not ACF).
 * Uses admin_menu with late priority to ensure parent exists.
 */
function tempone_register_seo_news_page() {
	$sections = tempone_get_admin_sections();

	if ( ! empty( $sections['tempone-seo-news'] ) ) {
		add_submenu_page(
			'tempone-setup',
			$sections['tempone-seo-news']['title'],
			$sections['tempone-seo-news']['menu_title'],
			'manage_options',
			'tempone-seo-news',
			'tempone_render_seo_news_page'
		);
	}
}
add_action( 'admin_menu', 'tempone_register_seo_news_page', 999 );

/**
 * Register fallback menu when ACF Options is not available.
 */
function tempone_register_admin_menu_fallback() : void {
	if ( function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	add_menu_page(
		__( 'Tempone Setup', 'tempone' ),
		__( 'Tempone Setup', 'tempone' ),
		'manage_options',
		'tempone-setup',
		'tempone_render_acf_missing_notice',
		'dashicons-admin-customizer',
		59
	);

	$sections = tempone_get_admin_sections();
	// Note: tempone-seo-news is registered separately via tempone_register_seo_news_page().
	$slugs    = array( 'tempone-general-setting', 'tempone-customer-care' );

	foreach ( $slugs as $slug ) {
		if ( empty( $sections[ $slug ] ) ) {
			continue;
		}

		add_submenu_page(
			'tempone-setup',
			$sections[ $slug ]['title'],
			$sections[ $slug ]['menu_title'],
			'manage_options',
			$slug,
			'tempone_render_acf_missing_notice'
		);
	}
}
add_action( 'admin_menu', 'tempone_register_admin_menu_fallback' );

/**
 * Render fallback notice.
 */
function tempone_render_acf_missing_notice() : void {
	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Tempone Setup', 'tempone' ) . '</h1>';
	echo '<p>' . esc_html__( 'Activate Advanced Custom Fields Pro to start using this options page.', 'tempone' ) . '</p>';
	echo '</div>';
}

/**
 * Determine current Tempone admin slug.
 */
function tempone_get_current_admin_page_slug() : ?string {
	if ( empty( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return null;
	}

	$slug     = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sections = tempone_get_admin_sections();

	return isset( $sections[ $slug ] ) ? $slug : null;
}

/**
 * Enqueue admin styles for ALL admin pages.
 *
 * Loads admin.min.css globally to ensure consistent styling across
 * all WordPress admin pages (dashboard, posts, media, users, etc).
 */
function tempone_enqueue_admin_assets( string $hook ) : void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'tempone-admin',
		TEMPONE_URI . '/css/admin.min.css',
		array(),
		$theme_version
	);
}
add_action( 'admin_enqueue_scripts', 'tempone_enqueue_admin_assets' );

/**
 * Register custom meta boxes for Tempone admin pages.
 */
function tempone_register_admin_meta_boxes() : void {
	$sections = tempone_get_admin_sections();

	foreach ( array_keys( $sections ) as $slug ) {
		$hook = ( 'tempone-setup' === $slug ) ? 'toplevel_page_tempone-setup' : 'tempone_page_' . $slug;
		add_action( 'load-' . $hook, 'tempone_prepare_admin_metaboxes' );
	}
}
add_action( 'admin_menu', 'tempone_register_admin_meta_boxes', 20 );

/**
 * Prepare metaboxes for the current screen.
 */
function tempone_prepare_admin_metaboxes() : void {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$slug = tempone_get_current_admin_page_slug();

	add_meta_box(
		'tempone-admin-notes',
		__( 'Team Notes', 'tempone' ),
		'tempone_render_admin_notes_metabox',
		$screen,
		'tempone-admin',
		'high',
		array( 'slug' => $slug )
	);

	/**
	 * Allow developers to register additional metaboxes.
	 *
	 * @param WP_Screen $screen Screen object.
	 * @param string    $slug   Current Tempone admin slug.
	 */
	do_action( 'tempone/options_page/register_metaboxes', $screen, $slug );
}

/**
 * Default meta box content.
 *
 * @param WP_Post|mixed $post         Post object (unused).
 * @param array         $callback_args Callback arguments.
 */
function tempone_render_admin_notes_metabox( $post, array $callback_args ) : void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	$section = $callback_args['args']['slug'] ?? tempone_get_current_admin_page_slug();
	$data    = tempone_get_admin_sections();
	$title   = $data[ $section ]['title'] ?? __( 'Tempone Setup', 'tempone' );

	echo '<p>' . esc_html__(
		'Use this meta box to note internal to-dos, client requests, or other references.',
		'tempone'
	) . '</p>';
	echo '<ul>';
	echo '<li>' . esc_html__( 'Add notes via tempone/options_page/register_metaboxes filter.', 'tempone' ) . '</li>';
	echo '<li>' . esc_html__( 'ACF location for this page: ', 'tempone' ) . esc_html( $data[ $section ]['location'] ?? $title ) . '</li>';
	echo '</ul>';
}

/**
 * Render hero + cards before ACF form.
 */
function tempone_render_admin_intro() : void {
	$slug = tempone_get_current_admin_page_slug();

	if ( ! $slug ) {
		return;
	}

	$sections = tempone_get_admin_sections();
	$data     = $sections[ $slug ] ?? null;

	if ( ! $data ) {
		return;
	}

	$screen = get_current_screen();

	echo '<div class="tempone-admin wrap" id="tempone-admin-' . esc_attr( $slug ) . '">';
	echo '<div class="tempone-admin__hero">';
	echo '<div class="tempone-admin__hero-content">';
	if ( ! empty( $data['badge'] ) ) {
		echo '<span class="tempone-admin__badge">' . esc_html( $data['badge'] ) . '</span>';
	}
	echo '<h1>' . esc_html( $data['title'] ) . '</h1>';
	if ( ! empty( $data['tagline'] ) ) {
		echo '<p>' . esc_html( $data['tagline'] ) . '</p>';
	}
	echo '</div>';
	echo '</div>';

	if ( $screen ) {
		ob_start();
		echo '<div class="tempone-admin__metaboxes">';
		do_meta_boxes( $screen, 'tempone-admin', null );
		echo '</div>';
		$metabox_markup = trim( ob_get_clean() );
		if ( $metabox_markup ) {
			echo $metabox_markup;
		}
	}

	if ( ! empty( $data['cards'] ) && is_array( $data['cards'] ) ) {
		echo '<div class="tempone-admin__cards">';
		foreach ( $data['cards'] as $card ) {
			echo '<div class="tempone-admin__card">';
			if ( ! empty( $card['label'] ) ) {
				echo '<span class="tempone-admin__card-label">' . esc_html( $card['label'] ) . '</span>';
			}
			if ( ! empty( $card['title'] ) ) {
				echo '<h3>' . esc_html( $card['title'] ) . '</h3>';
			}
			if ( ! empty( $card['description'] ) ) {
				echo '<p>' . esc_html( $card['description'] ) . '</p>';
			}
			if ( ! empty( $card['items'] ) && is_array( $card['items'] ) ) {
				echo '<ul>';
				foreach ( $card['items'] as $item ) {
					echo '<li>' . esc_html( $item ) . '</li>';
				}
				echo '</ul>';
			}
			if ( ! empty( $card['link'] ) ) {
				echo '<a class="tempone-admin__cta" href="' . esc_url( $card['link'] ) . '">';
				echo esc_html( $card['link_label'] ?? __( 'Open page', 'tempone' ) );
				echo '<span aria-hidden="true">→</span>';
				echo '</a>';
			}
			echo '</div>';
		}
		echo '</div>';
	}

	do_action( 'tempone/options_page/after_intro', $slug );
	echo '</div>';
}
add_action( 'admin_notices', 'tempone_render_admin_intro' );

/**
 * Render SEO & News Setup page content.
 */
function tempone_render_seo_news_page() {

	$news_sitemap_url = tempone_get_news_sitemap_url();
	$home_url         = home_url();
	$rss_feed_url     = get_feed_link();

	?>
	<style>
		.tempone-seo-panel {
			background: white;
			border: 1px solid #ddd;
			border-radius: 4px;
			padding: 20px;
			margin: 20px 0;
			box-shadow: 0 1px 3px rgba(0,0,0,0.05);
		}
		.tempone-seo-panel h3 {
			margin-top: 0;
			border-bottom: 2px solid #2271b1;
			padding-bottom: 10px;
			color: #1d2327;
		}
		.tempone-seo-checklist {
			list-style: none;
			padding-left: 0;
		}
		.tempone-seo-checklist li {
			padding: 8px 0;
			padding-left: 30px;
			position: relative;
		}
		.tempone-seo-checklist li:before {
			content: '✓';
			position: absolute;
			left: 0;
			color: #46b450;
			font-weight: bold;
			font-size: 18px;
		}
		.tempone-seo-url-box {
			background: #f0f0f1;
			border: 1px solid #c3c4c7;
			border-radius: 4px;
			padding: 12px;
			font-family: monospace;
			font-size: 14px;
			word-break: break-all;
			margin: 10px 0;
		}
		.tempone-seo-url-box code {
			color: #2271b1;
			font-weight: 600;
		}
		.tempone-seo-warning {
			background: #fcf9e8;
			border-left: 4px solid #dba617;
			padding: 12px;
			margin: 15px 0;
		}
		.tempone-seo-success {
			background: #e7f7e7;
			border-left: 4px solid #46b450;
			padding: 12px;
			margin: 15px 0;
		}
		.tempone-seo-steps {
			counter-reset: step-counter;
			list-style: none;
			padding-left: 0;
		}
		.tempone-seo-steps li {
			counter-increment: step-counter;
			padding: 15px 0;
			padding-left: 45px;
			position: relative;
			border-bottom: 1px solid #f0f0f1;
		}
		.tempone-seo-steps li:before {
			content: counter(step-counter);
			position: absolute;
			left: 0;
			top: 15px;
			width: 30px;
			height: 30px;
			background: #2271b1;
			color: white;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: bold;
		}
		.tempone-seo-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
			gap: 20px;
			margin: 20px 0;
		}
	</style>

	<div class="wrap">
		<!-- Google News Sitemap -->
		<div class="tempone-seo-panel">
			<h3>📰 Google News Sitemap</h3>
			<p><?php esc_html_e( 'Your Google News sitemap is general and includes posts from the last 2 days.', 'tempone' ); ?></p>

			<div class="tempone-seo-url-box">
				<strong><?php esc_html_e( 'News Sitemap URL:', 'tempone' ); ?></strong><br>
				<code><?php echo esc_html( $news_sitemap_url ); ?></code>
			</div>

			<p>
				<a href="<?php echo esc_url( $news_sitemap_url ); ?>" class="button button-primary" target="_blank">
					<?php esc_html_e( 'View News Sitemap', 'tempone' ); ?>
				</a>
			</p>

			<div class="tempone-seo-warning">
				<strong><?php esc_html_e( '⚠️ Important:', 'tempone' ); ?></strong>
				<?php esc_html_e( 'Add this URL to Google Search Console under Sitemaps section.', 'tempone' ); ?>
			</div>
		</div>

		<!-- Google News Publisher Center -->
		<div class="tempone-seo-panel">
			<h3>🚀 Google News Publisher Center Submission</h3>
			<p><?php esc_html_e( 'Follow these steps to submit your news website to Google News:', 'tempone' ); ?></p>

			<ol class="tempone-seo-steps">
				<li>
					<strong><?php esc_html_e( 'Go to Google News Publisher Center', 'tempone' ); ?></strong><br>
					<a href="https://publishercenter.google.com/" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Publisher Center', 'tempone' ); ?>
					</a>
				</li>
				<li>
					<strong><?php esc_html_e( 'Add Your Publication', 'tempone' ); ?></strong><br>
					<?php esc_html_e( 'Click "Add publication" and enter your website URL.', 'tempone' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Verify Ownership', 'tempone' ); ?></strong><br>
					<?php esc_html_e( 'Verify via Google Search Console (recommended) or HTML tag.', 'tempone' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Add News Sitemap', 'tempone' ); ?></strong><br>
					<?php esc_html_e( 'In Google Search Console, go to Sitemaps and add:', 'tempone' ); ?><br>
					<code><?php echo esc_html( $news_sitemap_url ); ?></code>
				</li>
				<li>
					<strong><?php esc_html_e( 'Complete Publication Details', 'tempone' ); ?></strong><br>
					<?php esc_html_e( 'Fill in publication name, logo, contact info, and editorial team.', 'tempone' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Submit for Review', 'tempone' ); ?></strong><br>
					<?php esc_html_e( 'Google will review your application (typically 1-2 weeks).', 'tempone' ); ?>
				</li>
			</ol>

			<div class="tempone-seo-success">
				<strong><?php esc_html_e( '✅ Requirements Met:', 'tempone' ); ?></strong><br>
				<?php esc_html_e( 'Your theme includes NewsArticle schema, proper meta tags, and author attribution.', 'tempone' ); ?>
			</div>
		</div>

		<!-- SEO Features Enabled -->
		<div class="tempone-seo-panel">
			<h3>🎯 SEO Features Enabled</h3>
			<p><?php esc_html_e( 'Tempone theme automatically includes these SEO enhancements:', 'tempone' ); ?></p>

			<div class="tempone-seo-grid">
				<div>
					<h4><?php esc_html_e( 'Schema.org Markup', 'tempone' ); ?></h4>
					<ul class="tempone-seo-checklist">
						<li><?php esc_html_e( 'NewsArticle schema', 'tempone' ); ?></li>
						<li><?php esc_html_e( 'Breadcrumb schema', 'tempone' ); ?></li>
						<li><?php esc_html_e( 'Publisher & Author schema', 'tempone' ); ?></li>
					</ul>
				</div>

				<div>
					<h4><?php esc_html_e( 'AI-Friendly Metadata', 'tempone' ); ?></h4>
					<ul class="tempone-seo-checklist">
						<li><?php esc_html_e( 'Dublin Core metadata', 'tempone' ); ?></li>
						<li><?php esc_html_e( 'Citation metadata', 'tempone' ); ?></li>
						<li><?php esc_html_e( 'Enhanced RSS feed', 'tempone' ); ?></li>
					</ul>
				</div>

				<div>
					<h4><?php esc_html_e( 'Open Graph & Twitter', 'tempone' ); ?></h4>
					<ul class="tempone-seo-checklist">
						<li><?php esc_html_e( 'Facebook Open Graph', 'tempone' ); ?></li>
						<li><?php esc_html_e( 'Twitter Card tags', 'tempone' ); ?></li>
						<li><?php esc_html_e( 'Social sharing optimized', 'tempone' ); ?></li>
					</ul>
				</div>

				<div>
					<h4><?php esc_html_e( 'News Optimization', 'tempone' ); ?></h4>
					<ul class="tempone-seo-checklist">
						<li><?php esc_html_e( 'Google News sitemap', 'tempone' ); ?></li>
						<li><?php esc_html_e( 'Freshness signals', 'tempone' ); ?></li>
						<li><?php esc_html_e( 'Robots meta enhanced', 'tempone' ); ?></li>
					</ul>
				</div>
			</div>
		</div>

		<!-- AI Crawler Optimization -->
		<div class="tempone-seo-panel">
			<h3>🤖 AI Crawler Optimization</h3>
			<p><?php esc_html_e( 'Your content is optimized for AI models like ChatGPT, Claude, and Perplexity:', 'tempone' ); ?></p>

			<ul class="tempone-seo-checklist">
				<li><?php esc_html_e( 'Dublin Core metadata for academic/news citations', 'tempone' ); ?></li>
				<li><?php esc_html_e( 'Citation metadata for proper attribution', 'tempone' ); ?></li>
				<li><?php esc_html_e( 'Structured NewsArticle schema', 'tempone' ); ?></li>
				<li><?php esc_html_e( 'Enhanced RSS feed with full content', 'tempone' ); ?></li>
				<li><?php esc_html_e( 'Clear author attribution and bylines', 'tempone' ); ?></li>
				<li><?php esc_html_e( 'Semantic HTML5 markup', 'tempone' ); ?></li>
			</ul>

			<div class="tempone-seo-url-box">
				<strong><?php esc_html_e( 'RSS Feed URL:', 'tempone' ); ?></strong><br>
				<code><?php echo esc_html( $rss_feed_url ); ?></code>
			</div>
		</div>

		<!-- Testing & Validation -->
		<div class="tempone-seo-panel">
			<h3>🧪 Testing & Validation Tools</h3>
			<p><?php esc_html_e( 'Use these tools to validate your SEO implementation:', 'tempone' ); ?></p>

			<div class="tempone-seo-grid">
				<div>
					<h4><?php esc_html_e( 'Facebook Debugger', 'tempone' ); ?></h4>
					<p><?php esc_html_e( 'Test Open Graph tags', 'tempone' ); ?></p>
					<a href="https://developers.facebook.com/tools/debug/" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'tempone' ); ?>
					</a>
				</div>

				<div>
					<h4><?php esc_html_e( 'Twitter Card Validator', 'tempone' ); ?></h4>
					<p><?php esc_html_e( 'Test Twitter Card meta', 'tempone' ); ?></p>
					<a href="https://cards-dev.twitter.com/validator" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'tempone' ); ?>
					</a>
				</div>

				<div>
					<h4><?php esc_html_e( 'Schema Markup Validator', 'tempone' ); ?></h4>
					<p><?php esc_html_e( 'Test structured data', 'tempone' ); ?></p>
					<a href="https://validator.schema.org/" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'tempone' ); ?>
					</a>
				</div>

				<div>
					<h4><?php esc_html_e( 'Google Rich Results Test', 'tempone' ); ?></h4>
					<p><?php esc_html_e( 'Test rich snippets', 'tempone' ); ?></p>
					<a href="https://search.google.com/test/rich-results" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'tempone' ); ?>
					</a>
				</div>
			</div>
		</div>

		<!-- Additional Resources -->
		<div class="tempone-seo-panel">
			<h3>📚 Additional Resources</h3>
			<ul>
				<li>
					<strong><?php esc_html_e( 'Google News Guidelines:', 'tempone' ); ?></strong>
					<a href="https://support.google.com/news/publisher-center/answer/9606710" target="_blank">
						<?php esc_html_e( 'View Guidelines', 'tempone' ); ?>
					</a>
				</li>
				<li>
					<strong><?php esc_html_e( 'Google Search Console:', 'tempone' ); ?></strong>
					<a href="https://search.google.com/search-console" target="_blank">
						<?php esc_html_e( 'Open Console', 'tempone' ); ?>
					</a>
				</li>
				<li>
					<strong><?php esc_html_e( 'Schema.org Documentation:', 'tempone' ); ?></strong>
					<a href="https://schema.org/NewsArticle" target="_blank">
						<?php esc_html_e( 'NewsArticle Docs', 'tempone' ); ?>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<?php
}

/**
 * Inject admin bar logo dynamically for mobile.
 *
 * Uses WordPress custom logo if set, otherwise fallback to theme logo.
 * Injects inline CSS to replace hardcoded "tempone" text with logo image.
 *
 * @since 1.0.0
 */
function tempone_admin_bar_logo() : void {
	// Get custom logo ID from theme customizer.
	$custom_logo_id = get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id ) {
		// Use WordPress custom logo if set.
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
	} else {
		// Fallback to theme logo.
		$logo_url = get_template_directory_uri() . '/img/logo-tempone.svg';
	}

	?>
	<style>
		@media screen and (max-width: 782px) {
			#wpadminbar #wp-admin-bar-root-default::after {
				background-image: url('<?php echo esc_url( $logo_url ); ?>') !important;
			}
		}
	</style>
	<?php
}
add_action( 'admin_head', 'tempone_admin_bar_logo' );
add_action( 'wp_head', 'tempone_admin_bar_logo' );
