document.addEventListener('DOMContentLoaded', () => {
	const menuToggle = document.querySelector('[data-menu-toggle]');
	const headerMenus = document.querySelector('[data-header-menus]');

	if (menuToggle && headerMenus) {
		menuToggle.addEventListener('click', () => {
			const isOpen = headerMenus.classList.toggle('is-open');
			menuToggle.setAttribute('aria-expanded', String(isOpen));
		});
	}

	const shareButtons = document.querySelectorAll('[data-share]');
	shareButtons.forEach((button) => {
		button.addEventListener('click', async (event) => {
			event.preventDefault();
			const url = button.getAttribute('href');
			if (navigator.share) {
				try {
					await navigator.share({
						url,
						title: document.title,
					});
				} catch (error) {
					window.open(url, '_blank', 'noopener');
				}
			} else {
				window.open(url, '_blank', 'noopener');
			}
		});
	});

	// Featured Carousel - Desktop Only
	const carousel = document.getElementById('featuredCarousel');
	if (carousel && carousel.children.length > 0) {
		let currentSlide = 0;
		const slides = carousel.querySelectorAll('.carousel-slide');
		const totalSlides = slides.length;
		const prevBtn = document.querySelector('.carousel-prev');
		const nextBtn = document.querySelector('.carousel-next');
		const indicators = document.querySelectorAll('.carousel-indicator');

		if (totalSlides > 0) {
			function goToSlide(index) {
				currentSlide = ((index % totalSlides) + totalSlides) % totalSlides;
				carousel.style.transform = `translateX(-${currentSlide * 100}%)`;

				// Update indicators
				indicators.forEach((indicator, i) => {
					if (i === currentSlide) {
						indicator.classList.add('active', 'bg-white');
						indicator.classList.remove('bg-white/60');
					} else {
						indicator.classList.remove('active', 'bg-white');
						indicator.classList.add('bg-white/60');
					}
				});
			}

			if (prevBtn) {
				prevBtn.addEventListener('click', () => goToSlide(currentSlide - 1));
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', () => goToSlide(currentSlide + 1));
			}

			indicators.forEach((indicator, index) => {
				indicator.addEventListener('click', () => goToSlide(index));
			});

			// Auto-play carousel every 5 seconds
			setInterval(() => goToSlide(currentSlide + 1), 5000);
		}
	}

	// Category Featured Carousel - Seamless infinite auto-sliding
	const categoryCarousels = document.querySelectorAll('[data-category-carousel]');
	categoryCarousels.forEach((track) => {
		const originalSlides = Array.from(track.children);
		const totalSlides = originalSlides.length;

		if (totalSlides === 0) return;

		// Clone ALL slides 2x (before + after original) for seamless loop
		const clonesBefore = originalSlides.map(slide => slide.cloneNode(true));
		const clonesAfter = originalSlides.map(slide => slide.cloneNode(true));

		// Insert clones
		clonesBefore.reverse().forEach(clone => track.insertBefore(clone, track.firstChild));
		clonesAfter.forEach(clone => track.appendChild(clone));

		let currentTranslate = 0;
		let isDragging = false;
		let startPos = 0;
		let prevTranslate = 0;
		let autoSlideInterval;

		// Get width of one complete set of slides
		function getSetWidth() {
			let totalWidth = 0;
			const gap = parseFloat(getComputedStyle(track).gap) || 24;

			// Calculate from middle set (original slides)
			for (let i = totalSlides; i < totalSlides * 2; i++) {
				totalWidth += track.children[i].offsetWidth + gap;
			}

			return totalWidth;
		}

		// Set initial position to middle set
		function resetToMiddle() {
			const setWidth = getSetWidth();
			currentTranslate = -setWidth;
			prevTranslate = currentTranslate;
			track.style.transition = 'none';
			track.style.transform = `translateX(${currentTranslate}px)`;
		}

		// Auto-slide: move by one slide width
		function autoSlide() {
			const gap = parseFloat(getComputedStyle(track).gap) || 24;
			const slideWidth = track.children[totalSlides].offsetWidth + gap;

			currentTranslate -= slideWidth;

			track.style.transition = 'transform 0.6s ease-in-out';
			track.style.transform = `translateX(${currentTranslate}px)`;

			// Check if we need to reset
			const setWidth = getSetWidth();
			if (Math.abs(currentTranslate) >= setWidth * 2) {
				// Reset after animation completes
				setTimeout(() => {
					resetToMiddle();
				}, 600);
			}
		}

		// Initialize
		resetToMiddle();

		// Handle window resize
		let resizeTimer;
		window.addEventListener('resize', () => {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(() => {
				resetToMiddle();
			}, 250);
		});

		// Touch/Mouse events for manual drag
		function touchStart(event) {
			isDragging = true;
			startPos = event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
			track.style.transition = 'none';

			// Pause auto-slide during drag
			if (autoSlideInterval) {
				clearInterval(autoSlideInterval);
			}
		}

		function touchMove(event) {
			if (!isDragging) return;
			event.preventDefault();
			const currentPosition = event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
			currentTranslate = prevTranslate + currentPosition - startPos;
			track.style.transform = `translateX(${currentTranslate}px)`;
		}

		function touchEnd() {
			if (!isDragging) return;

			isDragging = false;
			track.style.transition = 'transform 0.6s ease-in-out';

			// Snap to nearest position
			const setWidth = getSetWidth();
			if (Math.abs(currentTranslate) >= setWidth * 2) {
				resetToMiddle();
			} else {
				track.style.transform = `translateX(${currentTranslate}px)`;
				prevTranslate = currentTranslate;
			}

			// Resume auto-slide
			startAutoSlide();
		}

		function startAutoSlide() {
			if (autoSlideInterval) {
				clearInterval(autoSlideInterval);
			}
			autoSlideInterval = setInterval(autoSlide, 4000);
		}

		// Attach events
		track.addEventListener('mousedown', touchStart);
		track.addEventListener('mousemove', touchMove);
		track.addEventListener('mouseup', touchEnd);
		track.addEventListener('mouseleave', touchEnd);

		track.addEventListener('touchstart', touchStart, { passive: false });
		track.addEventListener('touchmove', touchMove, { passive: false });
		track.addEventListener('touchend', touchEnd);

		// Note: Window resize already handled by global resize listener above (line 144-150)
		// No need for duplicate resize handler

		// Start auto-slide
		startAutoSlide();
	});

	// Flexi Design 5: Auto-sliding Carousel with Mouse Drag
	const flexiCarousels = document.querySelectorAll('[data-flexi-carousel]');
	flexiCarousels.forEach((track) => {
		const originalSlides = Array.from(track.children);
		const totalSlides = originalSlides.length;

		if (totalSlides === 0) return;

		// Clone ALL slides 2x (before + after original) for seamless loop
		const clonesBefore = originalSlides.map(slide => slide.cloneNode(true));
		const clonesAfter = originalSlides.map(slide => slide.cloneNode(true));

		// Insert clones
		clonesBefore.reverse().forEach(clone => track.insertBefore(clone, track.firstChild));
		clonesAfter.forEach(clone => track.appendChild(clone));

		let currentTranslate = 0;
		let isDragging = false;
		let startPos = 0;
		let prevTranslate = 0;
		let autoSlideInterval;

		// Get width of one complete set of slides
		function getSetWidth() {
			let totalWidth = 0;
			const gap = parseFloat(getComputedStyle(track).gap) || 24;

			// Calculate from middle set (original slides)
			for (let i = totalSlides; i < totalSlides * 2; i++) {
				totalWidth += track.children[i].offsetWidth + gap;
			}

			return totalWidth;
		}

		// Set initial position to middle set
		function resetToMiddle() {
			const setWidth = getSetWidth();
			currentTranslate = -setWidth;
			prevTranslate = currentTranslate;
			track.style.transition = 'none';
			track.style.transform = `translateX(${currentTranslate}px)`;
		}

		// Auto-slide: move by one slide width
		function autoSlide() {
			const gap = parseFloat(getComputedStyle(track).gap) || 24;
			const slideWidth = track.children[totalSlides].offsetWidth + gap;

			currentTranslate -= slideWidth;

			track.style.transition = 'transform 0.6s ease-in-out';
			track.style.transform = `translateX(${currentTranslate}px)`;

			prevTranslate = currentTranslate;

			// Check if we need to reset
			const setWidth = getSetWidth();
			if (Math.abs(currentTranslate) >= setWidth * 2) {
				// Reset after animation completes
				setTimeout(() => {
					resetToMiddle();
				}, 600);
			}
		}

		// Initialize
		resetToMiddle();

		// Touch/Mouse events for manual drag
		function touchStart(event) {
			isDragging = true;
			startPos = event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
			track.style.transition = 'none';

			// Pause auto-slide during drag
			if (autoSlideInterval) {
				clearInterval(autoSlideInterval);
			}
		}

		function touchMove(event) {
			if (!isDragging) return;
			event.preventDefault();
			const currentPosition = event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
			currentTranslate = prevTranslate + currentPosition - startPos;
			track.style.transform = `translateX(${currentTranslate}px)`;
		}

		function touchEnd() {
			if (!isDragging) return;
			isDragging = false;

			// Check if we need to reset to middle
			const setWidth = getSetWidth();
			if (Math.abs(currentTranslate) >= setWidth * 2 || currentTranslate > 0) {
				resetToMiddle();
			} else {
				prevTranslate = currentTranslate;
			}

			// Resume auto-slide
			startAutoSlide();
		}

		function startAutoSlide() {
			if (autoSlideInterval) {
				clearInterval(autoSlideInterval);
			}
			autoSlideInterval = setInterval(autoSlide, 4000);
		}

		// Attach events
		track.addEventListener('mousedown', touchStart);
		track.addEventListener('mousemove', touchMove);
		track.addEventListener('mouseup', touchEnd);
		track.addEventListener('mouseleave', touchEnd);

		track.addEventListener('touchstart', touchStart, { passive: false });
		track.addEventListener('touchmove', touchMove, { passive: false });
		track.addEventListener('touchend', touchEnd);

		// Handle window resize
		let resizeTimer;
		window.addEventListener('resize', () => {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(() => {
				resetToMiddle();
			}, 250);
		});

		// Start auto-slide
		startAutoSlide();
	});
});
