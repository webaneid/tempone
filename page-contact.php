<?php
/**
 * Template Name: Contact Page
 *
 * Template untuk halaman kontak dengan Contact Form 7 integration.
 *
 * @package tempone
 */

get_header();

// Get company data from ACF options
$contact = tempone_get_address_contact_data();
$company = tempone_get_about_company_data();
$company_name = tempone_get_company_display_name();

// Get contact page content from ACF options
$contact_group = tempone_get_option_group( 'ane_page_contact' );
$tagline = ! empty( $contact_group['ane_title'] ) ? $contact_group['ane_title'] : '';
$description = ! empty( $contact_group['ane_description'] ) ? $contact_group['ane_description'] : '';
?>

<div class="contact-page">
	<div class="contact-page__container">
		<?php while ( have_posts() ) : the_post(); ?>

			<!-- Header Section -->
			<header class="contact-page__header">
				<h1 class="contact-page__title"><?php the_title(); ?></h1>
				<?php if ( $tagline ) : ?>
					<p class="contact-page__tagline"><?php echo esc_html( $tagline ); ?></p>
				<?php endif; ?>
				<?php if ( $description ) : ?>
					<p class="contact-page__subtitle"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</header>

			<!-- Main Content Grid -->
			<div class="contact-page__content">

				<!-- Contact Form (Left Column) -->
				<div class="contact-page__form-wrapper">
					<?php
					// Display page content (Contact Form 7 shortcode should be in content)
					the_content();
					?>
				</div>

				<!-- Contact Information (Right Column) -->
				<div class="contact-page__info-wrapper">

					<!-- Email Support -->
					<?php if ( $contact['email'] ) : ?>
						<div class="contact-info__card">
							<h2 class="contact-info__title"><?php esc_html_e( 'Email Support', 'tempone' ); ?></h2>
							<p class="contact-info__description">
								<?php esc_html_e( 'Interested in trying it? speak to our team.', 'tempone' ); ?>
							</p>
							<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>" class="contact-info__value">
								<?php echo esc_html( $contact['email'] ); ?>
							</a>
						</div>
					<?php endif; ?>

					<!-- Call Us -->
					<?php if ( $contact['phone'] ) : ?>
						<div class="contact-info__card">
							<h2 class="contact-info__title"><?php esc_html_e( 'Call Us', 'tempone' ); ?></h2>
							<p class="contact-info__description">
								<?php esc_html_e( 'Give us a call for immediate assistance.', 'tempone' ); ?>
							</p>
							<a href="tel:<?php echo esc_attr( $contact['phone'] ); ?>" class="contact-info__value">
								<?php echo esc_html( $contact['phone_display'] ); ?>
							</a>
						</div>
					<?php endif; ?>

					<!-- Office Location -->
					<?php if ( $contact['address'] ) : ?>
						<div class="contact-info__card">
							<h2 class="contact-info__title">
								<?php echo esc_html( $company_name ); ?>
							</h2>
							<p class="contact-info__hours">
								<?php esc_html_e( 'Visit our office Mon - Fri, 08:00 AM - 05:00PM', 'tempone' ); ?>
							</p>
							<p class="contact-info__address">
								<?php echo esc_html( $contact['address'] ); ?>
							</p>
						</div>
					<?php endif; ?>

				</div>

			</div>

		<?php endwhile; ?>
	</div>
</div>

<?php
get_footer();
