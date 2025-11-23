<?php
/**
 * WordPress Core Integration & Enhancements.
 *
 * @package tempone
 */

/**
 * Enqueue GLightbox for gallery lightbox functionality.
 */
function tempone_enqueue_lightbox() {
	// Only load on single posts/pages with gallery.
	if ( ! is_singular() ) {
		return;
	}

	// Check if post content has gallery block.
	global $post;
	if ( ! has_block( 'gallery', $post ) ) {
		return;
	}

	// GLightbox CSS from CDN.
	wp_enqueue_style(
		'glightbox',
		'https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css',
		array(),
		'3.3.0'
	);

	// GLightbox JS from CDN.
	wp_enqueue_script(
		'glightbox',
		'https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js',
		array(),
		'3.3.0',
		true
	);

	// Initialize lightbox.
	wp_enqueue_script(
		'tempone-lightbox',
		get_template_directory_uri() . '/js/lightbox.js',
		array( 'glightbox' ),
		TEMPONE_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tempone_enqueue_lightbox' );

/**
 * Add lightbox attributes to gallery images.
 *
 * Filter gallery block output to wrap images with links to full-size.
 *
 * @param string $block_content Block HTML output.
 * @param array  $block Block data.
 * @return string Modified block HTML.
 */
function tempone_gallery_lightbox( $block_content, $block ) {
	// Only process gallery blocks.
	if ( 'core/gallery' !== $block['blockName'] ) {
		return $block_content;
	}

	// Parse HTML.
	$dom = new DOMDocument();
	// Suppress errors for HTML5 tags.
	libxml_use_internal_errors( true );
	$dom->loadHTML( mb_convert_encoding( $block_content, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();

	$xpath = new DOMXPath( $dom );

	// Find all images in gallery.
	$images = $xpath->query( '//figure[contains(@class, "wp-block-image")]//img' );

	foreach ( $images as $img ) {
		$src = $img->getAttribute( 'src' );
		$alt = $img->getAttribute( 'alt' );

		// Get large-size image URL (priority: large > medium > full/original).
		$attachment_id = $img->getAttribute( 'class' );
		preg_match( '/wp-image-(\d+)/', $attachment_id, $matches );

		if ( ! empty( $matches[1] ) ) {
			// Try large first.
			$large_image = wp_get_attachment_image_url( $matches[1], 'large' );
			if ( $large_image ) {
				$src = $large_image;
			} else {
				// Fallback to medium.
				$medium_image = wp_get_attachment_image_url( $matches[1], 'medium' );
				if ( $medium_image ) {
					$src = $medium_image;
				} else {
					// Final fallback to original.
					$full_image = wp_get_attachment_image_url( $matches[1], 'full' );
					if ( $full_image ) {
						$src = $full_image;
					}
				}
			}
		}

		// Get description from attachment metadata.
		$description = '';
		$title       = '';
		if ( ! empty( $matches[1] ) ) {
			$attachment_id = $matches[1];

			// Try description field first (from media library).
			$attachment_description = get_post_field( 'post_content', $attachment_id );
			if ( $attachment_description ) {
				$description = trim( $attachment_description );
			}

			// Fallback to title if no description.
			if ( ! $description ) {
				$attachment_title = get_post_field( 'post_title', $attachment_id );
				if ( $attachment_title ) {
					$description = trim( $attachment_title );
				}
			}
		}

		// Final fallback to alt text.
		if ( ! $description && $alt ) {
			$description = $alt;
		}

		// Check if image is already wrapped in <a>.
		$parent = $img->parentNode;
		if ( 'a' === $parent->nodeName ) {
			// Already has link, modify it.
			$parent->setAttribute( 'href', $src );
			$parent->setAttribute( 'class', 'glightbox' );
			$parent->setAttribute( 'data-gallery', 'gallery-block' );
			if ( $description ) {
				$parent->setAttribute( 'data-description', $description );
			}
		} else {
			// Wrap image with link.
			$link = $dom->createElement( 'a' );
			$link->setAttribute( 'href', $src );
			$link->setAttribute( 'class', 'glightbox' );
			$link->setAttribute( 'data-gallery', 'gallery-block' );
			if ( $description ) {
				$link->setAttribute( 'data-description', $description );
			}

			// Clone image and replace.
			$img_clone = $img->cloneNode( true );
			$parent->replaceChild( $link, $img );
			$link->appendChild( $img_clone );
		}
	}

	return $dom->saveHTML();
}
add_filter( 'render_block', 'tempone_gallery_lightbox', 10, 2 );
