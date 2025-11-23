<?php
/**
 * Template part: Flexible Content Section with Sidebar.
 * Grid 2fr 1fr - Left: ACF Flexible Layouts, Right: Static Sidebar.
 *
 * Usage:
 * get_template_part( 'tp/section', 'flexible' );
 *
 * @package tempone
 */

// Check if ACF flexible content field exists.
if ( ! function_exists( 'have_rows' ) ) {
	return;
}
?>

<section class="section-flexible-content">
	<div class="container">
		<div class="grid lg:grid-cols-[2fr_1fr] gap-8 lg:divide-x divide-gray-200">

			<!-- Left Column: Flexible Content Layouts -->
			<div class="flexible-content lg:pr-8 min-w-0">
				<?php
				// DEBUG: Check if field exists.
				$has_flexible = have_rows( 'ane_flexible_content' );

				// DEBUG OUTPUT (hapus setelah fix).
				echo '<!-- DEBUG: have_rows result: ' . ( $has_flexible ? 'TRUE' : 'FALSE' ) . ' -->';
				echo '<!-- DEBUG: Current Post ID: ' . get_the_ID() . ' -->';

				if ( $has_flexible ) :
					while ( have_rows( 'ane_flexible_content' ) ) :
						the_row();

						// Get layout name (flexi_design_1, flexi_design_2, dst).
						$layout_name = get_row_layout();

						// DEBUG OUTPUT.
						echo '<!-- DEBUG: Layout Name: ' . esc_html( $layout_name ) . ' -->';
						echo '<!-- DEBUG: Looking for file: tp/' . esc_html( $layout_name ) . '.php -->';

						// Load template part: tp/{layout_name}.php (layout name sudah lengkap).
						get_template_part( 'tp/' . $layout_name );

					endwhile;
				else :
					?>
					<div class="p-6 bg-gray-100 rounded-lg text-center">
						<p class="text-sm text-gray-600">
							<?php esc_html_e( 'No flexible content. Add layouts from ACF on this page.', 'tempone' ); ?>
						</p>
					</div>
					<?php
				endif;
				?>
			</div>

			<!-- Right Column: Static Sidebar -->
			<aside class="sidebar lg:pl-8">
				<?php
				if ( is_active_sidebar( 'sidebar-landingpage' ) ) :
					dynamic_sidebar( 'sidebar-landingpage' );
				else :
					?>
					<div class="sidebar-placeholder p-6 bg-gray-100 rounded-lg text-center">
						<p class="text-sm text-gray-600">
							<?php esc_html_e( 'Add widgets for Landing Page Sidebar in WP Admin → Appearance → Widgets.', 'tempone' ); ?>
						</p>
					</div>
					<?php
				endif;
				?>
			</aside>

		</div><!-- .grid -->
	</div><!-- .container -->
</section>
