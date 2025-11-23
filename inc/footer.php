<?php
/**
 * Footer helpers and option retrieval.
 *
 * @package tempone
 */

/**
 * Fetch generic option group via ACF.
 */
function tempone_get_option_group( string $group_key ) : array {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$data = get_field( $group_key, 'option' );

	return is_array( $data ) ? $data : array();
}

/**
 * Normalize ACF link field.
 */
function tempone_prepare_link( $link ) : array {
	if ( empty( $link ) || ! is_array( $link ) || empty( $link['url'] ) ) {
		return array();
	}

	return array(
		'url'    => esc_url( $link['url'] ),
		'title'  => sanitize_text_field( $link['title'] ?? __( 'Read More', 'tempone' ) ),
		'target' => empty( $link['target'] ) ? '_blank' : sanitize_html_class( $link['target'] ),
	);
}

/**
 * About company information.
 */
function tempone_get_about_company_data() : array {
	$group = tempone_get_option_group( 'ane_about_company' );

	return array(
		'name'        => sanitize_text_field( $group['ane_name'] ?? '' ),
		'description' => sanitize_text_field( $group['ane_description'] ?? '' ),
		'link'        => tempone_prepare_link( $group['ane_link'] ?? array() ),
	);
}

/**
 * Contact info data.
 */
function tempone_get_address_contact_data() : array {
	$group = tempone_get_option_group( 'ane_address_contact' );
	$phone = isset( $group['ane_phone'] ) ? (string) $group['ane_phone'] : '';
	$clean = preg_replace( '/\D+/', '', $phone );

	return array(
		'address'       => sanitize_text_field( $group['ane_address'] ?? '' ),
		'phone_display' => sanitize_text_field( $phone ),
		'phone'         => $clean,
		'email'         => sanitize_email( $group['ane_email'] ?? '' ),
	);
}

/**
 * Build WhatsApp link using API format for app opening.
 */
function tempone_get_whatsapp_link( string $number, string $message = '' ) : string {
	if ( empty( $number ) ) {
		return '';
	}

	// Use api.whatsapp.com for better app opening
	$url = 'https://api.whatsapp.com/send?phone=' . rawurlencode( $number );

	if ( $message ) {
		$url .= '&text=' . rawurlencode( $message );
	}

	return esc_url( $url );
}

/**
 * Social links list.
 */
function tempone_get_social_links() : array {
	$group = tempone_get_option_group( 'ane_social_media' );

	// Get WhatsApp number and message from ACF options
	$number = isset( $group['ane_whatsapp'] ) ? preg_replace( '/\D+/', '', (string) $group['ane_whatsapp'] ) : '';

	// Get WhatsApp message - try both possible field names
	$message = '';
	if ( ! empty( $group['ane_whatsapp_message'] ) ) {
		$message = sanitize_text_field( $group['ane_whatsapp_message'] );
	} elseif ( function_exists( 'get_field' ) ) {
		// Fallback: try to get directly from options
		$direct_message = get_field( 'ane_whatsapp_message', 'option' );
		if ( $direct_message ) {
			$message = sanitize_text_field( $direct_message );
		}
	}

	$map = array(
		'ane_facebook'        => array( 'slug' => 'facebook', 'label' => __( 'Facebook', 'tempone' ) ),
		'url_threads'         => array( 'slug' => 'threads', 'label' => __( 'Threads', 'tempone' ) ),
		'ane_tiktok'          => array( 'slug' => 'tiktok', 'label' => __( 'TikTok', 'tempone' ) ),
		'ane_instagram'       => array( 'slug' => 'instagram', 'label' => __( 'Instagram', 'tempone' ) ),
		'ane_whatsapp_chanel' => array( 'slug' => 'whatsapp-channel', 'label' => __( 'WhatsApp Channel', 'tempone' ) ),
		'ane_x'               => array( 'slug' => 'x', 'label' => __( 'X', 'tempone' ) ),
	);

	$links = array();

	foreach ( $map as $field => $meta ) {
		if ( empty( $group[ $field ] ) ) {
			continue;
		}

		$url = esc_url( $group[ $field ] );

		$links[] = array(
			'slug'  => $meta['slug'],
			'label' => $meta['label'],
			'url'   => $url,
			'icon'  => tempone_get_social_icon( $meta['slug'] ),
		);
	}

	if ( $number ) {
		$links[] = array(
			'slug'  => 'whatsapp',
			'label' => __( 'WhatsApp', 'tempone' ),
			'url'   => tempone_get_whatsapp_link( $number, $message ),
			'icon'  => tempone_get_social_icon( 'whatsapp' ),
		);
	}

	return $links;
}

/**
 * Retrieve site logo HTML.
 */
function tempone_get_footer_logo_html() : string {
	if ( function_exists( 'get_custom_logo' ) && has_custom_logo() ) {
		return get_custom_logo();
	}

	return '<span class="site-footer__logo-text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
}

/**
 * Compute copyright year range.
 */
function tempone_get_copyright_years() : string {
	$latest  = get_posts(
		array(
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		)
	);
	$earliest = get_posts(
		array(
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		)
	);

	$first_year = $earliest ? get_the_date( 'Y', $earliest[0] ) : gmdate( 'Y' );
	$last_year  = $latest ? get_the_date( 'Y', $latest[0] ) : $first_year;

	return ( $first_year === $last_year ) ? $first_year : $first_year . ' - ' . $last_year;
}

/**
 * Retrieve preferred company name.
 */
function tempone_get_company_display_name() : string {
	$about = tempone_get_about_company_data();
	return $about['name'] ?: get_bloginfo( 'name' );
}

/**
 * SVG icon helper for social labels.
 */
function tempone_get_social_icon( string $slug ) : string {
	$icons = array(
		// Facebook - Official F logo
		'facebook'          => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9.198 21.5h4v-8.01h3.604l.396-3.98h-4V7.5a1 1 0 0 1 1-1h3v-4h-3a5 5 0 0 0-5 5v2.01h-2l-.396 3.98h2.396v8.01Z"/></svg>',

		// Threads - Instagram Threads logo
		'threads'           => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M12.184 1.41h-.002C9.09 1.432 6.7 2.473 5.094 4.516c-1.428 1.815-2.16 4.348-2.184 7.49v.002c.025 3.143.756 5.662 2.184 7.477c1.606 2.042 4.009 3.084 7.1 3.105h.002c2.748-.019 4.697-.74 6.303-2.344c2.104-2.103 2.042-4.741 1.347-6.363c-.53-1.234-1.575-2.221-2.976-2.835c-.18-2.985-1.86-4.726-4.62-4.744c-1.63-.01-3.102.72-4.003 2.087l1.655 1.136c.533-.809 1.377-1.199 2.335-1.19c1.387.009 2.3.774 2.555 2.117a11.7 11.7 0 0 0-2.484-.105c-2.64.152-4.368 1.712-4.253 3.875c.12 2.262 2.312 3.495 4.393 3.381c2.492-.137 3.973-1.976 4.324-4.321c.577.373 1.003.85 1.244 1.413c.44 1.025.468 2.716-.915 4.098c-1.217 1.216-2.68 1.746-4.912 1.762c-2.475-.018-4.332-.811-5.537-2.343C5.52 16.774 4.928 14.688 4.906 12c.022-2.688.614-4.775 1.746-6.213c1.205-1.533 3.062-2.325 5.537-2.344c2.493.019 4.384.815 5.636 2.356c.691.85 1.124 1.866 1.413 2.915l1.94-.517c-.363-1.338-.937-2.613-1.815-3.694c-1.653-2.034-4.081-3.071-7.18-3.093m.236 10.968a9.4 9.4 0 0 1 2.432.156c-.14 1.578-.793 2.947-2.512 3.041c-1.112.063-2.237-.434-2.292-1.461c-.04-.764.525-1.63 2.372-1.736"/></svg>',

		// TikTok - Official logo
		'tiktok'            => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>',

		// Instagram - Rounded square with camera
		'instagram'         => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M17.34 5.46a1.2 1.2 0 1 0 1.2 1.2a1.2 1.2 0 0 0-1.2-1.2m4.6 2.42a7.6 7.6 0 0 0-.46-2.43a4.9 4.9 0 0 0-1.16-1.77a4.7 4.7 0 0 0-1.77-1.15a7.3 7.3 0 0 0-2.43-.47C15.06 2 14.72 2 12 2s-3.06 0-4.12.06a7.3 7.3 0 0 0-2.43.47a4.8 4.8 0 0 0-1.77 1.15a4.7 4.7 0 0 0-1.15 1.77a7.3 7.3 0 0 0-.47 2.43C2 8.94 2 9.28 2 12s0 3.06.06 4.12a7.3 7.3 0 0 0 .47 2.43a4.7 4.7 0 0 0 1.15 1.77a4.8 4.8 0 0 0 1.77 1.15a7.3 7.3 0 0 0 2.43.47C8.94 22 9.28 22 12 22s3.06 0 4.12-.06a7.3 7.3 0 0 0 2.43-.47a4.7 4.7 0 0 0 1.77-1.15a4.85 4.85 0 0 0 1.16-1.77a7.6 7.6 0 0 0 .46-2.43c0-1.06.06-1.4.06-4.12s0-3.06-.06-4.12M20.14 16a5.6 5.6 0 0 1-.34 1.86a3.06 3.06 0 0 1-.75 1.15a3.2 3.2 0 0 1-1.15.75a5.6 5.6 0 0 1-1.86.34c-1 .05-1.37.06-4 .06s-3 0-4-.06a5.7 5.7 0 0 1-1.94-.3a3.3 3.3 0 0 1-1.1-.75a3 3 0 0 1-.74-1.15a5.5 5.5 0 0 1-.4-1.9c0-1-.06-1.37-.06-4s0-3 .06-4a5.5 5.5 0 0 1 .35-1.9A3 3 0 0 1 5 5a3.1 3.1 0 0 1 1.1-.8A5.7 5.7 0 0 1 8 3.86c1 0 1.37-.06 4-.06s3 0 4 .06a5.6 5.6 0 0 1 1.86.34a3.06 3.06 0 0 1 1.19.8a3.1 3.1 0 0 1 .75 1.1a5.6 5.6 0 0 1 .34 1.9c.05 1 .06 1.37.06 4s-.01 3-.06 4M12 6.87A5.13 5.13 0 1 0 17.14 12A5.12 5.12 0 0 0 12 6.87m0 8.46A3.33 3.33 0 1 1 15.33 12A3.33 3.33 0 0 1 12 15.33"/></svg>',

		// WhatsApp Channel - Speaker icon variant
		'whatsapp-channel'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>',

		// X (Twitter) - Official X logo
		'x'                 => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',

		// WhatsApp - Official logo with chat bubble
		'whatsapp'          => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>',
	);

	return $icons[ $slug ] ?? '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10"/></svg>';
}
