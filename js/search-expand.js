/**
 * Expandable search form for desktop.
 *
 * @package tempone
 */

document.addEventListener('DOMContentLoaded', function () {
	const searchForm = document.querySelector('[data-search-form]');
	const searchInput = searchForm?.querySelector('input[type="search"]');
	const searchButton = searchForm?.querySelector('[data-search-toggle]');

	if (!searchForm || !searchInput || !searchButton) {
		return;
	}

	// Only expand on tablet & desktop (>= 768px)
	function isDesktop() {
		return window.innerWidth >= 768;
	}

	// Expand search form
	function expandSearch(focusInput = true) {
		if (!isDesktop()) return;

		searchForm.classList.add('is-expanded');
		if (focusInput) {
			setTimeout(() => {
				searchInput.focus();
			}, 100);
		}
	}

	// Collapse search form
	function collapseSearch() {
		if (!isDesktop()) return;

		searchForm.classList.remove('is-expanded');
		searchInput.blur();
	}

	// Toggle on button click (only when collapsed)
	searchButton.addEventListener('click', function (e) {
		if (!isDesktop()) return;

		if (!searchForm.classList.contains('is-expanded')) {
			e.preventDefault();
			expandSearch();
		}
		// When expanded, submit form normally
	});

	// Expand when input is focused (edge case: tab navigation)
	searchInput.addEventListener('focus', function () {
		expandSearch(false);
	});

	// Collapse when clicking outside
	document.addEventListener('click', function (e) {
		if (!isDesktop()) return;

		const isClickInside = searchForm.contains(e.target);
		if (!isClickInside && searchForm.classList.contains('is-expanded')) {
			collapseSearch();
		}
	});

	// Handle window resize
	let resizeTimer;
	window.addEventListener('resize', function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function () {
			// Remove class if switching to mobile
			if (!isDesktop()) {
				searchForm.classList.remove('is-expanded');
			}
		}, 250);
	});
});
