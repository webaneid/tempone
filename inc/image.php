<?php
/**
 * Image handling and optimization.
 *
 * @package tempone
 */

/**
 * Register custom image sizes.
 * Sizes are optimized for SEO (Yoast) and modern web standards.
 */
function tempone_register_image_sizes() : void {
	// Override WordPress default sizes with SEO-optimized dimensions.
	// Thumbnail - 16:9 ratio for news thumbnail.
	update_option( 'thumbnail_size_w', 320 );
	update_option( 'thumbnail_size_h', 180 );
	update_option( 'thumbnail_crop', 1 ); // Hard crop.

	// Medium - 16:9 ratio for news medium.
	update_option( 'medium_size_w', 640 );
	update_option( 'medium_size_h', 360 );
	update_option( 'medium_crop', 1 ); // Hard crop.

	// Medium Large - 16:9 ratio for news medium large.
	update_option( 'medium_large_size_w', 960 );
	update_option( 'medium_large_size_h', 540 );

	// Large - 16:9 ratio for news large.
	update_option( 'large_size_w', 1280 );
	update_option( 'large_size_h', 720 );
	update_option( 'large_crop', 1 ); // Hard crop.

	// Square sizes - for avatars, widgets, icons.
	add_image_size( 'tempone-square', 300, 300, true );
	add_image_size( 'tempone-square-sm', 150, 150, true );

	// News sizes - 16:9 aspect ratio.
	add_image_size( 'tempone-news-sm', 480, 270, true );
	add_image_size( 'tempone-news-md', 800, 450, true );
	add_image_size( 'tempone-news-lg', 1024, 576, true );
	add_image_size( 'tempone-news-xl', 1600, 900, true );

	// Hero/Banner - ultra wide for homepage hero.
	add_image_size( 'tempone-hero', 1920, 1080, true );

	// Open Graph / Social sharing - 1200x630 (Facebook/LinkedIn standard).
	add_image_size( 'tempone-og', 1200, 630, true );
}
add_action( 'after_setup_theme', 'tempone_register_image_sizes' );

/**
 * Enable WebP support in WordPress media uploads.
 */
function tempone_enable_webp_upload( array $mimes ) : array {
	$mimes['webp'] = 'image/webp';
	return $mimes;
}
add_filter( 'upload_mimes', 'tempone_enable_webp_upload' );

/**
 * Fix WebP thumbnail display in media library.
 */
function tempone_webp_display( array $result, string $path ) : array {
	if ( ! isset( $result['type'] ) ) {
		$file_info = wp_check_filetype( $path );
		if ( 'webp' === $file_info['ext'] ) {
			$result = array(
				'ext'  => 'webp',
				'type' => 'image/webp',
				'proper_filename' => false,
			);
		}
	}
	return $result;
}
add_filter( 'wp_check_filetype_and_ext', 'tempone_webp_display', 10, 2 );

/**
 * Auto-generate WebP versions of uploaded images.
 * Creates WebP alongside original format for better performance.
 *
 * @param array $metadata Image metadata.
 * @return array Modified metadata.
 */
function tempone_generate_webp_on_upload( array $metadata ) : array {
	if ( ! isset( $metadata['file'] ) ) {
		return $metadata;
	}

	$upload_dir = wp_upload_dir();
	$file_path  = $upload_dir['basedir'] . '/' . $metadata['file'];

	// Only process if GD library with WebP support is available.
	if ( ! function_exists( 'imagewebp' ) ) {
		return $metadata;
	}

	// Generate WebP for main image.
	tempone_create_webp_image( $file_path, $metadata );

	// Generate WebP for all sizes.
	if ( ! empty( $metadata['sizes'] ) && is_array( $metadata['sizes'] ) ) {
		$base_dir = dirname( $file_path );

		foreach ( $metadata['sizes'] as $size => $size_data ) {
			$size_path = $base_dir . '/' . $size_data['file'];
			tempone_create_webp_image( $size_path, $size_data );
		}
	}

	return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'tempone_generate_webp_on_upload' );

/**
 * Create WebP version of an image.
 *
 * @param string $file_path Path to source image.
 * @param array  $metadata  Image metadata (optional, for mime type detection).
 * @return bool True on success, false on failure.
 */
function tempone_create_webp_image( string $file_path, array $metadata = array() ) : bool {
	// Skip if file doesn't exist.
	if ( ! file_exists( $file_path ) ) {
		return false;
	}

	// Skip if already WebP.
	if ( str_ends_with( strtolower( $file_path ), '.webp' ) ) {
		return false;
	}

	// Determine mime type.
	$mime_type = $metadata['mime-type'] ?? mime_content_type( $file_path );

	// Create image resource based on mime type.
	$image = match ( $mime_type ) {
		'image/jpeg' => imagecreatefromjpeg( $file_path ),
		'image/png'  => imagecreatefrompng( $file_path ),
		'image/gif'  => imagecreatefromgif( $file_path ),
		default      => false,
	};

	if ( ! $image ) {
		return false;
	}

	// Preserve transparency for PNG.
	if ( 'image/png' === $mime_type ) {
		imagepalettetotruecolor( $image );
		imagealphablending( $image, true );
		imagesavealpha( $image, true );
	}

	// Generate WebP path.
	$webp_path = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $file_path );

	// Create WebP with 85% quality (optimal balance between size and quality).
	$success = imagewebp( $image, $webp_path, 85 );

	// Free memory.
	imagedestroy( $image );

	return $success;
}

/**
 * Get responsive image srcset with WebP support.
 * Returns WebP version if available, falls back to original.
 *
 * @param int    $attachment_id Image attachment ID.
 * @param string $size          Image size name.
 * @return string Image srcset attribute value.
 */
function tempone_get_webp_srcset( int $attachment_id, string $size = 'full' ) : string {
	$srcset = wp_get_attachment_image_srcset( $attachment_id, $size );

	if ( ! $srcset ) {
		return '';
	}

	// Replace extensions with .webp if WebP files exist.
	$srcset_array = explode( ', ', $srcset );
	$webp_srcset  = array();

	foreach ( $srcset_array as $srcset_item ) {
		list( $url, $descriptor ) = explode( ' ', $srcset_item, 2 );

		// Check if WebP version exists.
		$webp_url  = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $url );
		$webp_path = str_replace( wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $webp_url );

		if ( file_exists( $webp_path ) ) {
			$webp_srcset[] = $webp_url . ' ' . $descriptor;
		} else {
			$webp_srcset[] = $srcset_item;
		}
	}

	return implode( ', ', $webp_srcset );
}

/**
 * Output responsive image with WebP support using <picture> element.
 *
 * @param int    $attachment_id Image attachment ID.
 * @param string $size          Image size name.
 * @param array  $attr          Additional attributes for <img> tag.
 * @return void
 */
function tempone_picture_webp( int $attachment_id, string $size = 'full', array $attr = array() ) : void {
	$image_src    = wp_get_attachment_image_src( $attachment_id, $size );
	$image_srcset = tempone_get_webp_srcset( $attachment_id, $size );
	$image_alt    = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

	if ( ! $image_src ) {
		return;
	}

	// Default attributes.
	$defaults = array(
		'loading' => 'lazy',
		'class'   => '',
		'alt'     => $image_alt ? esc_attr( $image_alt ) : '',
	);

	$attr = wp_parse_args( $attr, $defaults );

	// Build attributes string.
	$attr_string = '';
	foreach ( $attr as $key => $value ) {
		if ( 'src' !== $key && 'srcset' !== $key && 'sizes' !== $key ) {
			$attr_string .= sprintf( ' %s="%s"', $key, esc_attr( $value ) );
		}
	}

	// Check if WebP version exists.
	$webp_url  = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $image_src[0] );
	$webp_path = str_replace( wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $webp_url );

	?>
	<picture>
		<?php if ( file_exists( $webp_path ) && $image_srcset ) : ?>
			<source type="image/webp" srcset="<?php echo esc_attr( $image_srcset ); ?>" sizes="<?php echo esc_attr( wp_get_attachment_image_sizes( $attachment_id, $size ) ); ?>">
		<?php endif; ?>
		<img src="<?php echo esc_url( $image_src[0] ); ?>"
			<?php if ( $image_srcset ) : ?>
				srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $attachment_id, $size ) ); ?>"
				sizes="<?php echo esc_attr( wp_get_attachment_image_sizes( $attachment_id, $size ) ); ?>"
			<?php endif; ?>
			width="<?php echo esc_attr( $image_src[1] ); ?>"
			height="<?php echo esc_attr( $image_src[2] ); ?>"
			<?php echo $attr_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	</picture>
	<?php
}

/**
 * Add custom image sizes to media library size dropdown.
 *
 * @param array $sizes Existing image sizes.
 * @return array Modified image sizes.
 */
function tempone_custom_image_sizes( array $sizes ) : array {
	return array_merge(
		$sizes,
		array(
			'tempone-square'    => __( 'Square (300x300)', 'tempone' ),
			'tempone-square-sm' => __( 'Square Small (150x150)', 'tempone' ),
			'tempone-news-sm'   => __( 'News Small (480x270)', 'tempone' ),
			'tempone-news-md'   => __( 'News Medium (800x450)', 'tempone' ),
			'tempone-news-lg'   => __( 'News Large (1024x576)', 'tempone' ),
			'tempone-news-xl'   => __( 'News XLarge (1600x900)', 'tempone' ),
			'tempone-hero'      => __( 'Hero Banner (1920x1080)', 'tempone' ),
			'tempone-og'        => __( 'Social Sharing (1200x630)', 'tempone' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'tempone_custom_image_sizes' );

/**
 * Fix media library thumbnail display for WebP images.
 * Replaces JPG/PNG URLs with WebP if available.
 *
 * @param string       $image     HTML img element or empty string.
 * @param int          $id        Attachment ID.
 * @param string|int[] $size      Image size.
 * @param bool         $icon      Whether the image should be treated as an icon.
 * @param string|array $attr      Attributes for the image markup.
 * @return string Modified HTML img element.
 */
function tempone_fix_admin_thumbnail( $image, int $id, $size, bool $icon, $attr ) : string {
	// Only process in admin area.
	if ( ! is_admin() || empty( $image ) ) {
		return $image;
	}

	// Get attachment source.
	$image_src = wp_get_attachment_image_src( $id, $size );

	if ( ! $image_src ) {
		return $image;
	}

	// Check if WebP version exists.
	$original_url = $image_src[0];
	$webp_url     = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $original_url );

	// Skip if already WebP or no extension to replace.
	if ( $webp_url === $original_url ) {
		return $image;
	}

	$upload_dir = wp_upload_dir();
	$webp_path  = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $webp_url );

	// Replace URL in img tag if WebP exists.
	if ( file_exists( $webp_path ) ) {
		$image = str_replace( $original_url, $webp_url, $image );
	}

	return $image;
}
add_filter( 'wp_get_attachment_image', 'tempone_fix_admin_thumbnail', 10, 5 );

/**
 * Fix media library grid view thumbnail URLs (AJAX response).
 * WordPress media grid uses wp_prepare_attachment_for_js to generate thumbnail data.
 *
 * @param array   $response   Array of prepared attachment data.
 * @param WP_Post $attachment Attachment object.
 * @param array   $meta       Array of attachment meta data.
 * @return array Modified response with WebP URLs.
 */
function tempone_fix_media_grid_thumbnail( array $response, $attachment, array $meta ) : array {
	$upload_dir = wp_upload_dir();

	// Fix all size URLs in the response.
	if ( ! empty( $response['sizes'] ) && is_array( $response['sizes'] ) ) {
		foreach ( $response['sizes'] as $size_name => $size_data ) {
			if ( ! empty( $size_data['url'] ) ) {
				$original_url = $size_data['url'];

				// Check if file is already WebP.
				if ( preg_match( '/\.webp$/i', $original_url ) ) {
					// Verify WebP file exists, if not remove from sizes.
					$webp_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $original_url );
					if ( ! file_exists( $webp_path ) ) {
						// File doesn't exist, try to find JPG/PNG alternative.
						$jpg_url  = preg_replace( '/\.webp$/i', '.jpg', $original_url );
						$jpg_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $jpg_url );

						if ( file_exists( $jpg_path ) ) {
							$response['sizes'][ $size_name ]['url'] = $jpg_url;
						}
					}
					continue;
				}

				// Try to convert JPG/PNG to WebP.
				$webp_url  = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $original_url );
				$webp_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $webp_url );

				// Use WebP if exists, otherwise keep original.
				if ( $webp_url !== $original_url && file_exists( $webp_path ) ) {
					$response['sizes'][ $size_name ]['url'] = $webp_url;
				}
			}
		}
	}

	// Fix main URL.
	if ( ! empty( $response['url'] ) ) {
		$original_url = $response['url'];

		// Check if file is already WebP.
		if ( preg_match( '/\.webp$/i', $original_url ) ) {
			// Verify WebP file exists, if not try JPG alternative.
			$webp_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $original_url );
			if ( ! file_exists( $webp_path ) ) {
				$jpg_url  = preg_replace( '/\.webp$/i', '.jpg', $original_url );
				$jpg_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $jpg_url );

				if ( file_exists( $jpg_path ) ) {
					$response['url'] = $jpg_url;
				}
			}
		} else {
			// Try to convert JPG/PNG to WebP.
			$webp_url  = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $original_url );
			$webp_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $webp_url );

			if ( file_exists( $webp_path ) ) {
				$response['url'] = $webp_url;
			}
		}
	}

	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'tempone_fix_media_grid_thumbnail', 10, 3 );
