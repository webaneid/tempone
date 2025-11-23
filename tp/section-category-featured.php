<?php
/**
 * Template part: Category Featured section with auto-sliding carousel.
 *
 * Usage:
 * // Pass category and optional custom title via set_query_var
 * set_query_var( 'featured_category', $category_object );
 * set_query_var( 'featured_category_title', 'Custom Title' );
 * get_template_part( 'tp/section', 'category-featured' );
 *
 * @package tempone
 */

// Get category from query var (passed from ACF flexible content).
$featured_category = get_query_var( 'featured_category', null );
$custom_title = get_query_var( 'featured_category_title', '' );

// If no category provided, return early.
if ( ! $featured_category || is_wp_error( $featured_category ) ) {
	return;
}

// Determine title: custom title or category name.
$section_title = ! empty( $custom_title ) ? $custom_title : $featured_category->name;

// Query 8 latest posts from this category.
$category_query = new WP_Query(
	array(
		'cat'            => $featured_category->term_id,
		'posts_per_page' => 8,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	)
);

if ( ! $category_query->have_posts() ) {
	return;
}

$category_link = get_category_link( $featured_category->term_id );
$is_mobile = wp_is_mobile();
?>

<section class="section-category-featured">
	<!-- Header: Title + All Posts Button (inside container) -->
	<div class="container">
		<div class="section-category-featured__header">
			<h2 class="section-category-featured__title">
				<span class="section-category-featured__icon">E</span>
				<?php echo esc_html( $section_title ); ?>
			</h2>
			<a href="<?php echo esc_url( $category_link ); ?>" class="section-category-featured__all-posts">
				<?php esc_html_e( 'All Posts', 'tempone' ); ?>
			</a>
		</div>
	</div>

	<!-- Carousel: 8 Posts Auto-sliding -->
	<div class="section-category-featured__carousel-wrapper">
		<div class="container">
			<div class="section-category-featured__track" data-category-carousel>
				<?php
				while ( $category_query->have_posts() ) :
					$category_query->the_post();
					?>
					<div class="section-category-featured__slide">
						<?php get_template_part( 'tp/content', 'classic' ); ?>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</div>

	<!-- Footer: Explore Link (inside container) -->
	<div class="container">
		<div class="section-category-featured__footer">
			<a href="<?php echo esc_url( $category_link ); ?>" class="section-category-featured__explore-link">
				<?php esc_html_e( 'Explore All posts', 'tempone' ); ?>
				<span class="section-category-featured__arrow">→</span>
			</a>
		</div>
	</div>
</section>
