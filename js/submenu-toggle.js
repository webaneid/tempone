/**
 * Submenu toggle for mobile navigation.
 *
 * @package tempone
 */

document.addEventListener('DOMContentLoaded', function () {
	console.log('Submenu toggle script loaded');

	// Mobile submenu toggle
	const menuItems = document.querySelectorAll('.header-menu .menu-item-has-children');
	const menuContainer = document.querySelector('.header-menu--primary');
	console.log('Found menu items:', menuItems.length);

	// Close all open submenus
	function closeAllSubmenus() {
		menuItems.forEach(function (item) {
			item.classList.remove('is-open');
		});
	}

	menuItems.forEach(function (item) {
		// Get direct child anchor using :scope
		const link = item.querySelector(':scope > a');

		if (!link) {
			console.log('No link found for item');
			return;
		}

		console.log('Link found:', link.textContent);

		link.addEventListener('click', function (e) {
			console.log('Menu clicked, window width:', window.innerWidth);

			// Only toggle on mobile (< 768px)
			if (window.innerWidth >= 768) return;

			console.log('Preventing default and toggling');

			// Always prevent default on mobile for parent items
			e.preventDefault();
			e.stopPropagation();

			// Close other submenus first
			const wasOpen = item.classList.contains('is-open');
			closeAllSubmenus();

			// Toggle current submenu
			if (!wasOpen) {
				item.classList.add('is-open');

				// Position submenu using fixed coordinates
				const submenu = item.querySelector(':scope > .sub-menu');
				if (submenu) {
					const rect = link.getBoundingClientRect();
					submenu.style.top = (rect.bottom + window.scrollY) + 'px';
					submenu.style.left = rect.left + 'px';
				}
			}
		});
	});

	// Close submenu when clicking outside
	document.addEventListener('click', function (e) {
		if (window.innerWidth >= 768) return;

		// Check if click is outside all menu items
		let clickedInsideMenu = false;
		menuItems.forEach(function (item) {
			if (item.contains(e.target)) {
				clickedInsideMenu = true;
			}
		});

		if (!clickedInsideMenu) {
			closeAllSubmenus();
		}
	});

	// Close submenu when scrolling the menu horizontally
	if (menuContainer) {
		menuContainer.addEventListener('scroll', function () {
			if (window.innerWidth >= 768) return;
			closeAllSubmenus();
		});
	}
});
