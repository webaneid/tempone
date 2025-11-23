<?php
/**
 * Single sidebar partial.
 *
 * @package tempone
 */
?>
<aside class="space-y-6">
	<section>
		<h2 class="text-sm font-semibold uppercase tracking-wide mb-3"><?php esc_html_e( 'Latest Stories', 'tempone' ); ?></h2>
		<?php
		$query = new WP_Query(
			array(
				'post_type'      => 'post',
				'posts_per_page' => 5,
				'post__not_in'   => array( get_the_ID() ),
			)
		);
		if ( $query->have_posts() ) :
			echo '<ul class="space-y-3 text-sm">';
			while ( $query->have_posts() ) :
				$query->the_post();
				printf(
					'<li><a class="hover:underline" href="%s">%s</a></li>',
					esc_url( get_permalink() ),
					esc_html( get_the_title() )
				);
			endwhile;
			echo '</ul>';
			wp_reset_postdata();
		endif;
		?>
	</section>
</aside>
