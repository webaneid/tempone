<?php
/**
 * Archive template.
 *
 * @package tempone
 */

get_header();
?>

<main id="primary" class="container py-8">

	<div class="mb-6">
		<?php tempone_breadcrumbs(); ?>
	</div>

	<header class="page-header mb-8">
		<h1 class="text-3xl font-bold"><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="archive-description mt-2 text-gray-600">', '</div>' ); ?>
	</header>

	<?php
	// Mobile detection.
	$is_mobile = wp_is_mobile();

	// Pagination check.
	$paged_check = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$show_featured = ( 1 === $paged_check );

	// Build featured query args based on archive type.
	$featured_args = array(
		'post_type'      => 'post',
		'posts_per_page' => 7,
		'post_status'    => 'publish',
		'meta_key'       => 'ane_news_utama',
		'meta_value'     => '1',
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	// Filter featured posts by current archive context.
	if ( is_category() ) {
		$featured_args['cat'] = get_queried_object_id();
	} elseif ( is_tag() ) {
		$featured_args['tag_id'] = get_queried_object_id();
	} elseif ( is_author() ) {
		$featured_args['author'] = get_queried_object_id();
	} elseif ( is_date() ) {
		// Date archives: use year/month/day from query.
		if ( get_query_var( 'year' ) ) {
			$featured_args['year'] = get_query_var( 'year' );
		}
		if ( get_query_var( 'monthnum' ) ) {
			$featured_args['monthnum'] = get_query_var( 'monthnum' );
		}
		if ( get_query_var( 'day' ) ) {
			$featured_args['day'] = get_query_var( 'day' );
		}
	}

	$featured_query = new WP_Query( $featured_args );

	if ( $featured_query->have_posts() && $is_mobile && $show_featured ) :
		?>
		<!-- Mobile: Featured carousel -->
		<section class="featured-section-mobile mb-8 pb-8 border-b border-gray-200">
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
	<?php endif; ?>

	<div class="grid lg:grid-cols-[2fr_1fr] gap-8 lg:divide-x divide-gray-200">
		<!-- Main Content -->
		<div class="main-content lg:pr-8 min-w-0">

		<?php
		// Desktop: Featured section.
		if ( $featured_query->have_posts() && ! $is_mobile && $show_featured ) :
			$featured_query->rewind_posts();
			?>
			<section class="featured-section mb-8 pb-8 border-b border-gray-200">
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

						<div class="carousel-indicators absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
							<?php for ( $i = 0; $i < 4; $i++ ) : ?>
								<button type="button" class="carousel-indicator w-2 h-2 rounded-full bg-white/60 hover:bg-white transition-colors <?php echo 0 === $i ? 'active bg-white' : ''; ?>" data-slide="<?php echo $i; ?>"></button>
							<?php endfor; ?>
						</div>
					</div>
				</div>

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
		<?php endif; ?>

		<?php
		// Main Posts.
		if ( have_posts() ) :
			?>
			<div class="posts-list space-y-6">
				<?php
				$post_counter = 1;
				while ( have_posts() ) :
					the_post();

					// Post 6 dan 11 pakai overlay.
					if ( 6 === $post_counter || 11 === $post_counter ) {
						if ( $is_mobile ) {
							echo '<div class="-mx-4 mb-6">';
							get_template_part( 'tp/content', 'overlay' );
							echo '</div>';
						} else {
							get_template_part( 'tp/content', 'overlay' );
						}
					} elseif ( $is_mobile ) {
						get_template_part( 'tp/content', 'image-side' );
					} else {
						get_template_part( 'tp/content' );
					}

					$post_counter++;
				endwhile;
				?>
			</div>

			<?php
			global $wp_query;
			ane_post_pagination( $wp_query );
		else :
			?>
			<p><?php esc_html_e( 'No posts found in this archive.', 'tempone' ); ?></p>
		<?php endif; ?>

		</div>

		<!-- Sidebar -->
		<aside class="sidebar lg:pl-8">
			<?php if ( is_active_sidebar( 'sidebar-main' ) ) : ?>
				<?php dynamic_sidebar( 'sidebar-main' ); ?>
			<?php else : ?>
				<div class="sidebar-placeholder p-6 bg-gray-100 rounded-lg text-center">
					<p class="text-sm text-gray-600"><?php esc_html_e( 'No widgets in sidebar. Add from WP Admin.', 'tempone' ); ?></p>
				</div>
			<?php endif; ?>
		</aside>
	</div>

</main>

<?php
get_footer();
