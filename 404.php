<?php
/**
 * 404 template.
 *
 * @package tempone
 */

get_header();
?>
<main id="primary" class="min-h-[50vh] flex flex-col items-center justify-center text-center px-4 py-20">
	<p class="text-sm uppercase tracking-[0.3em] text-gray-400"><?php esc_html_e( 'Error 404', 'tempone' ); ?></p>
	<h1 class="text-4xl font-bold mt-4 mb-6"><?php esc_html_e( 'Page not found', 'tempone' ); ?></h1>
	<p class="text-gray-600 mb-8"><?php esc_html_e( 'The page you are looking for might have been removed or never existed.', 'tempone' ); ?></p>
	<a class="px-6 py-3 bg-gray-900 text-white rounded-full" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php esc_html_e( 'Return home', 'tempone' ); ?>
	</a>
</main>
<?php
get_footer();
