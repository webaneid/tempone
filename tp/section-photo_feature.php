<?php
/**
 * Photo feature grid.
 *
 * @package tempone
 */

$posts = function_exists( 'get_sub_field' ) ? (array) get_sub_field( 'posts' ) : array();
$title = function_exists( 'get_sub_field' ) ? tempone_clean_text( get_sub_field( 'section_title' ) ) : __( 'Photo Stories', 'tempone' );

if ( empty( $posts ) ) {
	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 6,
		)
	);
}
?>
<section class="py-12" aria-labelledby="<?php echo esc_attr( sanitize_title( $title ) ); ?>">
	<div class="max-w-6xl mx-auto px-4">
		<h2 id="<?php echo esc_attr( sanitize_title( $title ) ); ?>" class="text-2xl font-semibold mb-6">
			<?php echo esc_html( $title ); ?>
		</h2>
		<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			foreach ( $posts as $post ) :
				setup_postdata( $post );
				?>
				<article class="relative overflow-hidden rounded-xl">
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-64 object-cover' ) ); ?>
						<div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent p-4 flex items-end">
							<h3 class="text-white text-lg font-semibold"><?php the_title(); ?></h3>
						</div>
					</a>
				</article>
				<?php
			endforeach;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
