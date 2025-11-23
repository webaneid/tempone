/**
 * Drag-to-scroll for primary navigation menu.
 *
 * @package tempone
 */

document.addEventListener('DOMContentLoaded', function () {
	const nav = document.querySelector('.site-header__primary-nav');

	if (!nav) {
		return;
	}

	let isDown = false;
	let startX;
	let scrollLeft;

	nav.addEventListener('mousedown', function (e) {
		isDown = true;
		nav.classList.add('is-dragging');
		startX = e.pageX - nav.offsetLeft;
		scrollLeft = nav.scrollLeft;
		nav.style.cursor = 'grabbing';
	});

	nav.addEventListener('mouseleave', function () {
		isDown = false;
		nav.classList.remove('is-dragging');
		nav.style.cursor = 'grab';
	});

	nav.addEventListener('mouseup', function () {
		isDown = false;
		nav.classList.remove('is-dragging');
		nav.style.cursor = 'grab';
	});

	nav.addEventListener('mousemove', function (e) {
		if (!isDown) return;
		e.preventDefault();
		const x = e.pageX - nav.offsetLeft;
		const walk = (x - startX) * 2; // Scroll speed multiplier
		nav.scrollLeft = scrollLeft - walk;
	});

	// Set initial cursor
	nav.style.cursor = 'grab';

	// Prevent link clicks during drag
	let clickStart = 0;
	nav.addEventListener('mousedown', function () {
		clickStart = Date.now();
	});

	nav.addEventListener('click', function (e) {
		const clickDuration = Date.now() - clickStart;
		// If drag duration > 200ms, it's a drag not a click
		if (clickDuration > 200 && isDown) {
			e.preventDefault();
		}
	}, true);
});
