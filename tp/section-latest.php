<?php
/**
 * Latest posts fallback section.
 *
 * @package tempone
 */

$section_title = function_exists( 'get_sub_field' ) ? get_sub_field( 'section_title' ) : __( 'Latest', 'tempone' );
$posts_per     = function_exists( 'get_sub_field' ) ? (int) get_sub_field( 'posts_per_page' ) : 4;

$query = new WP_Query(
	tempone_section_query_args(
		array(
			'posts_per_page' => $posts_per ?: 4,
		)
	)
);
?>
<section class="py-10" aria-labelledby="<?php echo esc_attr( sanitize_title( $section_title ) ); ?>">
	<div class="max-w-6xl mx-auto px-4">
		<div class="flex items-center justify-between mb-6">
			<h2 id="<?php echo esc_attr( sanitize_title( $section_title ) ); ?>" class="text-2xl font-semibold">
				<?php echo esc_html( $section_title ); ?>
			</h2>
			<a class="text-sm uppercase tracking-wide hover:underline" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>">
				<?php esc_html_e( 'View all', 'tempone' ); ?>
			</a>
		</div>
		<div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
			<?php
			if ( $query->have_posts() ) :
				while ( $query->have_posts() ) :
					$query->the_post();
					get_template_part( 'tp/loop', 'article-card' );
				endwhile;
				wp_reset_postdata();
			else :
				echo '<p class="text-gray-500">' . esc_html__( 'No posts available yet.', 'tempone' ) . '</p>';
			endif;
			?>
		</div>
	</div>
</section>
