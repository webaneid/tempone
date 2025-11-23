/**
 * GLightbox initialization for WordPress galleries.
 *
 * @package tempone
 */

document.addEventListener('DOMContentLoaded', function () {
	// Initialize GLightbox for all gallery images
	if (typeof GLightbox !== 'undefined') {
		const lightbox = GLightbox({
			selector: '.glightbox',
			touchNavigation: true,
			loop: true,
			autoplayVideos: true,
			closeOnOutsideClick: true,
			closeButton: true,
			closeEffect: 'fade',
			openEffect: 'fade',
			moreLength: 0,
			descPosition: 'bottom',
			skin: 'clean',
		});
	}
});
