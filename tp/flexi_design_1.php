<?php
/**
 * Flexible Layout: Design 1 - Hero + List.
 * Grid 2 kolom: 1 post besar (classic) + 4 posts list (image-side).
 *
 * ACF Fields:
 * - ane_category (Taxonomy: Category, Return: Term Object)
 * - ane_title (Text, optional custom title)
 *
 * @package tempone
 */

// Get category section data using helper function.
$data = tempone_get_category_section_data();

// Return early if no valid data.
if ( false === $data ) {
	return;
}

// Extract data.
$category      = $data['category'];
$section_title = $data['section_title'];
$category_link = $data['category_link'];

// Query 5 posts from selected category.
$query_args = array(
	'cat'            => $category->term_id,
	'posts_per_page' => 5,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => true,
);

$flexi_query = new WP_Query( $query_args );

if ( ! $flexi_query->have_posts() ) {
	return;
}
?>

<section class="flexi-design-1">
	<!-- Header: Title + Link -->
	<div class="flexi-design-1__header">
		<h2 class="flexi-design-1__title">
			<?php echo esc_html( $section_title ); ?>
		</h2>
		<a href="<?php echo esc_url( $category_link ); ?>" class="flexi-design-1__link">
			<?php esc_html_e( 'View More', 'tempone' ); ?>
			<span class="flexi-design-1__arrow">→</span>
		</a>
	</div>

	<!-- Content: Grid 2 Kolom -->
	<div class="flexi-design-1__grid">

		<!-- Left Column: 1 Post Classic -->
		<div class="flexi-design-1__featured">
			<?php
			if ( $flexi_query->have_posts() ) :
				$flexi_query->the_post();
				get_template_part( 'tp/content', 'classic' );
			endif;
			?>
		</div>

		<!-- Right Column: 4 Posts Image-Side -->
		<div class="flexi-design-1__list">
			<?php
			while ( $flexi_query->have_posts() ) :
				$flexi_query->the_post();
				get_template_part( 'tp/content', 'image-side' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>

	</div><!-- .flexi-design-1__grid -->
</section>
