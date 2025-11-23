<?php
/**
 * Comments Template.
 *
 * Handles both WordPress native comments dan Facebook Comments.
 * Switch via ACF Options: Tempone Setup → Customization → Facebook Comments.
 *
 * @package tempone
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't load directly.
if ( post_password_required() ) {
	return;
}

// Check if Facebook Comments enabled.
if ( tempone_is_facebook_comments_enabled() ) {
	// Load Facebook Comments template.
	get_template_part( 'tp/comments', 'facebook' );
	return;
}

// WordPress Native Comments.
?>
<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			if ( 1 === $comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'One comment on &ldquo;%1$s&rdquo;', 'tempone' ),
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			} else {
				printf(
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$s comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', $comment_count, 'comments title', 'tempone' ) ),
					number_format_i18n( $comment_count ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => '<span class="screen-reader-text">' . __( 'Previous Comments', 'tempone' ) . '</span>',
				'next_text' => '<span class="screen-reader-text">' . __( 'Next Comments', 'tempone' ) . '</span>',
			)
		);
		?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'tempone' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h3>',
			'class_submit'       => 'submit button',
		)
	);
	?>

</div><!-- #comments -->
