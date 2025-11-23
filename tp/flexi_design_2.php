<?php
/**
 * Flexible Layout: Design 2 - List + Hero.
 * Grid 2 kolom: 4 posts list (image-side) KIRI + 1 post besar (classic) KANAN.
 * SAMA dengan Design 1, hanya posisi kolom ditukar.
 *
 * ACF Fields:
 * - ane_category (Taxonomy: Category, Return: Term Object)
 * - ane_title (Text, optional custom title)
 *
 * @package tempone
 */

// Get category section data using helper function.
$data = tempone_get_category_section_data();

// Return early if no valid data.
if ( false === $data ) {
	return;
}

// Extract data.
$category      = $data['category'];
$section_title = $data['section_title'];
$category_link = $data['category_link'];

// Query 5 posts from selected category.
$query_args = array(
	'cat'            => $category->term_id,
	'posts_per_page' => 5,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => true,
);

$flexi_query = new WP_Query( $query_args );

if ( ! $flexi_query->have_posts() ) {
	return;
}
?>

<section class="flexi-design-2">
	<!-- Header: Title + Link -->
	<div class="flexi-design-2__header">
		<h2 class="flexi-design-2__title">
			<?php echo esc_html( $section_title ); ?>
		</h2>
		<a href="<?php echo esc_url( $category_link ); ?>" class="flexi-design-2__link">
			<?php esc_html_e( 'View More', 'tempone' ); ?>
			<span class="flexi-design-2__arrow">→</span>
		</a>
	</div>

	<!-- Content: Grid 2 Kolom (Reversed dari Design 1) -->
	<div class="flexi-design-2__grid">

		<!-- Right Column: 4 Posts Image-Side (muncul di kiri karena order CSS) -->
		<div class="flexi-design-2__list">
			<?php
			// Skip first post (akan dipakai untuk featured).
			if ( $flexi_query->have_posts() ) {
				$flexi_query->the_post(); // Skip post pertama.
			}

			// Display 4 posts berikutnya.
			while ( $flexi_query->have_posts() ) :
				$flexi_query->the_post();
				get_template_part( 'tp/content', 'image-side' );
			endwhile;
			?>
		</div>

		<!-- Left Column: 1 Post Classic (muncul di kanan karena order CSS) -->
		<div class="flexi-design-2__featured">
			<?php
			// Rewind query dan ambil post pertama.
			$flexi_query->rewind_posts();
			if ( $flexi_query->have_posts() ) :
				$flexi_query->the_post();
				get_template_part( 'tp/content', 'classic' );
			endif;
			wp_reset_postdata();
			?>
		</div>

	</div><!-- .flexi-design-2__grid -->
</section>
