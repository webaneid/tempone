<?php
/**
 * Hero main section.
 *
 * @package tempone
 */

$primary_post   = function_exists( 'get_sub_field' ) ? get_sub_field( 'primary_post' ) : null;
$secondary_post = function_exists( 'get_sub_field' ) ? get_sub_field( 'secondary_posts' ) : null;
?>
<section class="py-8 border-b border-gray-200" aria-labelledby="hero-heading">
	<div class="max-w-6xl mx-auto px-4 grid gap-8 md:grid-cols-3">
		<div class="md:col-span-2 space-y-4">
			<?php
			if ( $primary_post ) :
				$post = $primary_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				setup_postdata( $post );
				?>
				<h1 id="hero-heading" class="text-4xl font-bold leading-tight">
					<a class="hover:underline" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h1>
				<?php the_post_thumbnail( 'tempone-card-large', array( 'class' => 'rounded-xl w-full object-cover aspect-video' ) ); ?>
				<?php tempone_post_meta(); ?>
				<p class="text-lg text-gray-600"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php
				wp_reset_postdata();
			endif;
			?>
		</div>
		<div class="space-y-6">
			<?php
			if ( $secondary_post ) :
				foreach ( $secondary_post as $post ) :
					setup_postdata( $post );
					get_template_part( 'tp/loop', 'article-card' );
				endforeach;
				wp_reset_postdata();
			else :
				get_template_part( 'tp/section', 'latest' );
			endif;
			?>
		</div>
	</div>
</section>
