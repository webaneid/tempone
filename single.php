<?php
/**
 * Single post template.
 *
 * @package tempone
 */

get_header();
?>

<main id="primary" class="container py-8">

	<div class="mb-6">
		<?php tempone_breadcrumbs(); ?>
	</div>

	<?php
	while ( have_posts() ) :
		the_post();

		// Increment post views.
		tempone_set_views();
		?>

		<div class="grid lg:grid-cols-[2fr_1fr] gap-8 lg:divide-x divide-gray-200">
			<!-- Main Content -->
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post lg:pr-8 min-w-0' ); ?> itemscope itemtype="https://schema.org/NewsArticle">

				<!-- Category & Reading Time -->
				<div class="single-post__category-wrapper mb-4">
					<?php tempone_post_category( null, 'single-post__category' ); ?>
					<span class="single-post__separator">•</span>
					<?php tempone_reading_time(); ?>
				</div>

				<!-- Title -->
				<h1 class="single-post__title mb-4" itemprop="headline"><?php the_title(); ?></h1>

				<!-- Headline (ACF) -->
				<?php
				$headline = get_field( 'ane_news_headline' );
				if ( $headline ) :
					?>
					<div class="single-post__headline mb-4" itemprop="description">
						<?php echo esc_html( $headline ); ?>
					</div>
				<?php endif; ?>

				<!-- Meta: Date -->
				<div class="single-post__meta mb-6">
					<?php
					// Timezone mapping for user-friendly display.
					$timezone_string = wp_timezone_string();

					// Map timezone names to abbreviations.
					$tz_map = array(
						'Asia/Jakarta'    => 'WIB',
						'Asia/Makassar'   => 'WITA',
						'Asia/Jayapura'   => 'WIT',
						'Asia/Pontianak'  => 'WIB',
						'Asia/Singapore'  => 'WIB',
					);

					// Map UTC offsets to Indonesian timezones.
					$utc_offset_map = array(
						'UTC+7'  => 'WIB',
						'+07:00' => 'WIB',
						'+7'     => 'WIB',
						'UTC+8'  => 'WITA',
						'+08:00' => 'WITA',
						'+8'     => 'WITA',
						'UTC+9'  => 'WIT',
						'+09:00' => 'WIT',
						'+9'     => 'WIT',
					);

					// Get timezone display.
					if ( isset( $tz_map[ $timezone_string ] ) ) {
						$tz_display = $tz_map[ $timezone_string ];
					} elseif ( isset( $utc_offset_map[ $timezone_string ] ) ) {
						$tz_display = $utc_offset_map[ $timezone_string ];
					} else {
						// Try to extract offset from string like "UTC+7" or "+07:00".
						if ( preg_match( '/[+\-]0?([789])/', $timezone_string, $matches ) ) {
							$offset_hour = (int) $matches[1];
							if ( 7 === $offset_hour ) {
								$tz_display = 'WIB';
							} elseif ( 8 === $offset_hour ) {
								$tz_display = 'WITA';
							} elseif ( 9 === $offset_hour ) {
								$tz_display = 'WIT';
							} else {
								$tz_display = $timezone_string;
							}
						} else {
							$tz_display = $timezone_string; // Fallback.
						}
					}

					// Date format: Monday, 19 January 2025 | 15.30 WIB
					$date_format = 'l, j F Y | H.i';
					$formatted_date = wp_date( $date_format, get_the_time( 'U' ) );
					?>
					<time class="single-post__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" itemprop="datePublished">
						<?php echo esc_html( $formatted_date . ' ' . $tz_display ); ?>
					</time>
				</div>

				<!-- Featured Image with Caption -->
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="single-post__featured-image-wrapper -mx-4 lg:mx-0 mb-6">
						<figure class="single-post__featured-image" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
							<?php
							$is_mobile_device = wp_is_mobile();
							$attachment_id    = get_post_thumbnail_id();

							// Determine optimal image size.
							if ( $is_mobile_device ) {
								// Mobile: always use medium for performance.
								$image_size = 'medium';
							} else {
								// Desktop: try large first, fallback to medium if not available.
								$large_src = wp_get_attachment_image_src( $attachment_id, 'large' );

								if ( $large_src ) {
									$upload_dir = wp_upload_dir();
									// Check for WebP version first.
									$large_webp_url  = preg_replace( '/\.(jpe?g|png|gif)$/i', '.webp', $large_src[0] );
									$large_webp_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $large_webp_url );

									// Check for original JPG/PNG.
									$large_original_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $large_src[0] );

									// Use large if WebP or original exists, otherwise fallback to medium.
									if ( file_exists( $large_webp_path ) || file_exists( $large_original_path ) ) {
										$image_size = 'large';
									} else {
										$image_size = 'medium';
									}
								} else {
									$image_size = 'medium';
								}
							}

							// Output image with determined size.
							tempone_post_thumbnail( $image_size, array(
								'class'    => 'w-full h-auto',
								'itemprop' => 'url',
							) );
							?>
							<?php
							$caption = get_the_post_thumbnail_caption();
							if ( $caption ) :
								?>
								<figcaption class="single-post__image-caption mt-2 px-4 lg:px-0" itemprop="caption">
									<?php echo esc_html( $caption ); ?>
								</figcaption>
							<?php endif; ?>
							<meta itemprop="width" content="960">
							<meta itemprop="height" content="540">
						</figure>
					</div>
				<?php endif; ?>

				<!-- Post Content -->
				<div class="single-post__content" itemprop="articleBody">
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'tempone' ),
							'after'  => '</div>',
						)
					);
					?>
				</div>

				<!-- Tags -->
				<?php
				$tags = get_the_tags();
				if ( $tags ) :
					?>
					<div class="single-post__tags mt-8 pt-6 border-t border-gray-200">
						<ul class="single-post__tags-list">
							<?php foreach ( $tags as $tag ) : ?>
								<li>
									<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="single-post__tag" rel="tag">
										#<?php echo esc_html( $tag->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<!-- Share Buttons -->
				<div class="single-post__share mt-8 pt-6 border-t border-gray-200">
					<?php tempone_share_buttons(); ?>
				</div>

				<!-- Author Info -->
				<div class="single-post__author mt-8 pt-6 border-t border-gray-200" itemprop="author" itemscope itemtype="https://schema.org/Person">
					<div class="single-post__author-wrapper">
						<div class="single-post__author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 80, '', '', array( 'class' => 'single-post__author-image' ) ); ?>
						</div>
						<div class="single-post__author-info">
							<div class="single-post__author-name" itemprop="name">
								<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
									<?php echo esc_html( get_the_author() ); ?>
								</a>
							</div>
							<?php if ( get_the_author_meta( 'description' ) ) : ?>
								<div class="single-post__author-bio" itemprop="description">
									<?php echo esc_html( get_the_author_meta( 'description' ) ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<!-- Comments Section (WordPress Native or Facebook Comments) -->
				<?php
				// If comments are open or there are comments, load the template.
				if ( comments_open() || get_comments_number() || tempone_is_facebook_comments_enabled() ) {
					comments_template();
				}
				?>

				<!-- Schema.org meta -->
				<meta itemprop="dateModified" content="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
				<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
					<meta itemprop="name" content="<?php echo esc_attr( tempone_get_company_name() ); ?>">
					<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
						<meta itemprop="url" content="<?php echo esc_url( get_site_icon_url() ); ?>">
					</div>
				</div>

			</article>

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

	<?php endwhile; ?>



	<!-- Single Footer: Related, Category, Newest Posts -->
	<?php $is_mobile = wp_is_mobile(); ?>
	<div class="single-footer">
		<div class="single-footer__grid">
			<!-- Related Posts -->
			<div class="single-footer__column">
				<h3 class="single-footer__title"><?php
				
				esc_html_e( 'Related Posts', 'tempone' );
				?></h3>
				<?php
				$related_query = tempone_get_related_posts( get_the_ID(), 6 );
				if ( $related_query->have_posts() ) :
					$related_counter = 0;
					while ( $related_query->have_posts() ) :
						$related_query->the_post();
						$related_counter++;

						if ( 1 === $related_counter ) {
							// First post: classic layout with mobile full-width
							if ( $is_mobile ) {
								echo '<div class="-mx-4 mb-4">';
								get_template_part( 'tp/content', 'classic' );
								echo '</div>';
							} else {
								get_template_part( 'tp/content', 'classic' );
							}
							echo '<hr class="single-footer__divider">';
						} else {
							// Posts 2-6: title only
							get_template_part( 'tp/content', 'title' );
						}
					endwhile;
					wp_reset_postdata();
				else :
					echo '<p class="single-footer__empty">' . esc_html__( 'No related posts found.', 'tempone' ) . '</p>';
				endif;
				?>
			</div>

			<!-- Category Posts -->
			<div class="single-footer__column">
				<?php
				$category = tempone_get_primary_category( get_the_ID() );
				$category_title = $category ? $category->name : __( 'Category', 'tempone' );
				?>
				<h3 class="single-footer__title"><?php echo esc_html( $category_title ); ?></h3>
				<?php
				$category_query = tempone_get_category_posts( get_the_ID(), 6 );
				if ( $category_query->have_posts() ) :
					$category_counter = 0;
					while ( $category_query->have_posts() ) :
						$category_query->the_post();
						$category_counter++;

						if ( 1 === $category_counter ) {
							if ( $is_mobile ) {
								echo '<div class="-mx-4 mb-4">';
								get_template_part( 'tp/content', 'classic' );
								echo '</div>';
							} else {
								get_template_part( 'tp/content', 'classic' );
							}
							echo '<hr class="single-footer__divider">';
						} else {
							get_template_part( 'tp/content', 'title' );
						}
					endwhile;
					wp_reset_postdata();
				else :
					echo '<p class="single-footer__empty">' . esc_html__( 'No posts found in this category.', 'tempone' ) . '</p>';
				endif;
				?>
			</div>

			<!-- Newest Posts -->
			<div class="single-footer__column">
				<h3 class="single-footer__title"><?php esc_html_e( 'Newest Posts', 'tempone' ); ?></h3>
				<?php
				$newest_query = tempone_get_newest_posts( 6 );
				if ( $newest_query->have_posts() ) :
					$newest_counter = 0;
					while ( $newest_query->have_posts() ) :
						$newest_query->the_post();
						$newest_counter++;

						if ( 1 === $newest_counter ) {
							if ( $is_mobile ) {
								echo '<div class="-mx-4 mb-4">';
								get_template_part( 'tp/content', 'classic' );
								echo '</div>';
							} else {
								get_template_part( 'tp/content', 'classic' );
							}
							echo '<hr class="single-footer__divider">';
						} else {
							get_template_part( 'tp/content', 'title' );
						}
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</div>
		</div>
	</div>

</main>

<?php
get_footer();
