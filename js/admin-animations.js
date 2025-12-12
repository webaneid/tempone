/**
 * Modern Admin Page Animations & Transitions
 *
 * Minimal animations for professional feel without performance impact.
 *
 * @package tempone
 */

(function() {
	'use strict';

	/**
	 * Lightweight fade in on page load - NO stagger for performance.
	 */
	function initPageAnimations() {
		// Immediately show content - no blocking
		document.body.classList.add('tempone-loaded');

		// Optional subtle fade for main elements only
		setTimeout(function() {
			const mainElements = document.querySelectorAll('.wrap > h1, .wrap > h2, .postbox');
			mainElements.forEach(function(element) {
				element.classList.add('tempone-animate-in');
			});
		}, 50); // Very short delay
	}

	/**
	 * Animate progress bars.
	 */
	function initProgressBarAnimations() {
		const progressBars = document.querySelectorAll('.tempone-breakdown-progress__fill');

		progressBars.forEach(function(bar) {
			const targetWidth = bar.style.width;
			bar.style.width = '0%';

			setTimeout(function() {
				bar.style.width = targetWidth;
			}, 500);
		});
	}

	/**
	 * Add skeleton loading for async content.
	 */
	function addSkeletonLoader(container) {
		container.classList.add('tempone-skeleton-loading');

		setTimeout(function() {
			container.classList.remove('tempone-skeleton-loading');
			container.classList.add('tempone-skeleton-loaded');
		}, 300);
	}

	/**
	 * Initialize animations.
	 */
	function init() {
		initPageAnimations();
		initProgressBarAnimations();
	}

	// Initialize when DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	// Expose addSkeletonLoader for async content
	window.temponeAddSkeletonLoader = addSkeletonLoader;

})();
