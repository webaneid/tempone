<?php
/**
 * Template part: Overlay layout (image background with gradient overlay).
 *
 * @package tempone
 */

$image_url = tempone_get_post_image( null, 'tempone-news-md' );
?>
<article <?php post_class( 'post-overlay' ); ?> <?php if ( $image_url ) : ?>style="background-image: url('<?php echo esc_url( $image_url ); ?>');"<?php endif; ?>>
	<div class="post-overlay__gradient"></div>
	<div class="post-overlay__content">
		<h3 class="post-overlay__title">
			<a href="<?php the_permalink(); ?>" class="post-overlay__link"><?php the_title(); ?></a>
		</h3>
		<div class="post-overlay__meta">
			<?php tempone_post_category( null, 'post-overlay__category' ); ?>
			<span class="post-overlay__separator">•</span>
			<?php tempone_post_time(); ?>
		</div>
	</div>
</article>
