<?php
/**
 * Template part: Default post layout (thumbnail left, title right).
 *
 * @package tempone
 */
?>
<article <?php post_class( 'post-default' ); ?>>
	<div class="post-default__wrapper">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="post-default__thumbnail">
				<a href="<?php the_permalink(); ?>" class="post-default__link">
					<?php tempone_post_thumbnail( 'thumbnail', array( 'class' => 'post-default__image' ) ); ?>
				</a>
			</figure>
		<?php endif; ?>

		<div class="post-default__content">
			<div class="post-default__meta">
				<?php tempone_post_category( null, 'post-default__category' ); ?>
				<span>•</span>
				<?php tempone_post_time(); ?>
			</div>
			<h2 class="post-default__title">
				<a href="<?php the_permalink(); ?>" class="post-default__link"><?php the_title(); ?></a>
			</h2>
		</div>
	</div>
</article>
