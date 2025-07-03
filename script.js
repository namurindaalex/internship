// Presentation Control Script
class PresentationController {
    constructor() {
        this.currentSlideIndex = 0;
        this.slides = document.querySelectorAll('.slide');
        this.totalSlides = this.slides.length;
        this.isTransitioning = false;
        
        this.init();
    }
    
    init() {
        // Update total slides indicator
        document.getElementById('totalSlides').textContent = this.totalSlides;
        
        // Add event listeners
        this.addEventListeners();
        
        // Initialize first slide
        this.showSlide(0);
        
        // Preload images
        this.preloadImages();
    }
    
    addEventListeners() {
        // Keyboard navigation
        document.addEventListener('keydown', (event) => {
            if (this.isTransitioning) return;
            
            switch (event.key) {
                case 'ArrowRight':
                case ' ':
                case 'Enter':
                    event.preventDefault();
                    this.nextSlide();
                    break;
                case 'ArrowLeft':
                    event.preventDefault();
                    this.previousSlide();
                    break;
                case 'Home':
                    event.preventDefault();
                    this.goToSlide(0);
                    break;
                case 'End':
                    event.preventDefault();
                    this.goToSlide(this.totalSlides - 1);
                    break;
                case 'Escape':
                    event.preventDefault();
                    this.toggleFullscreen();
                    break;
            }
        });
        
        // Touch/swipe support for mobile
        let touchStartX = 0;
        let touchStartY = 0;
        
        document.addEventListener('touchstart', (event) => {
            touchStartX = event.touches[0].clientX;
            touchStartY = event.touches[0].clientY;
        });
        
        document.addEventListener('touchend', (event) => {
            if (this.isTransitioning) return;
            
            const touchEndX = event.changedTouches[0].clientX;
            const touchEndY = event.changedTouches[0].clientY;
            
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            
            // Minimum swipe distance
            const minSwipeDistance = 50;
            
            // Determine swipe direction
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > minSwipeDistance) {
                if (deltaX > 0) {
                    this.previousSlide();
                } else {
                    this.nextSlide();
                }
            }
        });
        
        // Mouse wheel support
        document.addEventListener('wheel', (event) => {
            if (this.isTransitioning) return;
            
            event.preventDefault();
            
            if (event.deltaY > 0) {
                this.nextSlide();
            } else {
                this.previousSlide();
            }
        }, { passive: false });
        
        // Prevent context menu on right click
        document.addEventListener('contextmenu', (event) => {
            event.preventDefault();
        });
    }
    
    showSlide(index) {
        if (index < 0 || index >= this.totalSlides || this.isTransitioning) return;
        
        this.isTransitioning = true;
        
        // Update slide classes
        this.slides.forEach((slide, i) => {
            slide.classList.remove('active', 'prev');
            if (i === index) {
                slide.classList.add('active');
            } else if (i < index) {
                slide.classList.add('prev');
            }
        });
        
        // Update current slide indicator
        document.getElementById('currentSlide').textContent = index + 1;
        
        // Reset and restart animations
        this.resetAnimations(index);
        
        // Update current index
        this.currentSlideIndex = index;
        
        // Enable transitions after animation completes
        setTimeout(() => {
            this.isTransitioning = false;
        }, 800);
        
        // Track slide change for analytics (if needed)
        this.trackSlideChange(index);
    }
    
    resetAnimations(slideIndex) {
        const activeSlide = this.slides[slideIndex];
        const animatedElements = activeSlide.querySelectorAll(`
            .hero-content,
            .info-card,
            .vision-card,
            .mission-card,
            .learning-card,
            .tech-card,
            .timeline-item,
            .assembly-section,
            .station-card,
            .qc-category,
            .skills-section,
            .challenge-card,
            .ack-item,
            .skill-item
        `);
        
        animatedElements.forEach((element, index) => {
            element.style.animation = 'none';
            element.offsetHeight; // Trigger reflow
            
            // Add staggered animation delays
            setTimeout(() => {
                element.style.animation = null;
                element.style.animationDelay = `${index * 0.1}s`;
            }, 50);
        });
    }
    
    nextSlide() {
        if (this.currentSlideIndex < this.totalSlides - 1) {
            this.showSlide(this.currentSlideIndex + 1);
        }
    }
    
    previousSlide() {
        if (this.currentSlideIndex > 0) {
            this.showSlide(this.currentSlideIndex - 1);
        }
    }
    
    goToSlide(index) {
        this.showSlide(index);
    }
    
    toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.log(`Error attempting to enable fullscreen: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    }
    
    preloadImages() {
        const images = [
            'spiro/Picture1.jpg',
            'spiro/pic2.JPG',
            'spiro/pic3.jpg',
            'spiro/pic4.jpg',
            'spiro/pic5.JPG',
            'spiro/pic6.jpg',
            'spiro/pic7.JPG',
            'spiro/pic8.jpg',
            'spiro/pic9.jpg',
            'spiro/pic12.png',
            'spiro/pic13.png',
            'spiro/pic14.png',
            'spiro/internee.jpg',
            'spiro/supervisor1.jpeg'
        ];
        
        images.forEach(src => {
            const img = new Image();
            img.src = src;
        });
    }
    
    trackSlideChange(index) {
        // This can be used for analytics or progress tracking
        console.log(`Slide changed to: ${index + 1}`);
    }
    
    // Public methods for button controls
    getCurrentSlide() {
        return this.currentSlideIndex;
    }
    
    getTotalSlides() {
        return this.totalSlides;
    }
}

// Auto-play functionality (optional)
class AutoPlay {
    constructor(presentation, interval = 10000) {
        this.presentation = presentation;
        this.interval = interval;
        this.timer = null;
        this.isPlaying = false;
    }
    
    start() {
        if (this.isPlaying) return;
        
        this.isPlaying = true;
        this.timer = setInterval(() => {
            if (this.presentation.getCurrentSlide() < this.presentation.getTotalSlides() - 1) {
                this.presentation.nextSlide();
            } else {
                this.stop();
            }
        }, this.interval);
    }
    
    stop() {
        if (!this.isPlaying) return;
        
        this.isPlaying = false;
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }
    
    toggle() {
        if (this.isPlaying) {
            this.stop();
        } else {
            this.start();
        }
    }
}

// Progress indicator
class ProgressIndicator {
    constructor(presentation) {
        this.presentation = presentation;
        this.createProgressBar();
    }
    
    createProgressBar() {
        const progressContainer = document.createElement('div');
        progressContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            z-index: 1001;
        `;
        
        const progressBar = document.createElement('div');
        progressBar.id = 'progress-bar';
        progressBar.style.cssText = `
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s ease;
        `;
        
        progressContainer.appendChild(progressBar);
        document.body.appendChild(progressContainer);
        
        this.updateProgress();
    }
    
    updateProgress() {
        const progressBar = document.getElementById('progress-bar');
        const progress = ((this.presentation.getCurrentSlide() + 1) / this.presentation.getTotalSlides()) * 100;
        
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }
    }
}

// Initialize presentation when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Create presentation controller
    const presentation = new PresentationController();
    
    // Create progress indicator
    const progressIndicator = new ProgressIndicator(presentation);
    
    // Create auto-play controller (optional)
    const autoPlay = new AutoPlay(presentation);
    
    // Global function for navigation buttons
    window.nextSlide = () => {
        presentation.nextSlide();
        progressIndicator.updateProgress();
    };
    
    window.previousSlide = () => {
        presentation.previousSlide();
        progressIndicator.updateProgress();
    };
    
    window.goToSlide = (index) => {
        presentation.goToSlide(index);
        progressIndicator.updateProgress();
    };
    
    // Listen for slide changes to update progress
    const originalShowSlide = presentation.showSlide.bind(presentation);
    presentation.showSlide = function(index) {
        originalShowSlide(index);
        setTimeout(() => {
            progressIndicator.updateProgress();
        }, 100);
    };
    
    // Add keyboard shortcuts help
    document.addEventListener('keydown', (event) => {
        if (event.key === 'h' || event.key === 'H') {
            showHelp();
        }
    });
    
    function showHelp() {
        const helpText = `
        Keyboard Shortcuts:
        ← / → : Navigate slides
        Space / Enter : Next slide
        Home : First slide
        End : Last slide
        Esc : Toggle fullscreen
        H : Show this help
        `;
        
        alert(helpText);
    }
    
    // Add touch indicators for mobile
    if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
    }
    
    // Performance monitoring
    const performanceMonitor = {
        start: performance.now(),
        
        logMetric: function(name, value) {
            console.log(`${name}: ${value}ms`);
        },
        
        measureSlideTransition: function() {
            const start = performance.now();
            return () => {
                const end = performance.now();
                this.logMetric('Slide Transition', end - start);
            };
        }
    };
    
    // Export for external use
    window.presentationController = presentation;
    window.autoPlay = autoPlay;
    window.performanceMonitor = performanceMonitor;
    
    // Initialize with first slide
    console.log('Spiro Uganda Presentation initialized successfully!');
    console.log('Press H for keyboard shortcuts');
});