<?php
/**
 * Flexible Layout: Design 5 - Carousel Grid.
 * Horizontal carousel dengan 3 kolom posts (content-classic.php).
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

// Query 6 posts from selected category (2 slides x 3 posts).
$query_args = array(
	'cat'            => $category->term_id,
	'posts_per_page' => 6,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => true,
);

$flexi_query = new WP_Query( $query_args );

if ( ! $flexi_query->have_posts() ) {
	return;
}

// Generate unique ID for carousel.
$carousel_id = 'carousel-' . uniqid();
?>

<section class="flexi-design-5">
	<!-- Header: Title + Link -->
	<div class="flexi-design-5__header">
		<h2 class="flexi-design-5__title">
			<?php echo esc_html( $section_title ); ?>
		</h2>
		<a href="<?php echo esc_url( $category_link ); ?>" class="flexi-design-5__link">
			<?php esc_html_e( 'View More', 'tempone' ); ?>
			<span class="flexi-design-5__arrow">→</span>
		</a>
	</div>

	<!-- Carousel Container (Auto-sliding + Mouse Draggable) -->
	<div class="flexi-design-5__carousel">
		<!-- Carousel Track -->
		<div class="flexi-design-5__carousel-track" data-flexi-carousel>
			<?php
			while ( $flexi_query->have_posts() ) :
				$flexi_query->the_post();
				?>
				<div class="flexi-design-5__carousel-item">
					<?php get_template_part( 'tp/content', 'classic' ); ?>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
