<?php
/**
 * Top banner section.
 *
 * @package tempone
 */

$image = function_exists( 'get_sub_field' ) ? get_sub_field( 'image' ) : null;
$title = function_exists( 'get_sub_field' ) ? tempone_clean_text( get_sub_field( 'title' ) ) : __( 'Special Coverage', 'tempone' );
$link  = function_exists( 'get_sub_field' ) ? get_sub_field( 'link' ) : null;
?>
<section class="bg-gradient-to-r from-orange-500 to-pink-500 text-white" aria-label="<?php esc_attr_e( 'Top Banner', 'tempone' ); ?>">
	<div class="max-w-6xl mx-auto px-4 py-4 flex flex-col md:flex-row items-center gap-4">
		<div class="flex-1 space-y-2">
			<p class="text-xs uppercase tracking-[0.2em]"><?php esc_html_e( 'Campaign', 'tempone' ); ?></p>
			<h2 class="text-2xl font-semibold"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $link ) : ?>
				<a class="inline-flex items-center gap-2 text-sm font-semibold" href="<?php echo esc_url( $link['url'] ); ?>">
					<?php echo esc_html( $link['title'] ); ?>
					<span aria-hidden="true">→</span>
				</a>
			<?php endif; ?>
		</div>
		<?php if ( $image ) : ?>
			<img class="w-full max-w-sm rounded-lg shadow-lg" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy" decoding="async" />
		<?php endif; ?>
	</div>
</section>
