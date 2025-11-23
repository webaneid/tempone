<?php
/**
 * Global header.
 *
 * @package tempone
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-white text-gray-900' ); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
	<div class="container">
		<div class="site-header__top">
			<div class="site-header__brand">
				<button class="site-header__menu-toggle" type="button" data-menu-toggle aria-expanded="false" aria-controls="site-header-menus">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16 18v2H5v-2zm5-7v2H3v-2zm-2-7v2H8V4z"/></svg>
					<span><?php esc_html_e( 'Menu', 'tempone' ); ?></span>
				</button>
				<?php if ( function_exists( 'get_custom_logo' ) && has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a class="site-header__logo custom-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<img src="<?php echo esc_url( TEMPONE_URI . '/img/logo-tempone.svg' ); ?>" class="custom-logo" alt="<?php bloginfo( 'name' ); ?>" width="140" height="32" loading="lazy" />
					</a>
				<?php endif; ?>
			</div>
			<?php if ( has_nav_menu( 'secondary' ) ) : ?>
				<nav class="site-header__secondary-nav" aria-label="<?php esc_attr_e( 'Secondary menu', 'tempone' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'secondary',
							'container'      => false,
							'menu_class'     => 'header-menu header-menu--secondary',
							'depth'          => 1,
						)
					);
					?>
				</nav>
			<?php endif; ?>
			<div class="site-header__actions">
				<a class="site-header__action site-header__action--subscribe" href="<?php echo esc_url( home_url( '/langganan' ) ); ?>">
					<?php esc_html_e( 'Subscribe', 'tempone' ); ?>
				</a>
				<a class="site-header__action site-header__action--login" href="<?php echo esc_url( wp_login_url() ); ?>">
					<svg class="site-header__action-icon" width="18" height="18" viewBox="0 0 18 18" role="img" aria-hidden="true">
						<circle cx="7" cy="6" r="3.5" stroke="currentColor" stroke-width="1.2" fill="none" />
						<path d="M1.5 16c1-3 3-4.5 5.5-4.5s4.5 1.5 5.5 4.5" stroke="currentColor" stroke-width="1.2" fill="none" stroke-linecap="round" />
						<path d="M11 9h5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" />
						<path d="M15.5 6.5L17 9l-1.5 2.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
					</svg>
					<?php esc_html_e( 'Login', 'tempone' ); ?>
				</a>
			</div>
		</div>
		<div class="site-header__menus" id="site-header-menus" data-header-menus>
			<div class="site-header__bottom">
				<?php if ( has_nav_menu( 'primary' ) ) : ?>
					<nav class="site-header__primary-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'tempone' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'container'      => false,
								'menu_class'     => 'header-menu header-menu--primary',
								'depth'          => 2,
							)
						);
						?>
					</nav>
				<?php endif; ?>
				<form class="site-header__search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" data-search-form>
					<label class="screen-reader-text" for="header-search"><?php esc_html_e( 'Search', 'tempone' ); ?></label>
					<input type="search" id="header-search" name="s" placeholder="<?php esc_attr_e( 'Search news…', 'tempone' ); ?>" />
					<button type="submit" data-search-toggle>
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"></circle>
							<path d="m21 21-4.35-4.35"></path>
						</svg>
						<span class="screen-reader-text"><?php esc_html_e( 'Submit search', 'tempone' ); ?></span>
					</button>
				</form>
			</div>
		</div>
	</div>
</header>
