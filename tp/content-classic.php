<?php
/**
 * Template part: Classic post layout (vertical - image top, title bottom).
 *
 * @package tempone
 */
?>
<article <?php post_class( 'post-classic' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="post-classic__thumbnail">
			<a href="<?php the_permalink(); ?>" class="post-classic__link">
				<?php tempone_post_thumbnail( 'medium', array( 'class' => 'post-classic__image' ) ); ?>
			</a>
		</figure>
	<?php endif; ?>

	<div class="post-classic__content">
		<div class="post-classic__meta">
			<?php tempone_post_category( null, 'post-classic__category' ); ?>
			<span class="post-classic__separator">•</span>
			<?php tempone_post_time(); ?>
		</div>
		<h3 class="post-classic__title">
			<a href="<?php the_permalink(); ?>" class="post-classic__link"><?php the_title(); ?></a>
		</h3>
	</div>
</article>
