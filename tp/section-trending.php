<?php
/**
 * Template part: Trending posts section.
 *
 * Usage:
 * // Basic usage (default 5 trending posts)
 * get_template_part( 'tp/section', 'trending' );
 *
 * // With custom limit
 * set_query_var( 'trending_limit', 10 );
 * get_template_part( 'tp/section', 'trending' );
 *
 * @package tempone
 */

// Get custom limit if provided.
$trending_limit = get_query_var( 'trending_limit', 5 );

// Get trending posts.
$trending_query = tempone_get_trending_posts( $trending_limit );

if ( ! $trending_query->have_posts() ) {
	return;
}

$is_mobile = wp_is_mobile();
?>

<section class="section-trending">
	<div class="section-trending__grid">
		<!-- Main Content: Trending Posts -->
		<div class="section-trending__content">
			<h2 class="section-trending__title">
				<?php esc_html_e( 'Recommended for You', 'tempone' ); ?>
			</h2>

			<div class="section-trending__posts">
				<?php if ( $is_mobile ) : ?>
					<!-- Mobile: 2 Column Grid dengan 4 Classic Posts -->
					<div class="section-trending__mobile-grid">
						<?php
						$post_counter = 0;
						while ( $trending_query->have_posts() && $post_counter < 4 ) :
							$trending_query->the_post();
							get_template_part( 'tp/content', 'classic' );
							$post_counter++;
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				<?php else : ?>
					<!-- Desktop: 2 Column Layout - Left 4 posts, Right 1 post -->
					<div class="section-trending__posts-grid">
						<!-- Left Column: 4 Title Posts -->
						<div class="section-trending__titles">
							<?php
							$post_counter = 0;
							while ( $trending_query->have_posts() && $post_counter < 4 ) :
								$trending_query->the_post();
								get_template_part( 'tp/content', 'title' );
								$post_counter++;
							endwhile;
							?>
						</div>

						<!-- Right Column: 1 Classic Post -->
						<div class="section-trending__featured">
							<?php
							if ( $trending_query->have_posts() ) :
								$trending_query->the_post();
								get_template_part( 'tp/content', 'classic' );
							endif;

							wp_reset_postdata();
							?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Sidebar: Trending Tags -->
		<aside class="section-trending__sidebar">
			<h3 class="section-trending__sidebar-title">
				<?php esc_html_e( 'Trending Topics', 'tempone' ); ?>
			</h3>

			<?php
			$trending_tags = tempone_get_trending_tags( 10 );

			if ( ! empty( $trending_tags ) ) :
				?>
				<ul class="section-trending__tags">
					<?php foreach ( $trending_tags as $tag ) : ?>
						<li class="section-trending__tag-item">
							<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="section-trending__tag-link">
								#<?php echo esc_html( $tag->name ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php
			endif;
			?>
		</aside>
	</div>
</section>
