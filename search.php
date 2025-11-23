<?php
/**
 * Search results template.
 *
 * @package tempone
 */

get_header();
?>

<main id="primary" class="container py-8">

	<div class="mb-6">
		<?php tempone_breadcrumbs(); ?>
	</div>

	<header class="page-header mb-8">
		<h1 class="text-3xl font-bold">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Search results for "%s"', 'tempone' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
	</header>

	<?php
	// Mobile detection.
	$is_mobile = wp_is_mobile();
	?>

	<div class="grid lg:grid-cols-[2fr_1fr] gap-8 lg:divide-x divide-gray-200">
		<!-- Main Content -->
		<div class="main-content lg:pr-8 min-w-0">

		<?php
		// Main Posts.
		if ( have_posts() ) :
			?>
			<div class="posts-list space-y-6">
				<?php
				$post_counter = 1;
				while ( have_posts() ) :
					the_post();

					// Post 6 dan 11 pakai overlay.
					if ( 6 === $post_counter || 11 === $post_counter ) {
						if ( $is_mobile ) {
							echo '<div class="-mx-4 mb-6">';
							get_template_part( 'tp/content', 'overlay' );
							echo '</div>';
						} else {
							get_template_part( 'tp/content', 'overlay' );
						}
					} elseif ( $is_mobile ) {
						get_template_part( 'tp/content', 'image-side' );
					} else {
						get_template_part( 'tp/content' );
					}

					$post_counter++;
				endwhile;
				?>
			</div>

			<?php
			global $wp_query;
			ane_post_pagination( $wp_query );
		else :
			?>
			<p><?php esc_html_e( 'Sorry, nothing matched your search.', 'tempone' ); ?></p>
		<?php endif; ?>

		</div>

		<!-- Sidebar -->
		<aside class="sidebar lg:pl-8">
			<?php if ( is_active_sidebar( 'sidebar-main' ) ) : ?>
				<?php dynamic_sidebar( 'sidebar-main' ); ?>
			<?php else : ?>
				<div class="sidebar-placeholder p-6 bg-gray-100 rounded-lg text-center">
					<p class="text-sm text-gray-600"><?php esc_html_e( 'No widgets in sidebar. Add from WP Admin.', 'tempone' ); ?></p>
				</div>
			<?php endif; ?>
		</aside>
	</div>

</main>

<?php
get_footer();
