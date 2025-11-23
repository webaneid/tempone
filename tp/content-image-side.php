<?php
/**
 * Template part: Square image left, title right (sidebar style).
 *
 * @package tempone
 */
?>
<article <?php post_class( 'post-image-side' ); ?>>
	<div class="post-image-side__wrapper">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="post-image-side__thumbnail">
				<a href="<?php the_permalink(); ?>" class="post-image-side__link">
					<?php tempone_post_thumbnail( 'tempone-square-sm', array( 'class' => 'post-image-side__image' ) ); ?>
				</a>
			</figure>
		<?php endif; ?>

		<div class="post-image-side__content">
			<h3 class="post-image-side__title">
				<a href="<?php the_permalink(); ?>" class="post-image-side__link"><?php the_title(); ?></a>
			</h3>
			<?php tempone_post_time(); ?>
		</div>
	</div>
</article>
