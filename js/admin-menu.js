/**
 * Lightweight Admin Menu - Tooltips + Click to Open Submenu
 *
 * @package tempone
 */

(function() {
	'use strict';

	/**
	 * Add tooltips to menu items.
	 */
	function initTooltips() {
		const menuItems = document.querySelectorAll('#adminmenu li.menu-top');

		menuItems.forEach(function(item) {
			const link = item.querySelector('a.menu-top');
			const menuName = item.querySelector('.wp-menu-name');

			if (link && menuName) {
				const tooltipText = menuName.textContent.trim();
				link.setAttribute('aria-label', tooltipText);
			}
		});
	}

	/**
	 * Click to toggle submenu - Submenu stays open (static).
	 */
	function initSubmenuToggle() {
		const menuItems = document.querySelectorAll('#adminmenu li.menu-top.wp-has-submenu');
		let activeItem = null; // Track which menu is open

		menuItems.forEach(function(item) {
			const link = item.querySelector('a.menu-top');

			if (link) {
				// Click handler
				link.addEventListener('click', function(e) {
					e.preventDefault(); // ALWAYS prevent - toggle submenu

					const isOpen = item.classList.contains('opensub');

					if (isOpen) {
						// Click same menu → close it
						item.classList.remove('opensub');
						item.setAttribute('data-tempone-locked', 'false');
						activeItem = null;

						// Always remove body class when closing submenu
						document.body.classList.remove('has-open-submenu');
					} else {
						// Click different menu → close others, open this
						menuItems.forEach(function(otherItem) {
							otherItem.classList.remove('opensub');
							otherItem.setAttribute('data-tempone-locked', 'false');
						});

						item.classList.add('opensub');
						item.setAttribute('data-tempone-locked', 'true');
						activeItem = item;
						document.body.classList.add('has-open-submenu');
					}
				});

				// MutationObserver to prevent class removal by WordPress
				const observer = new MutationObserver(function(mutations) {
					mutations.forEach(function(mutation) {
						if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
							const isLocked = item.getAttribute('data-tempone-locked') === 'true';
							const hasOpensub = item.classList.contains('opensub');

							// If locked but opensub was removed → re-add it
							if (isLocked && !hasOpensub) {
								item.classList.add('opensub');
							}
						}
					});
				});

				// Start observing
				observer.observe(item, {
					attributes: true,
					attributeFilter: ['class']
				});
			}
		});
	}

	/**
	 * Initialize everything.
	 */
	function init() {
		initTooltips();
		initSubmenuToggle();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
