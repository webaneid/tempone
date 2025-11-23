<?php
/**
 * Default page template.
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
		?>

		<!-- Centered single column layout (same width as single.php main content) -->
		<div class="max-w-[66.666%] mx-auto">
			<article id="page-<?php the_ID(); ?>" <?php post_class( 'single-post min-w-0' ); ?>>

				<!-- Title -->
				<h1 class="single-post__title mb-4"><?php the_title(); ?></h1>

				<!-- Featured Image with Caption -->
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="single-post__featured-image-wrapper -mx-4 lg:mx-0 mb-6">
						<figure class="single-post__featured-image">
							<?php
							the_post_thumbnail( 'tempone-card-large', array(
								'class' => 'w-full h-auto',
							) );
							?>
							<?php
							$caption = get_the_post_thumbnail_caption();
							if ( $caption ) :
								?>
								<figcaption class="single-post__image-caption mt-2 px-4 lg:px-0">
									<?php echo esc_html( $caption ); ?>
								</figcaption>
							<?php endif; ?>
						</figure>
					</div>
				<?php endif; ?>

				<!-- Page Content -->
				<div class="single-post__content">
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

			</article>
		</div>

	<?php endwhile; ?>

</main>

<?php
get_footer();
