const menuToggle = document.querySelector('.menu-toggle');
const mainNav = document.querySelector('#main-nav');

if (menuToggle && mainNav) {
    menuToggle.addEventListener('click', () => {
        const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';

        menuToggle.setAttribute('aria-expanded', String(!isOpen));
        menuToggle.setAttribute('aria-label', isOpen ? 'Abrir menu' : 'Fechar menu');
        mainNav.classList.toggle('is-open', !isOpen);
    });
}

const slideshow = document.querySelector('.hero-slideshow');

if (slideshow) {
    const track = slideshow.querySelector('.slideshow-track');
    const slides = Array.from(slideshow.querySelectorAll('.slide'));
    const dots = slideshow.querySelectorAll('.slide-dot');
    const previousButton = slideshow.querySelector('.slide-arrow-prev');
    const nextButton = slideshow.querySelector('.slide-arrow-next');
    let activeSlide = 0;
    let trackPosition = 1;
    let dragStartX = null;
    let dragStartY = null;
    let dragDistance = 0;
    let isDragging = false;
    let isTransitioning = false;
    let autoplayTimer;

    const firstClone = slides[0].cloneNode(true);
    const lastClone = slides[slides.length - 1].cloneNode(true);

    firstClone.classList.remove('is-active');
    lastClone.classList.remove('is-active');
    firstClone.setAttribute('aria-hidden', 'true');
    lastClone.setAttribute('aria-hidden', 'true');
    track.append(firstClone);
    track.prepend(lastClone);

    const moveTrack = (offset = 0, animate = true) => {
        track.classList.toggle('is-dragging', !animate);
        track.style.transform = `translate3d(calc(${-trackPosition * 100}% + ${offset}px), 0, 0)`;
    };

    const updateActiveState = () => {
        slides.forEach((slide, slideIndex) => {
            const isActive = slideIndex === activeSlide;

            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', String(!isActive));
        });

        dots.forEach((dot, dotIndex) => {
            const isActive = dotIndex === activeSlide;

            dot.classList.toggle('is-active', isActive);

            if (isActive) {
                dot.setAttribute('aria-current', 'true');
            } else {
                dot.removeAttribute('aria-current');
            }
        });
    };

    const showSlide = (index) => {
        if (isTransitioning) {
            return;
        }

        const direction = index > activeSlide ? 1 : -1;
        const nextSlide = (index + slides.length) % slides.length;

        if (nextSlide === activeSlide) {
            return;
        }

        if (activeSlide === slides.length - 1 && nextSlide === 0 && direction === 1) {
            trackPosition = slides.length + 1;
        } else if (activeSlide === 0 && nextSlide === slides.length - 1 && direction === -1) {
            trackPosition = 0;
        } else {
            trackPosition = nextSlide + 1;
        }

        activeSlide = nextSlide;
        isTransitioning = true;
        updateActiveState();
        moveTrack();
    };

    const startAutoplay = () => {
        clearInterval(autoplayTimer);
        autoplayTimer = setInterval(() => {
            showSlide(activeSlide + 1);
        }, 10000);
    };

    track.addEventListener('transitionend', (event) => {
        if (event.propertyName !== 'transform') {
            return;
        }

        if (trackPosition === 0) {
            trackPosition = slides.length;
            moveTrack(0, false);
        } else if (trackPosition === slides.length + 1) {
            trackPosition = 1;
            moveTrack(0, false);
        }

        isTransitioning = false;
        slideshow.classList.remove('is-interacting');
    });

    previousButton?.addEventListener('click', () => {
        showSlide(activeSlide - 1);
        startAutoplay();
    });

    nextButton?.addEventListener('click', () => {
        showSlide(activeSlide + 1);
        startAutoplay();
    });

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            startAutoplay();
        });
    });

    slideshow.addEventListener('pointerdown', (event) => {
        if (event.target.closest('button') || isTransitioning) {
            return;
        }

        clearInterval(autoplayTimer);
        dragStartX = event.clientX;
        dragStartY = event.clientY;
        dragDistance = 0;
        isDragging = true;
        slideshow.classList.add('is-interacting');
        slideshow.setPointerCapture(event.pointerId);
        moveTrack(0, false);
    });

    slideshow.addEventListener('dragstart', (event) => {
        event.preventDefault();
    });

    slideshow.addEventListener('pointermove', (event) => {
        if (!isDragging) {
            return;
        }

        dragDistance = event.clientX - dragStartX;
        moveTrack(dragDistance, false);
    });

    slideshow.addEventListener('pointerup', (event) => {
        if (!isDragging || dragStartX === null || dragStartY === null) {
            return;
        }

        const distanceX = event.clientX - dragStartX;
        const distanceY = event.clientY - dragStartY;
        const dragThreshold = slideshow.clientWidth * 0.4;
        const shouldChangeSlide = Math.abs(distanceX) >= dragThreshold && Math.abs(distanceX) > Math.abs(distanceY);

        isDragging = false;

        if (shouldChangeSlide) {
            showSlide(activeSlide + (distanceX < 0 ? 1 : -1));
        } else if (Math.abs(distanceX) >= 1) {
            isTransitioning = true;
            moveTrack();
        } else {
            moveTrack();
            slideshow.classList.remove('is-interacting');
        }

        dragStartX = null;
        dragStartY = null;
        dragDistance = 0;
        startAutoplay();
    });

    slideshow.addEventListener('pointercancel', () => {
        if (isDragging) {
            isDragging = false;

            if (Math.abs(dragDistance) >= 1) {
                isTransitioning = true;
                moveTrack();
            } else {
                moveTrack();
                slideshow.classList.remove('is-interacting');
            }

            startAutoplay();
        }

        dragStartX = null;
        dragStartY = null;
        dragDistance = 0;
    });

    updateActiveState();
    moveTrack(0, false);
    startAutoplay();
}
