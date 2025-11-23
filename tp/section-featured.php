<?php
/**
 * Template part: Featured posts section (reusable).
 *
 * Usage:
 * // Basic usage (default query, no pagination check)
 * get_template_part( 'tp/section', 'featured' );
 *
 * // With custom args
 * set_query_var( 'featured_args', array( 'cat' => 5 ) );
 * get_template_part( 'tp/section', 'featured' );
 *
 * // With pagination check (like index.php)
 * set_query_var( 'check_pagination', true );
 * get_template_part( 'tp/section', 'featured' );
 *
 * @package tempone
 */

// Get custom args if provided, otherwise use defaults.
$custom_args = get_query_var( 'featured_args', array() );
$check_pagination = get_query_var( 'check_pagination', false );

// Pagination check - featured only on page 1 (if enabled).
if ( $check_pagination ) {
	$paged_check = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	if ( 1 !== $paged_check ) {
		return; // Don't show featured on page 2+
	}
}

// Default featured args - persis seperti index.php.
$default_args = array(
	'post_type'      => 'post',
	'posts_per_page' => 7,
	'post_status'    => 'publish',
	'meta_key'       => 'ane_news_utama',
	'meta_value'     => '1',
	'orderby'        => 'date',
	'order'          => 'DESC',
);

// Merge custom args with defaults.
$featured_args = wp_parse_args( $custom_args, $default_args );

$featured_query = new WP_Query( $featured_args );

if ( ! $featured_query->have_posts() ) {
	return;
}

$is_mobile = wp_is_mobile();

// Mobile: Featured carousel OUTSIDE grid (full-width swipe).
if ( $is_mobile ) :
	?>
	<section class="featured-section-mobile mb-0">
		<div class="featured-mobile-carousel overflow-x-scroll -mx-4 px-4">
			<div class="flex gap-3 pb-4">
				<?php
				while ( $featured_query->have_posts() ) :
					$featured_query->the_post();
					?>
					<div class="flex-shrink-0 w-[90vw] max-w-md">
						<?php get_template_part( 'tp/content', 'overlay' ); ?>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
	return;
endif;

// Desktop: Featured section with carousel + title posts.
$featured_query->rewind_posts();
?>

<section class="featured-section mb-8">
	<!-- Desktop: Carousel 4 Posts -->
	<div class="featured-carousel mb-6">
		<div class="carousel-container overflow-hidden relative">
			<div class="carousel-track flex transition-transform duration-500 ease-in-out" id="featuredCarousel">
				<?php
				$slide_count = 0;
				while ( $featured_query->have_posts() && $slide_count < 4 ) :
					$featured_query->the_post();
					?>
					<div class="carousel-slide w-full flex-shrink-0">
						<?php get_template_part( 'tp/content', 'classic' ); ?>
					</div>
					<?php
					$slide_count++;
				endwhile;
				?>
			</div>

			<!-- Carousel Controls -->
			<button type="button" class="carousel-prev absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white p-2 rounded-full shadow-lg z-10 transition-colors">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
				</svg>
			</button>
			<button type="button" class="carousel-next absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white p-2 rounded-full shadow-lg z-10 transition-colors">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
				</svg>
			</button>

			<!-- Carousel Indicators -->
			<div class="carousel-indicators absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
				<?php for ( $i = 0; $i < 4; $i++ ) : ?>
					<button type="button" class="carousel-indicator w-2 h-2 rounded-full bg-white/60 hover:bg-white transition-colors <?php echo 0 === $i ? 'active bg-white' : ''; ?>" data-slide="<?php echo esc_attr( $i ); ?>"></button>
				<?php endfor; ?>
			</div>
		</div>
	</div>

	<!-- 3 Title Posts - Horizontal Grid -->
	<div class="featured-titles grid grid-cols-1 md:grid-cols-3 gap-4">
		<?php
		while ( $featured_query->have_posts() ) :
			$featured_query->the_post();
			get_template_part( 'tp/content', 'title' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
