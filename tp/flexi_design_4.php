<?php
/**
 * Flexible Layout: Design 4 - Horizontal Hero + Grid.
 * 1 post horizontal (content.php) di atas + 3 posts grid (classic) di bawah.
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

// Query 4 posts from selected category.
$query_args = array(
	'cat'            => $category->term_id,
	'posts_per_page' => 4,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => true,
);

$flexi_query = new WP_Query( $query_args );

if ( ! $flexi_query->have_posts() ) {
	return;
}

$is_mobile = wp_is_mobile();
?>

<section class="flexi-design-4">
	<!-- Header: Title + Link -->
	<div class="flexi-design-4__header">
		<h2 class="flexi-design-4__title">
			<?php echo esc_html( $section_title ); ?>
		</h2>
		<a href="<?php echo esc_url( $category_link ); ?>" class="flexi-design-4__link">
			<?php esc_html_e( 'More Articles', 'tempone' ); ?>
			<span class="flexi-design-4__arrow">→</span>
		</a>
	</div>

	<!-- Hero Post - Horizontal Layout (content.php) -->
	<div class="flexi-design-4__hero">
		<?php
		if ( $flexi_query->have_posts() ) :
			$flexi_query->the_post();

			if ( $is_mobile ) {
				get_template_part( 'tp/content', 'image-side' );
			} else {
				get_template_part( 'tp/content' );
			}
		endif;
		?>
	</div>

	<!-- Grid 3 Posts (content-classic.php) -->
	<div class="flexi-design-4__grid">
		<?php
		while ( $flexi_query->have_posts() ) :
			$flexi_query->the_post();

			if ( $is_mobile ) {
				get_template_part( 'tp/content', 'image-side' );
			} else {
				get_template_part( 'tp/content', 'classic' );
			}
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
