<?php
/**
 * Security helpers.
 *
 * @package tempone
 */

/**
 * Safe trim for textareas or optional fields.
 */
function tempone_clean_text( $value ) : string {
	return trim( wp_strip_all_tags( (string) $value ) );
}

/**
 * Safe html output for WYSIWYG.
 */
function tempone_safe_html( $value ) : string {
	return wp_kses_post( $value );
}
