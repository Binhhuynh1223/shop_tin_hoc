<!-- Slider -->
<aside class="rounded-2xl bg-white p-4 shadow">
    <div class="slider-container" style="overflow: hidden; position: relative; height: 280px;">
        <div class="slider-wrapper" style="display: flex; transition: transform 0.5s ease;">
            <div class="slide" style="min-width: 100%;">
                <img src="/images/sliders/1.png" alt="Slide 1" class="w-full h-[260px] object-cover rounded-lg shadow-sm">
            </div>
            <div class="slide" style="min-width: 100%;">
                <img src="/images/sliders/2.jpg" alt="Slide 2" class="w-full h-[260px] object-cover rounded-lg shadow-sm">
            </div>
            <div class="slide" style="min-width: 100%;">
                <img src="/images/sliders/3.jpeg" alt="Slide 3" class="w-full h-[260px] object-cover rounded-lg shadow-sm">
            </div>
            <div class="slide" style="min-width: 100%;">
                <img src="/images/sliders/4.jpg" alt="Slide 4" class="w-full h-[260px] object-cover rounded-lg shadow-sm">
            </div>
        </div>
        <!-- Slider Controls -->
        <div class="slider-controls absolute -bottom-2 left-0 right-0 flex justify-center gap-3">
            <button class="slider-dot w-2.5 h-2.5 rounded-full bg-black/20 transition-colors" data-index="0"></button>
            <button class="slider-dot w-2.5 h-2.5 rounded-full bg-black/20 transition-colors" data-index="1"></button>
            <button class="slider-dot w-2.5 h-2.5 rounded-full bg-black/20 transition-colors" data-index="2"></button>
        </div>
        <!-- Navigation Arrows -->
        <button class="slider-nav prev absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/20 hover:bg-black/30 flex items-center justify-center transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </button>
        <button class="slider-nav next absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/20 hover:bg-black/30 flex items-center justify-center transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
        </button>
    </div>
</aside>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliderWrapper = document.querySelector('.slider-wrapper');
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        const prevBtn = document.querySelector('.slider-nav.prev');
        const nextBtn = document.querySelector('.slider-nav.next');
        const sliderContainer = document.querySelector('.slider-container');

        let currentIndex = 1;
        let intervalId;

        // Clone slide đầu và cuối để tạo hiệu ứng mượt
        const firstClone = slides[0].cloneNode(true);
        const lastClone = slides[slides.length - 1].cloneNode(true);

        sliderWrapper.appendChild(firstClone);
        sliderWrapper.insertBefore(lastClone, slides[0]);

        const allSlides = document.querySelectorAll('.slide');
        const totalSlides = allSlides.length;

        // Thiết lập vị trí bắt đầu
        sliderWrapper.style.transform = `translateX(-100%)`;

        function updateDots(index) {
            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-white', i === index - 1);
                dot.classList.toggle('bg-white/50', i !== index - 1);
            });
        }

        function moveTo(index) {
            sliderWrapper.style.transition = 'transform 0.6s ease';
            sliderWrapper.style.transform = `translateX(-${index * 100}%)`;
            currentIndex = index;
            updateDots(index);
        }

        function nextSlide() {
            if (currentIndex >= totalSlides - 1) return;
            currentIndex++;
            moveTo(currentIndex);
        }

        function prevSlide() {
            if (currentIndex <= 0) return;
            currentIndex--;
            moveTo(currentIndex);
        }

        sliderWrapper.addEventListener('transitionend', () => {
            if (allSlides[currentIndex].isSameNode(firstClone)) {
                sliderWrapper.style.transition = 'none';
                currentIndex = 1;
                sliderWrapper.style.transform = `translateX(-100%)`;
            }
            if (allSlides[currentIndex].isSameNode(lastClone)) {
                sliderWrapper.style.transition = 'none';
                currentIndex = totalSlides - 2;
                sliderWrapper.style.transform = `translateX(-${currentIndex * 100}%)`;
            }
        });

        // Dots click
        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                moveTo(i + 1);
                resetInterval();
            });
        });

        // Nút điều hướng
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetInterval();
        });

        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetInterval();
        });

        function startInterval() {
            intervalId = setInterval(nextSlide, 3000);
        }

        function resetInterval() {
            clearInterval(intervalId);
            startInterval();
        }

        startInterval();

        sliderContainer.addEventListener('mouseenter', () => clearInterval(intervalId));
        sliderContainer.addEventListener('mouseleave', startInterval);

        updateDots(currentIndex);
    });
</script>