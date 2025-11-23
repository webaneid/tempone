<?php
/**
 * Post utilities: pagination, breadcrumbs, helpers.
 *
 * @package tempone
 */

/**
 * Accessible pagination styled for Tempone.
 *
 * @param WP_Query|null $wp_query Query to paginate.
 * @param bool          $echo     Output or return markup.
 *
 * @return string|null
 */
function ane_post_pagination( ?WP_Query $wp_query = null, bool $echo = true ) {
	$wp_query = $wp_query ?: $GLOBALS['wp_query'] ?? null;

	if ( ! $wp_query || $wp_query->max_num_pages <= 1 ) {
		return null;
	}

	$links = paginate_links(
		array(
			'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
			'format'    => '?paged=%#%',
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'total'     => (int) $wp_query->max_num_pages,
			'type'      => 'array',
			'end_size'  => 1,
			'mid_size'  => 2,
			'prev_next' => true,
			'prev_text' => '<span class="ane-pagination__icon" aria-hidden="true">&lsaquo;</span><span class="screen-reader-text">' . esc_html__( 'Previous', 'tempone' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'tempone' ) . '</span><span class="ane-pagination__icon" aria-hidden="true">&rsaquo;</span>',
		)
	);

	if ( empty( $links ) ) {
		return null;
	}

	$markup = '<nav class="ane-pagination" aria-label="' . esc_attr__( 'Pagination', 'tempone' ) . '"><ul class="ane-pagination__list">';
	foreach ( $links as $link ) {
		$class = strpos( $link, 'current' ) !== false ? ' class="is-active"' : '';
		$markup .= '<li' . $class . '>' . $link . '</li>';
	}
	$markup .= '</ul></nav>';

	if ( $echo ) {
		echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return null;
	}

	return $markup;
}

/**
 * Render breadcrumb trail with Yoast SEO support.
 *
 * @param array $args Override defaults.
 */
function tempone_breadcrumbs( array $args = array() ) : void {
	$defaults = array(
		'wrap_before' => '<nav class="ane-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'tempone' ) . '"><ol>',
		'wrap_after'  => '</ol></nav>',
		'separator'   => '<span class="ane-breadcrumbs__sep" aria-hidden="true">/</span>',
	);
	$args     = wp_parse_args( $args, $defaults );

	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( $args['wrap_before'], $args['wrap_after'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	$items = array();
	$home_icon = '<svg class="ane-breadcrumbs__home-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
	$items[] = sprintf(
		'<li><a href="%s" aria-label="%s">%s<span>%s</span></a></li>',
		esc_url( home_url( '/' ) ),
		esc_attr__( 'Go to homepage', 'tempone' ),
		$home_icon,
		esc_html__( 'Home', 'tempone' )
	);

	if ( is_singular( 'post' ) ) {
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$primary = $categories[0];
			$items[] = sprintf(
				'<li><a href="%s">%s</a></li>',
				esc_url( get_category_link( $primary ) ),
				esc_html( $primary->name )
			);
		}
		$items[] = sprintf( '<li class="breadcrumb-title"><span>%s</span></li>', esc_html( get_the_title() ) );
	} elseif ( is_page() ) {
		if ( $parents = get_post_ancestors( get_the_ID() ) ) { // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
			foreach ( array_reverse( $parents ) as $parent_id ) {
				$items[] = sprintf(
					'<li><a href="%s">%s</a></li>',
					esc_url( get_permalink( $parent_id ) ),
					esc_html( get_the_title( $parent_id ) )
				);
			}
		}
		$items[] = sprintf( '<li><span>%s</span></li>', esc_html( get_the_title() ) );
	} elseif ( is_category() ) {
		$items[] = sprintf( '<li><span>%s</span></li>', esc_html( single_cat_title( '', false ) ) );
	} elseif ( is_tag() ) {
		$items[] = sprintf( '<li><span>%s</span></li>', esc_html( single_tag_title( '', false ) ) );
	} elseif ( is_search() ) {
		$items[] = sprintf( '<li><span>%s</span></li>', esc_html( get_search_query() ) );
	} elseif ( is_author() ) {
		$items[] = sprintf( '<li><span>%s</span></li>', esc_html( get_the_author() ) );
	} elseif ( is_post_type_archive() ) {
		$items[] = sprintf( '<li><span>%s</span></li>', esc_html( post_type_archive_title( '', false ) ) );
	}

	echo $args['wrap_before'] . wp_kses_post( implode( $args['separator'], $items ) ) . $args['wrap_after']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Get primary category for a post.
 *
 * @param int|null $post_id Post ID.
 * @return WP_Term|null Category term object or null.
 */
function tempone_get_primary_category( ?int $post_id = null ) : ?WP_Term {
	$post_id = $post_id ?: get_the_ID();

	// Try Yoast primary category first.
	if ( class_exists( 'WPSEO_Primary_Term' ) ) {
		$primary_term = new WPSEO_Primary_Term( 'category', $post_id );
		$primary_id   = $primary_term->get_primary_term();

		if ( $primary_id ) {
			$term = get_term( $primary_id, 'category' );
			if ( $term && ! is_wp_error( $term ) ) {
				return $term;
			}
		}
	}

	// Fallback to first category.
	$categories = get_the_category( $post_id );
	return ! empty( $categories ) ? $categories[0] : null;
}

/**
 * Display post category link.
 *
 * @param int|null $post_id Post ID.
 * @param string   $class   Additional CSS class.
 */
function tempone_post_category( ?int $post_id = null, string $class = '' ) : void {
	$category = tempone_get_primary_category( $post_id );

	if ( ! $category ) {
		return;
	}

	printf(
		'<a href="%s" class="post-category %s" rel="category tag">%s</a>',
		esc_url( get_category_link( $category ) ),
		esc_attr( $class ),
		esc_html( $category->name )
	);
}

/**
 * Get relative time (e.g., "2 hours ago", "1 week ago").
 *
 * @param string|int|null $time Unix timestamp or date string.
 * @return string Human-readable time difference.
 */
function tempone_relative_time( string|int|null $time = null ) : string {
	$time = $time ?: get_the_time( 'U' );

	if ( is_string( $time ) ) {
		$time = strtotime( $time );
	}

	$diff = time() - $time;

	if ( $diff < MINUTE_IN_SECONDS ) {
		return __( 'Just now', 'tempone' );
	}

	if ( $diff < HOUR_IN_SECONDS ) {
		$minutes = floor( $diff / MINUTE_IN_SECONDS );
		return sprintf(
			/* translators: %s: Number of minutes */
			_n( '%s minute ago', '%s minutes ago', $minutes, 'tempone' ),
			number_format_i18n( $minutes )
		);
	}

	if ( $diff < DAY_IN_SECONDS ) {
		$hours = floor( $diff / HOUR_IN_SECONDS );
		return sprintf(
			/* translators: %s: Number of hours */
			_n( '%s hour ago', '%s hours ago', $hours, 'tempone' ),
			number_format_i18n( $hours )
		);
	}

	if ( $diff < WEEK_IN_SECONDS ) {
		$days = floor( $diff / DAY_IN_SECONDS );
		return sprintf(
			/* translators: %s: Number of days */
			_n( '%s day ago', '%s days ago', $days, 'tempone' ),
			number_format_i18n( $days )
		);
	}

	if ( $diff < MONTH_IN_SECONDS ) {
		$weeks = floor( $diff / WEEK_IN_SECONDS );
		return sprintf(
			/* translators: %s: Number of weeks */
			_n( '%s week ago', '%s weeks ago', $weeks, 'tempone' ),
			number_format_i18n( $weeks )
		);
	}

	if ( $diff < YEAR_IN_SECONDS ) {
		$months = floor( $diff / MONTH_IN_SECONDS );
		return sprintf(
			/* translators: %s: Number of months */
			_n( '%s month ago', '%s months ago', $months, 'tempone' ),
			number_format_i18n( $months )
		);
	}

	$years = floor( $diff / YEAR_IN_SECONDS );
	return sprintf(
		/* translators: %s: Number of years */
		_n( '%s year ago', '%s years ago', $years, 'tempone' ),
		number_format_i18n( $years )
	);
}

/**
 * Display relative time.
 *
 * @param string|int|null $time Unix timestamp or date string.
 */
function tempone_post_time( string|int|null $time = null ) : void {
	$time = $time ?: get_the_time( 'U' );
	$datetime = is_string( $time ) ? $time : gmdate( 'c', $time );

	printf(
		'<time class="post-time" datetime="%s">%s</time>',
		esc_attr( $datetime ),
		esc_html( tempone_relative_time( $time ) )
	);
}

/**
 * Get featured image with fallback.
 *
 * @param int|null    $post_id Post ID.
 * @param string      $size    Image size.
 * @param string|null $fallback_url Fallback image URL.
 * @return string|null Image URL or null.
 */
function tempone_get_post_image( ?int $post_id = null, string $size = 'medium', ?string $fallback_url = null ) : ?string {
	$post_id = $post_id ?: get_the_ID();

	if ( has_post_thumbnail( $post_id ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
		return $image ? $image[0] : $fallback_url;
	}

	return $fallback_url;
}

/**
 * Display post thumbnail with lazy loading and fallback.
 *
 * @param string      $size         Image size.
 * @param array       $attr         Additional attributes.
 * @param string|null $fallback_url Fallback image URL.
 */
function tempone_post_thumbnail( string $size = 'medium', array $attr = array(), ?string $fallback_url = null ) : void {
	$defaults = array(
		'loading' => 'lazy',
		'class'   => 'post-thumbnail',
		'alt'     => get_the_title(),
	);

	$attr = wp_parse_args( $attr, $defaults );

	if ( has_post_thumbnail() ) {
		$attachment_id = get_post_thumbnail_id();
		$image_src     = wp_get_attachment_image_src( $attachment_id, $size );

		if ( ! $image_src ) {
			return;
		}

		// Check if WebP version exists.
		$original_url = $image_src[0];
		$webp_url     = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $original_url );
		$upload_dir   = wp_upload_dir();
		$webp_path    = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $webp_url );

		// Use WebP if available, otherwise use original.
		$final_url = ( file_exists( $webp_path ) && $webp_url !== $original_url ) ? $webp_url : $original_url;

		// Get image metadata.
		$image_meta = wp_get_attachment_metadata( $attachment_id );
		$width      = $image_src[1] ?? ( $image_meta['width'] ?? '' );
		$height     = $image_src[2] ?? ( $image_meta['height'] ?? '' );

		// Get srcset and sizes for responsive images.
		$srcset = wp_get_attachment_image_srcset( $attachment_id, $size );
		$sizes  = wp_get_attachment_image_sizes( $attachment_id, $size );

		// Convert srcset URLs to WebP if available.
		if ( $srcset ) {
			$srcset_array = explode( ', ', $srcset );
			$webp_srcset  = array();

			foreach ( $srcset_array as $srcset_item ) {
				$parts = explode( ' ', $srcset_item, 2 );
				if ( count( $parts ) === 2 ) {
					list( $url, $descriptor ) = $parts;

					// Check if WebP version exists for this size.
					$srcset_webp_url  = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $url );
					$srcset_webp_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $srcset_webp_url );

					// Use WebP if exists, otherwise keep original.
					$final_srcset_url = ( file_exists( $srcset_webp_path ) && $srcset_webp_url !== $url ) ? $srcset_webp_url : $url;
					$webp_srcset[] = $final_srcset_url . ' ' . $descriptor;
				}
			}

			$srcset = ! empty( $webp_srcset ) ? implode( ', ', $webp_srcset ) : $srcset;
		}

		// Build attributes.
		$attr_string = '';
		foreach ( $attr as $key => $value ) {
			$attr_string .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		// Output image with WebP support.
		printf(
			'<img src="%s"%s%s%s%s%s>',
			esc_url( $final_url ),
			$width ? ' width="' . esc_attr( $width ) . '"' : '',
			$height ? ' height="' . esc_attr( $height ) . '"' : '',
			$srcset ? ' srcset="' . esc_attr( $srcset ) . '"' : '',
			$sizes ? ' sizes="' . esc_attr( $sizes ) . '"' : '',
			$attr_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	} elseif ( $fallback_url ) {
		printf(
			'<img src="%s" alt="%s" class="%s" loading="%s">',
			esc_url( $fallback_url ),
			esc_attr( $attr['alt'] ),
			esc_attr( $attr['class'] ),
			esc_attr( $attr['loading'] )
		);
	}
}

/**
 * Get custom excerpt with word limit.
 *
 * @param int      $word_limit Word limit.
 * @param int|null $post_id    Post ID.
 * @return string Excerpt text.
 */
function tempone_get_excerpt( int $word_limit = 20, ?int $post_id = null ) : string {
	$post_id = $post_id ?: get_the_ID();
	$excerpt = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : get_the_content( null, false, $post_id );

	// Strip tags and shortcodes.
	$excerpt = strip_tags( strip_shortcodes( $excerpt ) );

	// Limit words.
	$words = explode( ' ', $excerpt, $word_limit + 1 );

	if ( count( $words ) > $word_limit ) {
		array_pop( $words );
		$excerpt = implode( ' ', $words ) . '&hellip;';
	} else {
		$excerpt = implode( ' ', $words );
	}

	return $excerpt;
}

/**
 * Display post excerpt.
 *
 * @param int      $word_limit Word limit.
 * @param int|null $post_id    Post ID.
 */
function tempone_post_excerpt( int $word_limit = 20, ?int $post_id = null ) : void {
	echo '<p class="post-excerpt">' . esc_html( tempone_get_excerpt( $word_limit, $post_id ) ) . '</p>';
}

/**
 * Get estimated reading time for post.
 *
 * @param int|null $post_id Post ID.
 * @return string Reading time text (e.g., "5 min read").
 */
function tempone_get_reading_time( ?int $post_id = null ) : string {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}

	$content = wp_strip_all_tags( $post->post_content );
	$word_count = str_word_count( $content );

	// Average reading speed: 200 words per minute.
	$reading_time = ceil( $word_count / 200 );

	if ( $reading_time < 1 ) {
		$reading_time = 1;
	}

	return sprintf(
		/* translators: %d: Number of minutes */
		_n( '%d min read', '%d mins read', $reading_time, 'tempone' ),
		$reading_time
	);
}

/**
 * Display reading time.
 *
 * @param int|null $post_id Post ID.
 */
function tempone_reading_time( ?int $post_id = null ) : void {
	$reading_time = tempone_get_reading_time( $post_id );
	if ( $reading_time ) {
		echo '<span class="reading-time">' . esc_html( $reading_time ) . '</span>';
	}
}

/**
 * Get related posts by tags.
 *
 * @param int|null $post_id Post ID.
 * @param int      $limit   Number of posts to retrieve.
 * @return WP_Query Query object with related posts.
 */
function tempone_get_related_posts( ?int $post_id = null, int $limit = 6 ) : WP_Query {
	$post_id = $post_id ?: get_the_ID();
	$tags = wp_get_post_tags( $post_id );

	$args = array(
		'posts_per_page' => $limit,
		'post__not_in'   => array( $post_id ),
		'orderby'        => 'rand',
		'no_found_rows'  => true,
	);

	if ( ! empty( $tags ) ) {
		$tag_ids = wp_list_pluck( $tags, 'term_id' );
		$args['tag__in'] = $tag_ids;
	} else {
		// Fallback: get latest posts if no tags.
		$args['orderby'] = 'date';
	}

	return new WP_Query( $args );
}

/**
 * Get posts from the same category.
 *
 * @param int|null $post_id Post ID.
 * @param int      $limit   Number of posts to retrieve.
 * @return WP_Query Query object with category posts.
 */
function tempone_get_category_posts( ?int $post_id = null, int $limit = 6 ) : WP_Query {
	$post_id = $post_id ?: get_the_ID();
	$category = tempone_get_primary_category( $post_id );

	$args = array(
		'posts_per_page' => $limit,
		'post__not_in'   => array( $post_id ),
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	);

	if ( $category ) {
		$args['cat'] = $category->term_id;
	}

	return new WP_Query( $args );
}

/**
 * Get newest posts from all categories.
 *
 * @param int $limit Number of posts to retrieve.
 * @return WP_Query Query object with newest posts.
 */
function tempone_get_newest_posts( int $limit = 6 ) : WP_Query {
	$args = array(
		'posts_per_page' => $limit,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	);

	return new WP_Query( $args );
}

/**
 * Get post views count.
 *
 * @param int|null $post_id Post ID.
 * @return string Formatted view count (e.g., "123 views").
 */
function tempone_get_views( ?int $post_id = null ) : string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id ) {
		return '';
	}

	$count_key = 'tempone_views';
	$count = get_post_meta( $post_id, $count_key, true );

	if ( '' === $count ) {
		delete_post_meta( $post_id, $count_key );
		add_post_meta( $post_id, $count_key, '0' );
		$count = 0;
	}

	$count = absint( $count );

	return sprintf(
		/* translators: %s: Number of views */
		_n( '%s view', '%s views', $count, 'tempone' ),
		number_format_i18n( $count )
	);
}

/**
 * Increment post views count.
 *
 * @param int|null $post_id Post ID.
 * @return bool True on success, false on failure.
 */
function tempone_set_views( ?int $post_id = null ) : bool {
	// Don't count views if cache is enabled (will use AJAX instead).
	if ( defined( 'WP_CACHE' ) && WP_CACHE ) {
		return false;
	}

	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id ) {
		return false;
	}

	$count_key = 'tempone_views';
	$count = get_post_meta( $post_id, $count_key, true );

	if ( '' === $count ) {
		$count = 0;
		delete_post_meta( $post_id, $count_key );
		add_post_meta( $post_id, $count_key, '0' );
	} else {
		$count = absint( $count ) + 1;
		update_post_meta( $post_id, $count_key, $count );
	}

	return true;
}

/**
 * Get trending posts by view count.
 *
 * @param int $limit Number of posts to retrieve.
 * @return WP_Query Query object with trending posts.
 */
function tempone_get_trending_posts( int $limit = 6 ) : WP_Query {
	$args = array(
		'posts_per_page' => $limit,
		'meta_key'       => 'tempone_views',
		'orderby'        => 'meta_value_num',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	);

	return new WP_Query( $args );
}

/**
 * Get trending tags ordered by total views from all posts with each tag.
 *
 * @param int $limit Number of tags to return.
 *
 * @return array Array of tag objects with total_views property.
 */
function tempone_get_trending_tags( int $limit = 10 ) : array {
	global $wpdb;

	// Query to get tags with sum of post views.
	$results = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT t.term_id, t.name, t.slug, SUM(CAST(pm.meta_value AS UNSIGNED)) as total_views
			FROM {$wpdb->terms} t
			INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
			INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
			INNER JOIN {$wpdb->postmeta} pm ON tr.object_id = pm.post_id
			WHERE tt.taxonomy = 'post_tag'
			AND pm.meta_key = 'tempone_views'
			AND pm.meta_value != ''
			GROUP BY t.term_id
			ORDER BY total_views DESC
			LIMIT %d",
			$limit
		)
	);

	if ( empty( $results ) ) {
		return array();
	}

	// Convert to proper tag objects with total_views.
	$trending_tags = array();
	foreach ( $results as $result ) {
		$tag = get_tag( $result->term_id );
		if ( $tag && ! is_wp_error( $tag ) ) {
			$tag->total_views = (int) $result->total_views;
			$trending_tags[] = $tag;
		}
	}

	return $trending_tags;
}

// Remove issues with prefetching adding extra views.
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

/**
 * Add Views column to posts admin.
 *
 * @param array $defaults Column headers.
 * @return array Modified column headers.
 */
function tempone_column_views( array $defaults ) : array {
	$defaults['tempone_post_views'] = __( 'Views', 'tempone' );
	return $defaults;
}
add_filter( 'manage_posts_columns', 'tempone_column_views' );

/**
 * Display views count in admin column.
 *
 * @param string $column_name Column name.
 * @param int    $post_id     Post ID.
 */
function tempone_custom_column_views( string $column_name, int $post_id ) : void {
	if ( 'tempone_post_views' === $column_name ) {
		echo esc_html( tempone_get_views( $post_id ) );
	}
}
add_action( 'manage_posts_custom_column', 'tempone_custom_column_views', 5, 2 );

/**
 * Enqueue post views AJAX script for cached sites.
 */
function tempone_postview_cache_enqueue() : void {
	if ( ! is_single() ) {
		return;
	}

	if ( ! ( defined( 'WP_CACHE' ) && WP_CACHE ) ) {
		return;
	}

	wp_enqueue_script(
		'tempone-postviews-cache',
		TEMPONE_URI . '/js/postviews-cache.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);

	wp_localize_script(
		'tempone-postviews-cache',
		'temponePostViews',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
			'post_id'  => get_the_ID(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'tempone_postview_cache_enqueue' );

/**
 * AJAX handler to increment views for cached sites.
 */
function tempone_increment_views_ajax() : void {
	if ( empty( $_GET['postviews_id'] ) ) {
		wp_die();
	}

	if ( ! ( defined( 'WP_CACHE' ) && WP_CACHE ) ) {
		wp_die();
	}

	$post_id = absint( $_GET['postviews_id'] );

	if ( $post_id <= 0 ) {
		wp_die();
	}

	$count_key = 'tempone_views';
	$count = get_post_meta( $post_id, $count_key, true );

	if ( '' === $count ) {
		$count = 0;
		delete_post_meta( $post_id, $count_key );
		add_post_meta( $post_id, $count_key, '0' );
	} else {
		$count = absint( $count ) + 1;
		update_post_meta( $post_id, $count_key, $count );
	}

	wp_die();
}
add_action( 'wp_ajax_postviews', 'tempone_increment_views_ajax' );
add_action( 'wp_ajax_nopriv_postviews', 'tempone_increment_views_ajax' );
