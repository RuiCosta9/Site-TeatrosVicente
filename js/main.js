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
    const slides = slideshow.querySelectorAll('.slide');
    const dots = slideshow.querySelectorAll('.slide-dot');
    const previousButton = slideshow.querySelector('.slide-arrow-prev');
    const nextButton = slideshow.querySelector('.slide-arrow-next');
    let activeSlide = 0;

    const showSlide = (index) => {
        activeSlide = (index + slides.length) % slides.length;

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

    previousButton?.addEventListener('click', () => {
        showSlide(activeSlide - 1);
    });

    nextButton?.addEventListener('click', () => {
        showSlide(activeSlide + 1);
    });

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
        });
    });

    setInterval(() => {
        showSlide(activeSlide + 1);
    }, 10000);
}
