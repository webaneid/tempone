<?php
/**
 * Helpers for ACF flexible sections.
 *
 * @package tempone
 */

/**
 * Render homepage sections defined via ACF Flexible Content.
 */
function tempone_render_home_sections() : void {
	if ( ! function_exists( 'have_rows' ) ) {
		get_template_part( 'tp/section', 'latest' ); // fallback static layout.
		return;
	}

	if ( have_rows( 'homepage_sections' ) ) {
		while ( have_rows( 'homepage_sections' ) ) {
			the_row();
			$layout = get_row_layout();

			get_template_part( 'tp/section', $layout );
		}
	} else {
		get_template_part( 'tp/section', 'latest' );
	}
}

/**
 * Provide default query arguments for repeated section styles.
 */
function tempone_section_query_args( array $args = array() ) : array {
	$defaults = array(
		'post_type'      => 'post',
		'posts_per_page' => 4,
		'post_status'    => 'publish',
	);

	return wp_parse_args( $args, $defaults );
}

/**
 * Convert hex color to RGB values.
 *
 * @param string $colour Hex color code (#ffffff atau ffffff).
 * @return string|false RGB values separated by comma "255, 255, 255" atau false jika invalid.
 */
function tempone_hex2rgb( string $colour ) {
	if ( '#' === $colour[0] ) {
		$colour = substr( $colour, 1 );
	}

	if ( 6 === strlen( $colour ) ) {
		list( $r, $g, $b ) = array(
			$colour[0] . $colour[1],
			$colour[2] . $colour[3],
			$colour[4] . $colour[5],
		);
	} elseif ( 3 === strlen( $colour ) ) {
		list( $r, $g, $b ) = array(
			$colour[0] . $colour[0],
			$colour[1] . $colour[1],
			$colour[2] . $colour[2],
		);
	} else {
		return false;
	}

	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );

	return "$r, $g, $b";
}

/**
 * Generate dynamic CSS variables untuk color customization.
 *
 * Mapping ACF fields ke CSS variables di _tokens.scss:
 * - ane-warna-utama      → --tempone-color-primary
 * - ane-warna-utama-2    → --tempone-color-secondary
 * - ane-warna-text       → --tempone-color-body
 * - ane-warna-terang     → --tempone-color-light
 * - ane-warna-gelap      → --tempone-color-dark
 * - ane-warna-alternatif → --tempone-color-accent
 * - ane-warna-putih      → --tempone-color-white
 *
 * Di-inject ke wp_head dengan priority 999 untuk override default tokens.
 */
function tempone_custom_colors_css() {
	// Color mapping: ACF field name => CSS variable name.
	$color_map = array(
		'ane-warna-utama'      => 'primary',
		'ane-warna-utama-2'    => 'secondary',
		'ane-warna-text'       => 'body',
		'ane-warna-terang'     => 'light',
		'ane-warna-gelap'      => 'dark',
		'ane-warna-alternatif' => 'accent',
		'ane-warna-putih'      => 'white',
	);

	// Start CSS output.
	$css = '<style id="tempone-custom-colors">' . "\n";
	$css .= ':root {' . "\n";

	$has_custom_colors = false;

	foreach ( $color_map as $acf_field => $css_var_name ) {
		$color = get_field( $acf_field, 'option' );

		if ( $color ) {
			$has_custom_colors = true;

			// Hex color.
			$css .= "\t--tempone-color-{$css_var_name}: {$color};\n";

			// RGB variant.
			$rgb = tempone_hex2rgb( $color );
			if ( $rgb ) {
				$css .= "\t--tempone-color-{$css_var_name}-rgb: {$rgb};\n";
			}
		}
	}

	$css .= '}' . "\n";
	$css .= '</style>' . "\n";

	// Only output jika ada custom colors.
	if ( $has_custom_colors ) {
		echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'tempone_custom_colors_css', 999 );

/**
 * Generate dynamic CSS variables untuk ADMIN area.
 *
 * Admin area juga perlu custom colors untuk consistency.
 */
function tempone_custom_colors_admin_css() {
	// Color mapping: ACF field name => CSS variable name.
	$color_map = array(
		'ane-warna-utama'      => 'primary',
		'ane-warna-utama-2'    => 'secondary',
		'ane-warna-text'       => 'body',
		'ane-warna-terang'     => 'light',
		'ane-warna-gelap'      => 'dark',
		'ane-warna-alternatif' => 'accent',
		'ane-warna-putih'      => 'white',
	);

	// Start CSS output.
	$css = '<style id="tempone-custom-colors-admin">' . "\n";
	$css .= ':root {' . "\n";

	$has_custom_colors = false;

	foreach ( $color_map as $acf_field => $css_var_name ) {
		$color = get_field( $acf_field, 'option' );

		if ( $color ) {
			$has_custom_colors = true;

			// Hex color.
			$css .= "\t--tempone-color-{$css_var_name}: {$color};\n";

			// RGB variant.
			$rgb = tempone_hex2rgb( $color );
			if ( $rgb ) {
				$css .= "\t--tempone-color-{$css_var_name}-rgb: {$rgb};\n";
			}
		}
	}

	$css .= '}' . "\n";
	$css .= '</style>' . "\n";

	// Only output jika ada custom colors.
	if ( $has_custom_colors ) {
		echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'admin_head', 'tempone_custom_colors_admin_css', 999 );

/**
 * Register ACF Options Page untuk Theme Customization.
 *
 * Location: Tempone Setup → Theme Customization
 */
function tempone_register_acf_options_customization() {
	// Check if ACF function exists.
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Theme Customization', 'tempone' ),
			'menu_title'  => __( 'Customization', 'tempone' ),
			'menu_slug'   => 'tempone-customization',
			'parent_slug' => 'tempone-setup',
			'capability'  => 'manage_options',
		)
	);
}
// Disabled: This submenu conflicts with main menu registration in inc/admin.php
// add_action( 'acf/init', 'tempone_register_acf_options_customization' );

/**
 * NOTE: Color fields sudah dibuat manual via ACF UI dengan field names:
 * - ane-warna-utama, ane-warna-utama-2, ane-warna-text,
 * - ane-warna-terang, ane-warna-gelap, ane-warna-alternatif
 *
 * Field group registration dihapus untuk avoid duplikasi.
 * Functions tempone_custom_colors_css() dan tempone_custom_colors_admin_css()
 * sudah di-update untuk pakai field names yang ada.
 */

/**
 * ============================================================================
 * CUSTOM TRACKING SCRIPTS
 * ============================================================================
 */

/**
 * Inject Google Analytics / GTM script ke <head>.
 *
 * ACF Field: ane_ga_header
 * Location: wp_head
 */
function tempone_gtm_header_content() {
	$script = get_field( 'ane_ga_header', 'option' );
	if ( ! empty( $script ) ) {
		echo $script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'tempone_gtm_header_content', 10 );

/**
 * Inject custom header script (Search Console, etc.) ke <head>.
 *
 * ACF Field: ane_sc_header
 * Location: wp_head
 */
function tempone_sc_header_content() {
	$script = get_field( 'ane_sc_header', 'option' );
	if ( ! empty( $script ) ) {
		echo $script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'tempone_sc_header_content', 11 );

/**
 * Inject Meta Pixel script ke <head>.
 *
 * ACF Field: ane_metapixel_header
 * Location: wp_head
 */
function tempone_metapixel_header_content() {
	$script = get_field( 'ane_metapixel_header', 'option' );
	if ( ! empty( $script ) ) {
		echo $script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'tempone_metapixel_header_content', 12 );

/**
 * Inject Meta SDK script setelah <body> tag.
 *
 * ACF Field: ane_metasdk_body
 * Location: wp_body_open (immediately after <body>)
 */
function tempone_meta_sdk_script() {
	$script = get_field( 'ane_metasdk_body', 'option' );
	if ( ! empty( $script ) ) {
		echo $script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_body_open', 'tempone_meta_sdk_script' );

/**
 * Inject Google Analytics / GTM footer script ke footer.
 *
 * ACF Field: ane_ga_footer
 * Location: wp_footer
 */
function tempone_gtm_footer_content() {
	$script = get_field( 'ane_ga_footer', 'option' );
	if ( ! empty( $script ) ) {
		echo $script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_footer', 'tempone_gtm_footer_content', 100 );

/**
 * NOTE: Script fields sudah dibuat manual via ACF UI dengan field names:
 * - ane_ga_header, ane_ga_footer, ane_sc_header,
 * - ane_metapixel_header, ane_metasdk_body
 *
 * Field group registration dihapus untuk avoid duplikasi.
 * Injection functions di atas TETAP AKTIF untuk inject scripts ke website.
 */

/**
 * ============================================================================
 * FACEBOOK COMMENTS SYSTEM
 * ============================================================================
 */

/**
 * Load Facebook SDK script.
 *
 * Loads Facebook JavaScript SDK untuk Facebook Comments Social Plugin.
 * Hanya load jika Facebook Comments diaktifkan dan App ID tersedia.
 *
 * IMPORTANT: SDK must be loaded right after opening <body> tag (wp_body_open).
 *
 * Reference: https://developers.facebook.com/docs/plugins/comments/
 */
function tempone_facebook_sdk_script() {
	// Check if Facebook Comments enabled.
	$fb_comments_enabled = get_field( 'ane_facebook_comments_enable', 'option' );
	$fb_app_id           = get_field( 'ane_facebook_app_id', 'option' );

	if ( ! $fb_comments_enabled || empty( $fb_app_id ) ) {
		return;
	}

	// Only load on single posts.
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	// Get locale setting (default: id_ID).
	$fb_locale = get_field( 'ane_facebook_comments_locale', 'option' ) ?: 'id_ID';
	?>
	<div id="fb-root"></div>
	<script async defer crossorigin="anonymous" src="https://connect.facebook.net/<?php echo esc_attr( $fb_locale ); ?>/sdk.js#xfbml=1&version=v24.0&appId=<?php echo esc_attr( $fb_app_id ); ?>"></script>
	<?php
}
add_action( 'wp_body_open', 'tempone_facebook_sdk_script', 1 );

/**
 * Check if Facebook Comments is enabled.
 *
 * @return bool True if enabled, false otherwise.
 */
function tempone_is_facebook_comments_enabled() {
	$enabled = get_field( 'ane_facebook_comments_enable', 'option' );
	$app_id  = get_field( 'ane_facebook_app_id', 'option' );

	return $enabled && ! empty( $app_id );
}

/**
 * Get Facebook App ID.
 *
 * @return string Facebook App ID atau empty string.
 */
function tempone_get_facebook_app_id() {
	return get_field( 'ane_facebook_app_id', 'option' ) ?: '';
}

/**
 * Get Facebook Comments number of posts.
 *
 * @return int Number of comments to show (default 10).
 */
function tempone_get_facebook_comments_num_posts() {
	$num = get_field( 'ane_facebook_comments_num_posts', 'option' );
	return $num ? absint( $num ) : 10;
}

/**
 * Register ACF Field Group untuk Facebook Comments.
 *
 * Creates fields untuk Facebook Comments integration:
 * - Enable/Disable toggle
 * - Facebook App ID
 * - Number of comments to display
 */
function tempone_register_facebook_comments_fields() {
	// Check if ACF function exists.
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_tempone_facebook_comments',
			'title'    => __( 'Facebook Comments', 'tempone' ),
			'fields'   => array(
				// Enable Facebook Comments.
				array(
					'key'           => 'field_ane_facebook_comments_enable',
					'label'         => __( 'Enable Facebook Comments', 'tempone' ),
					'name'          => 'ane_facebook_comments_enable',
					'type'          => 'true_false',
					'instructions'  => __( 'Enable to replace WordPress native comments with Facebook Comments Social Plugin.', 'tempone' ),
					'ui'            => 1,
					'default_value' => 0,
				),
				// Facebook App ID.
				array(
					'key'               => 'field_ane_facebook_app_id',
					'label'             => __( 'Facebook App ID', 'tempone' ),
					'name'              => 'ane_facebook_app_id',
					'type'              => 'text',
					'instructions'      => __( 'Enter your Facebook App ID. Get it from: https://developers.facebook.com/apps/', 'tempone' ),
					'placeholder'       => '352619118625171',
					'prepend'           => __( '<strong>IMPORTANT:</strong> After entering App ID, make sure your website domain is added in Facebook App Settings → Basic → App Domains. Example: yoursite.com', 'tempone' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_ane_facebook_comments_enable',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
				),
				// Locale/Language.
				array(
					'key'               => 'field_ane_facebook_comments_locale',
					'label'             => __( 'Language / Locale', 'tempone' ),
					'name'              => 'ane_facebook_comments_locale',
					'type'              => 'select',
					'instructions'      => __( 'Select language for Facebook Comments plugin.', 'tempone' ),
					'choices'           => array(
						'id_ID' => 'Indonesian (id_ID)',
						'en_US' => 'English (en_US)',
						'en_GB' => 'English UK (en_GB)',
						'ms_MY' => 'Malay (ms_MY)',
						'ar_AR' => 'Arabic (ar_AR)',
						'zh_CN' => 'Chinese Simplified (zh_CN)',
						'ja_JP' => 'Japanese (ja_JP)',
						'ko_KR' => 'Korean (ko_KR)',
					),
					'default_value'     => 'id_ID',
					'allow_null'        => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_ane_facebook_comments_enable',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
				),
				// Number of posts.
				array(
					'key'               => 'field_ane_facebook_comments_num_posts',
					'label'             => __( 'Number of Comments', 'tempone' ),
					'name'              => 'ane_facebook_comments_num_posts',
					'type'              => 'number',
					'instructions'      => __( 'Number of comments to display (default: 10).', 'tempone' ),
					'default_value'     => 10,
					'min'               => 1,
					'max'               => 100,
					'step'              => 1,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_ane_facebook_comments_enable',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'tempone-general-setting',
					),
				),
			),
			'menu_order'            => 20,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
		)
	);
}
add_action( 'acf/init', 'tempone_register_facebook_comments_fields' );

/**
 * Add Open Graph meta tags for Facebook Comments.
 *
 * Facebook Comments requires OG meta tags untuk identify content.
 * Hanya output jika FB Comments enabled.
 */
function tempone_facebook_comments_og_tags() {
	// Only output on single posts.
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	// Check if Facebook Comments enabled.
	if ( ! tempone_is_facebook_comments_enabled() ) {
		return;
	}

	// Check if OG tags already exist from other plugins (Yoast, RankMath, etc.).
	if ( has_action( 'wp_head', 'wpseo_opengraph' ) ||
	     has_action( 'wp_head', 'rank_math_head' ) ||
	     function_exists( 'jetpack_og_tags' ) ) {
		return; // SEO plugin already handling OG tags.
	}

	$fb_app_id = tempone_get_facebook_app_id();
	?>
	<!-- Open Graph Meta Tags for Facebook Comments -->
	<meta property="fb:app_id" content="<?php echo esc_attr( $fb_app_id ); ?>" />
	<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="<?php echo esc_attr( get_the_title() ); ?>" />
	<?php if ( has_post_thumbnail() ) : ?>
		<meta property="og:image" content="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>" />
	<?php endif; ?>
	<?php if ( has_excerpt() ) : ?>
		<meta property="og:description" content="<?php echo esc_attr( wp_strip_all_tags( get_the_excerpt() ) ); ?>" />
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'tempone_facebook_comments_og_tags', 5 );

/**
 * Debug helper untuk Facebook Comments.
 *
 * Tampilkan debug info di HTML comment jika WP_DEBUG enabled.
 * Membantu troubleshooting Facebook Comments issues.
 */
function tempone_facebook_comments_debug() {
	// Only debug if WP_DEBUG enabled.
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return;
	}

	// Only on single posts.
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$enabled = get_field( 'ane_facebook_comments_enable', 'option' );
	$app_id  = get_field( 'ane_facebook_app_id', 'option' );
	$locale  = get_field( 'ane_facebook_comments_locale', 'option' ) ?: 'id_ID';
	$num     = get_field( 'ane_facebook_comments_num_posts', 'option' ) ?: 10;

	?>
	<!-- Facebook Comments Debug Info
	Enabled: <?php echo $enabled ? 'YES' : 'NO'; ?>

	App ID: <?php echo $app_id ? 'Set (' . esc_html( $app_id ) . ')' : 'NOT SET'; ?>

	Locale: <?php echo esc_html( $locale ); ?>

	Num Posts: <?php echo esc_html( $num ); ?>

	Post URL: <?php echo esc_url( get_permalink() ); ?>

	Is Enabled Check: <?php echo tempone_is_facebook_comments_enabled() ? 'TRUE' : 'FALSE'; ?>


	TROUBLESHOOTING STEPS:
	1. Check Facebook App Settings → Basic → App Domains
	   Domain harus ditambahkan: <?php echo esc_html( parse_url( home_url(), PHP_URL_HOST ) ); ?>

	2. Make sure App is LIVE (not in Development mode)
	   https://developers.facebook.com/apps/<?php echo esc_html( $app_id ); ?>/settings/basic/

	3. Clear browser cache and test in Incognito

	4. Check Browser Console for JavaScript errors
	-->
	<?php
}
add_action( 'wp_footer', 'tempone_facebook_comments_debug', 999 );

/**
 * ============================================================================
 * CUSTOM AVATAR SYSTEM (ACF)
 * ============================================================================
 */

/**
 * Use ACF image field as custom avatar.
 *
 * Replaces default Gravatar dengan custom image upload via ACF user field.
 * Field name: gravatar_ane (ACF Image field pada User profile).
 *
 * IMPORTANT: ACF field harus sudah dibuat manual via ACF UI dengan field name: gravatar_ane
 *
 * @param string            $avatar      Image tag untuk avatar.
 * @param int|string|object $id_or_email User ID, email, atau comment object.
 * @param int               $size        Avatar size dalam pixels.
 * @param string            $default     Default avatar URL.
 * @param string            $alt         Alt text untuk image tag.
 * @return string Modified avatar image tag.
 */
function tempone_acf_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
	$user = false;

	// Get user by id or email.
	if ( is_numeric( $id_or_email ) ) {
		$id   = (int) $id_or_email;
		$user = get_user_by( 'id', $id );
	} elseif ( is_object( $id_or_email ) ) {
		if ( ! empty( $id_or_email->user_id ) ) {
			$id   = (int) $id_or_email->user_id;
			$user = get_user_by( 'id', $id );
		}
	} else {
		$user = get_user_by( 'email', $id_or_email );
	}

	// Bail if no user found.
	if ( ! $user ) {
		return $avatar;
	}

	// Get the user ID.
	$user_id = $user->ID;

	// Get ACF image field: gravatar_ane (return format harus Array atau ID).
	$custom_avatar = get_field( 'gravatar_ane', 'user_' . $user_id );

	// Bail if no custom avatar set.
	if ( ! $custom_avatar ) {
		return $avatar;
	}

	// Handle different ACF return formats.
	$avatar_url = '';

	// Determine best square size based on requested avatar size.
	// WordPress avatar sizes typically: 26px (admin bar), 32px (comments), 48px, 96px, 150px.
	$square_size = $size <= 150 ? 'tempone-square-sm' : 'tempone-square'; // 150x150 or 300x300.

	if ( is_array( $custom_avatar ) ) {
		// Return format: Array - prioritize our square sizes for 1:1 aspect ratio.
		if ( isset( $custom_avatar['sizes'][ $square_size ] ) ) {
			$avatar_url = $custom_avatar['sizes'][ $square_size ];
		} elseif ( isset( $custom_avatar['sizes']['tempone-square-sm'] ) ) {
			$avatar_url = $custom_avatar['sizes']['tempone-square-sm']; // Fallback to small square.
		} elseif ( isset( $custom_avatar['sizes']['tempone-square'] ) ) {
			$avatar_url = $custom_avatar['sizes']['tempone-square']; // Fallback to large square.
		} elseif ( isset( $custom_avatar['sizes']['thumbnail'] ) ) {
			$avatar_url = $custom_avatar['sizes']['thumbnail']; // WordPress thumbnail (also square if cropped).
		} elseif ( isset( $custom_avatar['url'] ) ) {
			$avatar_url = $custom_avatar['url'];
		}
	} elseif ( is_numeric( $custom_avatar ) ) {
		// Return format: ID - use our square size instead of arbitrary dimensions.
		$image_data = wp_get_attachment_image_src( $custom_avatar, $square_size );
		$avatar_url = $image_data ? $image_data[0] : '';
	} elseif ( is_string( $custom_avatar ) ) {
		// Return format: URL.
		$avatar_url = $custom_avatar;
	}

	// Bail if no valid URL.
	if ( empty( $avatar_url ) ) {
		return $avatar;
	}

	// Build custom avatar HTML with proper escaping.
	$avatar = sprintf(
		'<img alt="%s" src="%s" class="avatar avatar-%d single-post__author-image" height="%d" width="%d"/>',
		esc_attr( $alt ),
		esc_url( $avatar_url ),
		absint( $size ),
		absint( $size ),
		absint( $size )
	);

	return $avatar;
}
add_filter( 'get_avatar', 'tempone_acf_custom_avatar', 10, 5 );
