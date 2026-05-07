/**
 * index1.js - Unified Amazon Carousel Logic
 */
document.addEventListener('DOMContentLoaded', function() {
    // --- 1. HERO SLIDER ---
    const heroSlider = document.querySelector('.hero-slider');
    const heroSlides = document.querySelectorAll('.hero-slide');
    if (heroSlider && heroSlides.length > 1) {
        let currentSlide = 0;
        window.moveHero = function(dir) {
            currentSlide = (currentSlide + dir + heroSlides.length) % heroSlides.length;
            heroSlider.style.transform = `translateX(-${currentSlide * 100}%)`;
        };
        setInterval(() => moveHero(1), 10000);
    }

    // --- 2. SHOVELER (CAROUSEL) LOGIC ---
    function updateButtons(list) {
        const container = list.closest('.shoveler-container');
        if (!container) return;
        const btnLeft = container.querySelector('.btn-left');
        const btnRight = container.querySelector('.btn-right');
        
        if (btnLeft) {
            list.scrollLeft <= 10 ? btnLeft.classList.add('disabled') : btnLeft.classList.remove('disabled');
        }
        if (btnRight) {
            const isEnd = list.scrollLeft + list.clientWidth >= list.scrollWidth - 10;
            isEnd ? btnRight.classList.add('disabled') : btnRight.classList.remove('disabled');
        }
    }

    const shovelers = document.querySelectorAll('.shoveler-list');
    shovelers.forEach(list => {
        updateButtons(list);
        list.addEventListener('scroll', () => updateButtons(list));
    });

    window.scrollShoveler = function(dir, btn) {
        const list = btn.closest('.shoveler-container').querySelector('.shoveler-list');
        if (!list) return;

        const isAtStart = list.scrollLeft <= 5;
        const isAtEnd = list.scrollLeft + list.clientWidth >= list.scrollWidth - 15;

        // Trigger Bounce Effect if at boundary
        if ((dir === -1 && isAtStart) || (dir === 1 && isAtEnd)) {
            const bounceClass = dir === -1 ? 'bounce-left' : 'bounce-right';
            list.classList.remove('bounce-left', 'bounce-right'); // Reset
            void list.offsetWidth; // Trigger reflow
            list.classList.add(bounceClass);
            setTimeout(() => list.classList.remove(bounceClass), 300);
            return;
        }

        // Normal Scrolling
        const scrollAmount = list.clientWidth * 0.8;
        list.scrollBy({ left: scrollAmount * dir, behavior: 'smooth' });
        setTimeout(() => updateButtons(list), 500);
    };
});

/**
 * applySort - Reloads page with new sort parameter while preserving search/category
 * @param {string} val 
 */
function applySort(val) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', val);
    window.location.search = urlParams.toString();
}
