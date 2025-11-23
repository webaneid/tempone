<?php
/**
 * Global footer.
 *
 * @package tempone
 */

$about        = tempone_get_about_company_data();
$contact      = tempone_get_address_contact_data();
$social       = tempone_get_social_links();
$has_menu     = has_nav_menu( 'media_network' ) || has_nav_menu( 'footer' );
$company_name = tempone_get_company_display_name();
$company_desc = $about['description'] ? $about['description'] : get_bloginfo( 'description' );
?>
<footer class="site-footer">
	<div class="container">
		<div class="site-footer__top">
			<div class="site-footer__about">
				<div class="site-footer__logo">
					<?php echo tempone_get_footer_logo_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<p class="site-footer__description"><?php echo esc_html( $company_desc ); ?></p>
				<?php if ( ! empty( $about['link'] ) ) : ?>
					<a class="site-footer__about-link" href="<?php echo esc_url( $about['link']['url'] ); ?>" target="<?php echo esc_attr( $about['link']['target'] ); ?>" rel="noopener noreferrer">
						<?php echo esc_html( $about['link']['title'] ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $contact['address'] || $contact['phone_display'] || $contact['email'] ) : ?>
					<div class="site-footer__contact">
						<h3><?php esc_html_e( 'Contact', 'tempone' ); ?></h3>
						<ul>
							<?php if ( $contact['address'] ) : ?>
								<li>
									<span><?php esc_html_e( 'Address', 'tempone' ); ?></span>
									<?php echo esc_html( $contact['address'] ); ?>
								</li>
							<?php endif; ?>
							<?php if ( $contact['phone'] ) : ?>
								<li>
									<span><?php esc_html_e( 'Phone', 'tempone' ); ?></span>
									<a href="tel:<?php echo esc_attr( $contact['phone'] ); ?>">
										<?php echo esc_html( $contact['phone_display'] ); ?>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( $contact['email'] ) : ?>
								<li>
									<span><?php esc_html_e( 'Email', 'tempone' ); ?></span>
									<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>">
										<?php echo esc_html( $contact['email'] ); ?>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $has_menu ) : ?>
				<div class="site-footer__menu-grid">
					<?php if ( has_nav_menu( 'media_network' ) ) : ?>
						<div class="site-footer__menu">
							<h4><?php esc_html_e( 'Media Networks', 'tempone' ); ?></h4>
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'media_network',
									'container'      => false,
									'menu_class'     => 'site-footer__menu-list',
									'depth'          => 1,
									'fallback_cb'    => false,
								)
							);
							?>
						</div>
					<?php endif; ?>
					<?php if ( has_nav_menu( 'footer' ) ) : ?>
						<div class="site-footer__menu">
							<h4><?php esc_html_e( 'Information', 'tempone' ); ?></h4>
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'footer',
									'container'      => false,
									'menu_class'     => 'site-footer__menu-list',
									'depth'          => 1,
									'fallback_cb'    => false,
								)
							);
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="site-footer__divider" role="presentation"></div>

		<div class="site-footer__bottom">
			<div class="site-footer__bottom-brand">
				<?php echo tempone_get_footer_logo_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php if ( has_nav_menu( 'footer_bottom' ) ) : ?>
				<nav class="site-footer__bottom-menu" aria-label="<?php esc_attr_e( 'Footer links', 'tempone' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer_bottom',
							'container'      => false,
							'menu_class'     => 'site-footer__bottom-list',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				</nav>
			<?php endif; ?>
			<?php if ( $social ) : ?>
				<div class="site-footer__follow">
					<span class="site-footer__follow-label"><?php esc_html_e( 'Follow us on', 'tempone' ); ?></span>
					<div class="site-footer__follow-icons">
							<?php foreach ( $social as $network ) : ?>
								<a class="site-footer__follow-icon" href="<?php echo esc_url( $network['url'] ); ?>" target="_blank" rel="noopener noreferrer">
									<span class="screen-reader-text"><?php echo esc_html( $network['label'] ); ?></span>
									<?php
									$allowed = array(
										'svg'   => array(
											'viewbox'     => true,
											'aria-hidden' => true,
											'fill'        => true,
											'stroke'      => true,
											'stroke-width'=> true,
										),
										'path'  => array(
											'd'             => true,
											'fill'          => true,
											'stroke'        => true,
											'stroke-width'  => true,
											'stroke-linecap'=> true,
											'stroke-linejoin'=> true,
										),
										'circle'=> array(
											'cx'    => true,
											'cy'    => true,
											'r'     => true,
											'fill'  => true,
											'stroke'=> true,
											'stroke-width'=> true,
										),
									);
									if ( ! empty( $network['icon'] ) ) {
										echo wp_kses( $network['icon'], $allowed );
									} else {
										echo '<span aria-hidden="true">' . esc_html( strtoupper( substr( $network['label'], 0, 1 ) ) ) . '</span>';
									}
									?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>

		<div class="site-footer__copyright">
			<?php
			printf(
				/* translators: 1: copyright years, 2: company name */
				esc_html__( 'Copyright © %1$s %2$s.', 'tempone' ),
				esc_html( tempone_get_copyright_years() ),
				esc_html( $company_name )
			);
			?>
			<span class="site-footer__credit">
				<?php echo wp_kses_post( tempone_load_designed_by() ); ?>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
