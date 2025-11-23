<?php
/**
 * Article card.
 *
 * @package tempone
 */

$post_id = $post_id ?? get_the_ID();
?>
<article <?php post_class( 'group flex flex-col gap-3' ); ?> id="post-<?php the_ID(); ?>">
	<a href="<?php the_permalink(); ?>" class="block overflow-hidden rounded-lg">
		<?php
		if ( has_post_thumbnail( $post_id ) ) {
			echo get_the_post_thumbnail(
				$post_id,
				'tempone-card',
				array(
					'class'           => 'w-full object-cover aspect-video transition-transform duration-300 group-hover:scale-105',
					'loading'         => 'lazy',
					'decoding'        => 'async',
					'fetchpriority'   => 'low',
				)
			);
		}
		?>
	</a>
	<div class="space-y-2">
		<?php tempone_post_meta( $post_id ); ?>
		<h3 class="text-lg font-semibold leading-tight">
			<a class="hover:underline" href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</h3>
		<p class="text-sm text-gray-600"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></p>
	</div>
</article>
