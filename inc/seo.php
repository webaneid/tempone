<?php
/**
 * SEO and News optimization functions.
 *
 * Enhance Yoast SEO Free with NewsArticle schema, Google News sitemap,
 * AI-friendly metadata, and more for news website optimization.
 *
 * @package tempone
 */

/**
 * Output NewsArticle Schema for Google News and AI crawlers.
 *
 * Critical for Google News indexing and AI attribution.
 */
function tempone_news_article_schema() {
	if ( ! is_single() ) {
		return;
	}

	$post_id = get_the_ID();

	// Get author info.
	$author_id   = get_post_field( 'post_author', $post_id );
	$author_name = get_the_author_meta( 'display_name', $author_id );

	// Get publisher info from Tempone settings.
	$publisher_name = get_option( 'ane_company_name', get_bloginfo( 'name' ) );
	$publisher_logo = get_site_icon_url( 512 );

	// Get featured image.
	$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
	if ( ! $image_url ) {
		$image_url = $publisher_logo; // Fallback.
	}

	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'NewsArticle',
		'headline'         => get_the_title( $post_id ),
		'description'      => get_the_excerpt( $post_id ),
		'image'            => $image_url,
		'datePublished'    => get_the_date( 'c', $post_id ),
		'dateModified'     => get_the_modified_date( 'c', $post_id ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => $author_name,
			'url'   => get_author_posts_url( $author_id ),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => $publisher_name,
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => $publisher_logo,
			),
		),
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => get_permalink( $post_id ),
		),
	);

	// Add article section (categories).
	$categories = get_the_category( $post_id );
	if ( ! empty( $categories ) ) {
		$schema['articleSection'] = $categories[0]->name;
	}

	// Add keywords (tags).
	$tags = get_the_tags( $post_id );
	if ( $tags ) {
		$keywords = array();
		foreach ( $tags as $tag ) {
			$keywords[] = $tag->name;
		}
		$schema['keywords'] = implode( ', ', $keywords );
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'tempone_news_article_schema', 20 );

/**
 * Output Breadcrumb Schema for content hierarchy.
 *
 * Helps Google News understand content organization.
 */
function tempone_breadcrumb_schema() {
	if ( ! is_single() ) {
		return;
	}

	$categories = get_the_category();
	if ( empty( $categories ) ) {
		return;
	}

	$category = $categories[0]; // Primary category.

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => __( 'Home', 'tempone' ),
				'item'     => home_url(),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => $category->name,
				'item'     => get_category_link( $category ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title(),
				'item'     => get_permalink(),
			),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'tempone_breadcrumb_schema', 21 );

/**
 * Output Dublin Core metadata for AI crawlers.
 *
 * Standard for academic/news citations. Used by ChatGPT, Claude, Perplexity.
 */
function tempone_dublin_core_meta() {
	if ( ! is_single() ) {
		return;
	}

	$post_id    = get_the_ID();
	$categories = wp_get_post_categories( $post_id, array( 'fields' => 'names' ) );
	$subject    = ! empty( $categories ) ? implode( ', ', $categories ) : '';

	?>
	<!-- Dublin Core Metadata -->
	<meta name="DC.title" content="<?php echo esc_attr( get_the_title() ); ?>">
	<meta name="DC.creator" content="<?php echo esc_attr( get_the_author() ); ?>">
	<meta name="DC.subject" content="<?php echo esc_attr( $subject ); ?>">
	<meta name="DC.description" content="<?php echo esc_attr( wp_strip_all_tags( get_the_excerpt() ) ); ?>">
	<meta name="DC.publisher" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta name="DC.date" content="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">
	<meta name="DC.type" content="Text">
	<meta name="DC.format" content="text/html">
	<meta name="DC.identifier" content="<?php echo esc_url( get_permalink() ); ?>">
	<meta name="DC.language" content="<?php echo esc_attr( get_locale() ); ?>">
	<?php
}
add_action( 'wp_head', 'tempone_dublin_core_meta', 6 );

/**
 * Output Citation metadata for AI attribution.
 *
 * Helps AI models properly cite your content as a source.
 */
function tempone_citation_meta() {
	if ( ! is_single() ) {
		return;
	}

	?>
	<!-- Citation Metadata -->
	<meta name="citation_title" content="<?php echo esc_attr( get_the_title() ); ?>">
	<meta name="citation_author" content="<?php echo esc_attr( get_the_author() ); ?>">
	<meta name="citation_publication_date" content="<?php echo esc_attr( get_the_date( 'Y/m/d' ) ); ?>">
	<meta name="citation_online_date" content="<?php echo esc_attr( get_the_date( 'Y/m/d' ) ); ?>">
	<meta name="citation_publisher" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta name="citation_journal_title" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta name="citation_abstract" content="<?php echo esc_attr( wp_strip_all_tags( get_the_excerpt() ) ); ?>">
	<?php
}
add_action( 'wp_head', 'tempone_citation_meta', 7 );

/**
 * Output content freshness signals for Google News.
 *
 * Google News prioritizes frequently updated content.
 */
function tempone_content_freshness_meta() {
	if ( ! is_single() ) {
		return;
	}

	?>
	<meta http-equiv="last-modified" content="<?php echo esc_attr( get_the_modified_date( 'D, d M Y H:i:s' ) ); ?> GMT">
	<meta name="revisit-after" content="1 days">
	<?php
}
add_action( 'wp_head', 'tempone_content_freshness_meta', 8 );

/**
 * Enhanced robots meta for news indexing.
 *
 * Instructs search engines to index with maximum snippet/image preview.
 */
function tempone_robots_meta_news() {
	if ( ! is_single() ) {
		return;
	}

	?>
	<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
	<meta name="googlebot" content="index, follow">
	<meta name="googlebot-news" content="index, follow">
	<?php
}
add_action( 'wp_head', 'tempone_robots_meta_news', 1 ); // Priority 1 to load early.

/**
 * Enhance RSS feed with featured images and full content.
 *
 * AI crawlers often use RSS feeds to discover content.
 *
 * @param string $content Feed content.
 * @return string Modified content.
 */
function tempone_enhance_rss_feed( $content ) {
	global $post;

	if ( ! is_feed() ) {
		return $content;
	}

	// Add featured image to RSS.
	if ( has_post_thumbnail( $post->ID ) ) {
		$thumbnail = get_the_post_thumbnail( $post->ID, 'medium', array( 'style' => 'max-width: 100%; height: auto;' ) );
		$content   = '<p>' . $thumbnail . '</p>' . $content;
	}

	// Add categories.
	$categories = get_the_category();
	if ( $categories ) {
		$content   .= '<p><strong>' . __( 'Categories:', 'tempone' ) . '</strong> ';
		$cat_names  = array();
		foreach ( $categories as $cat ) {
			$cat_names[] = $cat->name;
		}
		$content .= implode( ', ', $cat_names ) . '</p>';
	}

	// Add tags.
	$tags = get_the_tags();
	if ( $tags ) {
		$content  .= '<p><strong>' . __( 'Tags:', 'tempone' ) . '</strong> ';
		$tag_names = array();
		foreach ( $tags as $tag ) {
			$tag_names[] = $tag->name;
		}
		$content .= implode( ', ', $tag_names ) . '</p>';
	}

	return $content;
}
add_filter( 'the_content_feed', 'tempone_enhance_rss_feed' );
add_filter( 'the_excerpt_rss', 'tempone_enhance_rss_feed' );

/**
 * Generate Google News Sitemap.
 *
 * Google News only indexes content from last 2 days.
 * Access URL: https://yoursite.com/?tempone_news_sitemap=1
 */
function tempone_google_news_sitemap() {
	if ( ! isset( $_GET['tempone_news_sitemap'] ) ) {
		return;
	}

	// Security: nonce not needed for public sitemap, but sanitize input.
	$generate = sanitize_text_field( wp_unslash( $_GET['tempone_news_sitemap'] ) );
	if ( '1' !== $generate ) {
		return;
	}

	// Get recent posts (last 2 days for Google News).
	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 1000,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'date_query'     => array(
				array(
					'after' => '2 days ago',
				),
			),
		)
	);

	// Set XML header.
	header( 'Content-Type: application/xml; charset=utf-8' );

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
			xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
	<?php foreach ( $posts as $post ) : setup_postdata( $post ); ?>
		<url>
			<loc><?php echo esc_url( get_permalink( $post->ID ) ); ?></loc>
			<news:news>
				<news:publication>
					<news:name><?php echo esc_xml( get_bloginfo( 'name' ) ); ?></news:name>
					<news:language><?php echo esc_xml( str_replace( '_', '-', get_locale() ) ); ?></news:language>
				</news:publication>
				<news:publication_date><?php echo esc_xml( get_the_date( 'c', $post ) ); ?></news:publication_date>
				<news:title><?php echo esc_xml( get_the_title( $post ) ); ?></news:title>
			</news:news>
		</url>
	<?php endforeach; ?>
	</urlset>
	<?php
	wp_reset_postdata();
	exit;
}
add_action( 'init', 'tempone_google_news_sitemap' );

/**
 * Get Google News Sitemap URL.
 *
 * @return string Sitemap URL.
 */
function tempone_get_news_sitemap_url() {
	return home_url( '/?tempone_news_sitemap=1' );
}

/**
 * Override Yoast SEO og:image to use optimized size with WebP support.
 *
 * Yoast by default uses full-size original image which is too large.
 * This filter uses smart loading: Large WebP → Large Original → Medium fallback.
 *
 * @param string $image_url Original image URL from Yoast.
 * @return string Optimized image URL.
 */
function tempone_yoast_og_image_size( $image_url ) {
	if ( ! is_single() || ! has_post_thumbnail() ) {
		return $image_url;
	}

	$attachment_id = get_post_thumbnail_id();
	if ( ! $attachment_id ) {
		return $image_url;
	}

	// Try large size first.
	$large_src = wp_get_attachment_image_src( $attachment_id, 'large' );

	if ( $large_src ) {
		$upload_dir = wp_upload_dir();

		// Check for WebP version first.
		$large_webp_url  = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $large_src[0] );
		$large_webp_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $large_webp_url );

		// Check for original JPG/PNG.
		$large_original_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $large_src[0] );

		// Use large if WebP or original exists.
		if ( file_exists( $large_webp_path ) ) {
			return $large_webp_url; // Return WebP version.
		} elseif ( file_exists( $large_original_path ) ) {
			return $large_src[0]; // Return original large.
		}
	}

	// Fallback to medium if large not available.
	$medium_src = wp_get_attachment_image_src( $attachment_id, 'medium' );
	if ( $medium_src ) {
		$upload_dir = wp_upload_dir();

		// Check for medium WebP.
		$medium_webp_url  = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $medium_src[0] );
		$medium_webp_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $medium_webp_url );

		if ( file_exists( $medium_webp_path ) ) {
			return $medium_webp_url;
		}

		return $medium_src[0];
	}

	// Last resort: return original.
	return $image_url;
}
add_filter( 'wpseo_opengraph_image', 'tempone_yoast_og_image_size' );
add_filter( 'wpseo_twitter_image', 'tempone_yoast_og_image_size' );
