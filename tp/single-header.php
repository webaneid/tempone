<?php
/**
 * Single header partial.
 *
 * @package tempone
 */
?>
<header class="space-y-4">
	<div class="flex flex-wrap gap-2 text-xs uppercase text-gray-500">
		<?php the_category( ' ' ); ?>
	</div>
	<h1 class="text-4xl font-bold leading-tight"><?php the_title(); ?></h1>
	<div class="text-sm text-gray-500">
		<?php
		printf(
			/* translators: %s: author name */
			esc_html__( 'By %s', 'tempone' ),
			'<span class="font-semibold text-gray-700">' . esc_html( get_the_author() ) . '</span>'
		);
		echo ' · ';
		echo esc_html( get_the_date() );
		?>
	</div>
</header>
