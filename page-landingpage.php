<?php
/**
 * Template Name: Landing Page
 *
 * @package tempone
 */

get_header();

$is_mobile = wp_is_mobile();
?>

<main id="primary" class="container py-8">

	<!-- Section: Featured + Sidebar -->
	<section class="section-featured-sidebar">
			<?php
			// Mobile: Featured section OUTSIDE grid.
			if ( $is_mobile ) {
				get_template_part( 'tp/section', 'featured' );
			}
			?>

			<div class="grid lg:grid-cols-[2fr_1fr] gap-8 lg:divide-x divide-gray-200">
				<!-- Main Content -->
				<div class="main-content lg:pr-8 min-w-0">
					<?php
					// Desktop: Featured section INSIDE grid.
					if ( ! $is_mobile ) {
						get_template_part( 'tp/section', 'featured' );
					}
					?>
				</div>

				<!-- Sidebar -->
				<aside class="sidebar lg:pl-8">
					<h3 class="text-lg font-bold uppercase mb-4 pb-2 border-b border-gray-200">
						<?php esc_html_e( 'Latest Articles', 'tempone' ); ?>
					</h3>

					<?php
					$newest_query = tempone_get_newest_posts( 6 );
					if ( $newest_query->have_posts() ) :
						while ( $newest_query->have_posts() ) :
							$newest_query->the_post();
							get_template_part( 'tp/content', 'image-side' );
						endwhile;
						wp_reset_postdata();
					endif;
					?>
				</aside>
			</div>
	</section>

	<!-- Section: Trending -->
	<section class="section-trending-wrapper">
		<?php get_template_part( 'tp/section', 'trending' ); ?>
	</section>

</main>

<!-- Section: Category Featured (Full-width black background - outside main container) -->
<?php
$category_featured_group = get_field( 'ane_category_featured', get_the_ID() );

if ( $category_featured_group ) {
	$featured_category = $category_featured_group['ane_category'] ?? null;
	$custom_title = $category_featured_group['ane_title'] ?? '';

	if ( $featured_category && ! is_wp_error( $featured_category ) ) {
		set_query_var( 'featured_category', $featured_category );
		if ( ! empty( $custom_title ) ) {
			set_query_var( 'featured_category_title', $custom_title );
		}
		get_template_part( 'tp/section', 'category-featured' );
	}
}
?>

<!-- Section: Flexible Content + Sidebar (ACF Flexible Layouts) -->
<?php get_template_part( 'tp/section', 'flexible' ); ?>

<!-- Section: Category Columns (ACF Repeater - 4 Kolom Grid) -->
<?php get_template_part( 'tp/section', 'category-columns' ); ?>

<?php
get_footer();
