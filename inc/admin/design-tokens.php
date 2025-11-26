<?php
/**
 * Admin Design Tokens - Centralized Design System.
 *
 * Provides consistent design values across entire admin interface:
 * - Border radius scales
 * - Spacing scales
 * - Shadow definitions
 * - Animation timings
 * - Z-index layers
 *
 * Colors are dynamic (from ACF) and injected via inc/acf-layouts.php
 *
 * @package tempone
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inject design tokens CSS variables into admin head.
 *
 * Provides consistent design values without ACF dependency.
 * Colors handled separately by inc/acf-layouts.php
 */
function tempone_admin_design_tokens() {
	?>
	<style id="tempone-admin-design-tokens">
		:root {
			/* ============================================
			   BORDER RADIUS
			   ============================================ */
			--tempone-radius-xl: 20px;   /* Large containers, modals */
			--tempone-radius-lg: 12px;   /* Cards, postboxes */
			--tempone-radius-md: 10px;   /* Inputs, buttons */
			--tempone-radius-sm: 8px;    /* Icons, badges */
			--tempone-radius-full: 999px; /* Pills */
			--tempone-radius-circle: 50%; /* Avatars, rank badges */

			/* ============================================
			   SPACING SCALE
			   ============================================ */
			--tempone-space-xs: 0.5rem;   /* 8px - Tight spacing */
			--tempone-space-sm: 0.75rem;  /* 12px - Small gaps */
			--tempone-space-md: 1rem;     /* 16px - Default spacing */
			--tempone-space-lg: 1.5rem;   /* 24px - Section spacing */
			--tempone-space-xl: 2rem;     /* 32px - Large spacing */
			--tempone-space-2xl: 2.5rem;  /* 40px - Hero spacing */
			--tempone-space-3xl: 3rem;    /* 48px - Extra large */

			/* ============================================
			   SHADOWS
			   ============================================ */
			--tempone-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
			--tempone-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
			--tempone-shadow-lg: 0 8px 20px rgba(0, 0, 0, 0.1);
			--tempone-shadow-xl: 0 12px 28px rgba(0, 0, 0, 0.15);

			/* ============================================
			   TRANSITIONS
			   ============================================ */
			--tempone-transition-fast: 0.15s ease;
			--tempone-transition-base: 0.2s ease;
			--tempone-transition-slow: 0.3s ease;

			/* ============================================
			   Z-INDEX LAYERS
			   ============================================ */
			--tempone-z-base: 1;
			--tempone-z-dropdown: 1000;
			--tempone-z-sticky: 1020;
			--tempone-z-fixed: 1030;
			--tempone-z-modal-backdrop: 1040;
			--tempone-z-modal: 1050;
			--tempone-z-popover: 1060;
			--tempone-z-tooltip: 1070;

			/* ============================================
			   TYPOGRAPHY SCALE
			   ============================================ */
			--tempone-font-xs: 0.75rem;      /* 12px */
			--tempone-font-sm: 0.8125rem;    /* 13px */
			--tempone-font-base: 0.9375rem;  /* 15px */
			--tempone-font-md: 1rem;         /* 16px */
			--tempone-font-lg: 1.125rem;     /* 18px */
			--tempone-font-xl: 1.25rem;      /* 20px */
			--tempone-font-2xl: 1.5rem;      /* 24px */
			--tempone-font-3xl: 1.875rem;    /* 30px */
			--tempone-font-4xl: 2.25rem;     /* 36px */

			/* ============================================
			   FONT WEIGHTS
			   ============================================ */
			--tempone-font-normal: 400;
			--tempone-font-medium: 500;
			--tempone-font-semibold: 600;
			--tempone-font-bold: 700;
			--tempone-font-extrabold: 800;

			/* ============================================
			   LINE HEIGHTS
			   ============================================ */
			--tempone-leading-tight: 1.2;
			--tempone-leading-snug: 1.4;
			--tempone-leading-normal: 1.5;
			--tempone-leading-relaxed: 1.6;
			--tempone-leading-loose: 1.8;

			/* ============================================
			   LETTER SPACING
			   ============================================ */
			--tempone-tracking-tight: -0.02em;
			--tempone-tracking-normal: 0;
			--tempone-tracking-wide: 0.05em;
			--tempone-tracking-wider: 0.08em;

			/* ============================================
			   CONTAINER WIDTHS
			   ============================================ */
			--tempone-container-sm: 640px;
			--tempone-container-md: 768px;
			--tempone-container-lg: 1024px;
			--tempone-container-xl: 1280px;

			/* ============================================
			   MOBILE TOUCH TARGETS
			   ============================================ */
			--tempone-tap-target-min: 44px; /* Apple HIG minimum */

			/* ============================================
			   BREAKPOINTS (for reference in JS)
			   ============================================ */
			--tempone-breakpoint-sm: 600px;
			--tempone-breakpoint-md: 782px;  /* WordPress mobile admin */
			--tempone-breakpoint-lg: 1024px;
			--tempone-breakpoint-xl: 1200px;

			/* ============================================
			   GLASSMORPHISM (for login & special cards)
			   ============================================ */
			--tempone-glass-bg: rgba(255, 255, 255, 0.08);
			--tempone-glass-border: rgba(255, 255, 255, 0.1);
			--tempone-glass-blur: blur(20px);

			/* ============================================
			   GRADIENT OVERLAYS
			   ============================================ */
			--tempone-gradient-overlay: linear-gradient(
				135deg,
				rgba(0, 0, 0, 0.4),
				rgba(0, 0, 0, 0.2)
			);
		}
	</style>
	<?php
}
add_action( 'admin_head', 'tempone_admin_design_tokens', 1 ); // Priority 1 - load before custom colors

/**
 * Get design token value by name.
 *
 * Helper function untuk access design tokens dari PHP.
 * Useful untuk inline styles atau conditional logic.
 *
 * @param string $token_name Token name tanpa prefix (e.g. 'radius-lg', 'space-md').
 * @return string Token value atau empty string jika tidak found.
 */
function tempone_get_design_token( string $token_name ) : string {
	$tokens = array(
		// Border radius.
		'radius-xl'     => '20px',
		'radius-lg'     => '12px',
		'radius-md'     => '10px',
		'radius-sm'     => '8px',
		'radius-full'   => '999px',
		'radius-circle' => '50%',

		// Spacing.
		'space-xs'  => '0.5rem',
		'space-sm'  => '0.75rem',
		'space-md'  => '1rem',
		'space-lg'  => '1.5rem',
		'space-xl'  => '2rem',
		'space-2xl' => '2.5rem',
		'space-3xl' => '3rem',

		// Shadows.
		'shadow-sm' => '0 1px 3px rgba(0, 0, 0, 0.05)',
		'shadow-md' => '0 4px 12px rgba(0, 0, 0, 0.08)',
		'shadow-lg' => '0 8px 20px rgba(0, 0, 0, 0.1)',
		'shadow-xl' => '0 12px 28px rgba(0, 0, 0, 0.15)',

		// Transitions.
		'transition-fast' => '0.15s ease',
		'transition-base' => '0.2s ease',
		'transition-slow' => '0.3s ease',

		// Typography.
		'font-xs'   => '0.75rem',
		'font-sm'   => '0.8125rem',
		'font-base' => '0.9375rem',
		'font-md'   => '1rem',
		'font-lg'   => '1.125rem',
		'font-xl'   => '1.25rem',
		'font-2xl'  => '1.5rem',
		'font-3xl'  => '1.875rem',
		'font-4xl'  => '2.25rem',

		// Font weights.
		'font-normal'    => '400',
		'font-medium'    => '500',
		'font-semibold'  => '600',
		'font-bold'      => '700',
		'font-extrabold' => '800',

		// Touch targets.
		'tap-target-min' => '44px',
	);

	return $tokens[ $token_name ] ?? '';
}

/**
 * Output inline style with design token.
 *
 * Helper untuk generate inline style attribute dengan token value.
 *
 * @param string $property CSS property name (e.g. 'border-radius', 'padding').
 * @param string $token    Token name (e.g. 'radius-lg', 'space-md').
 * @return string Inline style attribute atau empty string.
 */
function tempone_token_style( string $property, string $token ) : string {
	$value = tempone_get_design_token( $token );

	if ( empty( $value ) ) {
		return '';
	}

	return sprintf( ' style="%s: %s;"', esc_attr( $property ), esc_attr( $value ) );
}

/**
 * Check if current screen is mobile admin.
 *
 * WordPress considers screen mobile if width < 782px.
 * Useful untuk conditional rendering.
 *
 * @return bool True if mobile admin, false otherwise.
 */
function tempone_is_mobile_admin() : bool {
	// Check if wp_is_mobile() available.
	if ( function_exists( 'wp_is_mobile' ) ) {
		return wp_is_mobile();
	}

	// Fallback: check user agent.
	$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

	if ( empty( $user_agent ) ) {
		return false;
	}

	// Mobile device detection.
	$mobile_agents = array(
		'Mobile',
		'Android',
		'iPhone',
		'iPad',
		'iPod',
		'BlackBerry',
		'Windows Phone',
	);

	foreach ( $mobile_agents as $agent ) {
		if ( stripos( $user_agent, $agent ) !== false ) {
			return true;
		}
	}

	return false;
}

/**
 * Get responsive tap target size.
 *
 * Returns minimum tap target size untuk mobile or desktop.
 *
 * @param bool $force_mobile Force mobile size even on desktop.
 * @return string Size value (e.g. '44px' atau '32px').
 */
function tempone_tap_target_size( bool $force_mobile = false ) : string {
	if ( $force_mobile || tempone_is_mobile_admin() ) {
		return tempone_get_design_token( 'tap-target-min' ); // 44px
	}

	return '32px'; // Desktop can be smaller.
}

/**
 * Enqueue admin animations script.
 *
 * Smooth page transitions, fade-ins, ripple effects.
 */
function tempone_admin_animations_script() {
	wp_enqueue_script(
		'tempone-admin-animations',
		TEMPONE_URI . '/js/admin-animations.js',
		array(),
		TEMPONE_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'tempone_admin_animations_script', 999 );
