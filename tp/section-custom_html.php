<?php
/**
 * Custom HTML block.
 *
 * @package tempone
 */

$content = function_exists( 'get_sub_field' ) ? get_sub_field( 'content' ) : __( 'Add custom HTML in ACF settings.', 'tempone' );
?>
<section class="py-12 bg-gray-50" aria-label="<?php esc_attr_e( 'Custom Content', 'tempone' ); ?>">
	<div class="max-w-4xl mx-auto px-4">
		<?php echo tempone_safe_html( $content ); ?>
	</div>
</section>
