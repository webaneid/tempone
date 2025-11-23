<?php
/**
 * Template helper functions.
 *
 * @package tempone
 */

/**
 * Print post meta (categories, date, author).
 */
function tempone_post_meta( int $post_id = 0 ) : void {
	$post_id = $post_id ? $post_id : get_the_ID();

	$categories = get_the_category( $post_id );
	echo '<div class="text-sm text-gray-500 flex flex-wrap gap-2">';

	if ( $categories ) {
		echo '<span class="flex gap-1">';
		foreach ( $categories as $category ) {
			printf(
				'<a class="uppercase tracking-wide font-semibold hover:underline" href="%s">%s</a>',
				esc_url( get_category_link( $category ) ),
				esc_html( $category->name )
			);
		}
		echo '</span>';
	}

	printf(
		'<span aria-label="%1$s">%1$s</span>',
		esc_html( get_the_date( '', $post_id ) )
	);

	echo '</div>';
}

/**
 * Get company name from ACF options or fallback to blogname.
 *
 * @return string Company name.
 */
function tempone_get_company_name() : string {
	// Try to get from ACF options first.
	if ( function_exists( 'get_field' ) ) {
		$company_name = get_field( 'ane_name', 'option' );
		if ( $company_name ) {
			return $company_name;
		}
	}

	// Fallback to blogname.
	return get_bloginfo( 'name' );
}

/**
 * Get category section data from ACF sub-fields.
 * Helper untuk Flexible Content dan Repeater sections.
 *
 * @return array|false Array dengan 'category', 'custom_title', 'section_title', 'category_link', atau false jika gagal.
 */
function tempone_get_category_section_data() {
	// Get ACF sub-fields.
	$category     = get_sub_field( 'ane_category' );
	$custom_title = get_sub_field( 'ane_title' );

	// Validate category.
	if ( ! $category || is_wp_error( $category ) ) {
		return false;
	}

	// Determine section title: custom title atau category name.
	$section_title = ! empty( $custom_title ) ? $custom_title : $category->name;
	$category_link = get_category_link( $category->term_id );

	return array(
		'category'      => $category,
		'custom_title'  => $custom_title,
		'section_title' => $section_title,
		'category_link' => $category_link,
	);
}

/**
 * Output accessible pagination.
 */
function tempone_pagination( WP_Query $query = null ) : void {
	$query = $query ?: $GLOBALS['wp_query'];
	$links = paginate_links(
		array(
			'total'   => $query->max_num_pages,
			'type'    => 'array',
			'current' => max( 1, get_query_var( 'paged' ) ),
		)
	);

	if ( empty( $links ) ) {
		return;
	}

	echo '<nav class="pagination my-8" aria-label="' . esc_attr__( 'Pagination', 'tempone' ) . '">';
		echo '<ul class="flex gap-2 justify-center">';
	foreach ( $links as $link ) {
		echo '<li class="px-3 py-1 border rounded">' . wp_kses_post( $link ) . '</li>';
	}
		echo '</ul>';
	echo '</nav>';
}

/**
 * Output Open Graph meta tags for social sharing.
 *
 * Used for Facebook, Twitter Card, and other social platforms.
 */
function tempone_open_graph_meta_tags() {
	// Only on single posts and pages.
	if ( ! is_singular() ) {
		return;
	}

	$post_id = get_the_ID();

	// Basic OG tags.
	$og_title       = get_the_title( $post_id );
	$og_description = get_the_excerpt( $post_id );
	$og_url         = get_permalink( $post_id );
	$og_type        = is_single() ? 'article' : 'website';
	$og_site_name   = get_bloginfo( 'name' );

	// Get featured image.
	$og_image = '';
	if ( has_post_thumbnail( $post_id ) ) {
		$image_id = get_post_thumbnail_id( $post_id );
		$image    = wp_get_attachment_image_src( $image_id, 'tempone-card-large' );
		if ( $image ) {
			$og_image        = $image[0];
			$og_image_width  = $image[1];
			$og_image_height = $image[2];
		}
	}

	// Fallback: site icon or default.
	if ( empty( $og_image ) ) {
		$og_image = get_site_icon_url( 512 );
	}

	// Limit description length.
	if ( strlen( $og_description ) > 200 ) {
		$og_description = substr( $og_description, 0, 200 ) . '...';
	}

	// Output Open Graph meta tags.
	?>
	<!-- Open Graph Meta Tags -->
	<meta property="og:title" content="<?php echo esc_attr( $og_title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $og_description ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $og_url ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $og_type ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( $og_site_name ); ?>">
	<?php if ( $og_image ) : ?>
	<meta property="og:image" content="<?php echo esc_url( $og_image ); ?>">
		<?php if ( isset( $og_image_width ) && isset( $og_image_height ) ) : ?>
	<meta property="og:image:width" content="<?php echo esc_attr( $og_image_width ); ?>">
	<meta property="og:image:height" content="<?php echo esc_attr( $og_image_height ); ?>">
		<?php endif; ?>
	<?php endif; ?>

	<!-- Twitter Card Meta Tags -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $og_title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $og_description ); ?>">
	<?php if ( $og_image ) : ?>
	<meta name="twitter:image" content="<?php echo esc_url( $og_image ); ?>">
	<?php endif; ?>

	<?php
	// Article-specific tags for single posts.
	if ( is_single() ) :
		$published_time = get_the_date( 'c', $post_id );
		$modified_time  = get_the_modified_date( 'c', $post_id );
		$author_id      = get_post_field( 'post_author', $post_id );
		$author_name    = get_the_author_meta( 'display_name', $author_id );

		// Categories as tags.
		$categories = get_the_category( $post_id );
		?>
	<meta property="article:published_time" content="<?php echo esc_attr( $published_time ); ?>">
	<meta property="article:modified_time" content="<?php echo esc_attr( $modified_time ); ?>">
	<meta property="article:author" content="<?php echo esc_attr( $author_name ); ?>">
		<?php
		if ( $categories ) :
			foreach ( $categories as $category ) :
				?>
	<meta property="article:section" content="<?php echo esc_attr( $category->name ); ?>">
				<?php
			endforeach;
		endif;

		// Tags.
		$tags = get_the_tags( $post_id );
		if ( $tags ) :
			foreach ( $tags as $tag ) :
				?>
	<meta property="article:tag" content="<?php echo esc_attr( $tag->name ); ?>">
				<?php
			endforeach;
		endif;
	endif;
}
