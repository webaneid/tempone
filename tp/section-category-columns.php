<?php
/**
 * Template part: Category Columns Section (Repeater).
 * Grid 4 kolom responsive dengan 1 hero (content-classic) + 3 title posts (content-title).
 *
 * ACF Field:
 * - ane_section_repeater (Repeater)
 *   - ane_category (Taxonomy: Category, Return: Term Object)
 *   - ane_title (Text, optional custom title)
 *
 * @package tempone
 */

// Check if ACF repeater exists.
if ( ! function_exists( 'have_rows' ) || ! have_rows( 'ane_section_repeater' ) ) {
	return;
}
?>

<section class="section-category-columns">
	<div class="container">
		<div class="category-columns">
			<?php
			while ( have_rows( 'ane_section_repeater' ) ) :
				the_row();

				// Get category section data using helper function.
				$data = tempone_get_category_section_data();

				// Skip if no valid data.
				if ( false === $data ) {
					continue;
				}

				// Extract data.
				$category      = $data['category'];
				$section_title = $data['section_title'];
				$category_link = $data['category_link'];

				// Query 4 posts from selected category (1 hero + 3 title posts).
				$query_args = array(
					'cat'            => $category->term_id,
					'posts_per_page' => 4,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'no_found_rows'  => true,
				);

				$column_query = new WP_Query( $query_args );

				if ( ! $column_query->have_posts() ) {
					continue;
				}
				?>

				<!-- Single Category Column -->
				<div class="category-column">
					<!-- Column Header -->
					<div class="category-column__header">
						<h2 class="category-column__title">
							<a href="<?php echo esc_url( $category_link ); ?>" class="category-column__title-link">
								<?php echo esc_html( $section_title ); ?>
							</a>
						</h2>
					</div>

					<!-- Hero Post (First Post - content-classic) -->
					<div class="category-column__hero">
						<?php
						if ( $column_query->have_posts() ) :
							$column_query->the_post();
							get_template_part( 'tp/content', 'classic' );
						endif;
						?>
					</div>

					<!-- Title Posts (Posts 2, 3, 4 - content-title) -->
					<div class="category-column__titles">
						<?php
						while ( $column_query->have_posts() ) :
							$column_query->the_post();
							get_template_part( 'tp/content', 'title' );
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</div>

				<?php
			endwhile;
			?>
		</div><!-- .category-columns -->
	</div><!-- .container -->
</section>
