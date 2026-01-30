// Lazy Loading Directive for Images
const lazyload = {
  mounted(el, binding) {
    const imageUrl = binding.value;
    const placeholder = el.dataset.placeholder || '/images/placeholder.jpg';

    // Set placeholder initially
    el.src = placeholder;
    el.classList.add('lazy-loading');

    // Create Intersection Observer
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // Load the actual image
          const img = new Image();
          img.onload = () => {
            el.src = imageUrl;
            el.classList.remove('lazy-loading');
            el.classList.add('lazy-loaded');
          };
          img.onerror = () => {
            el.src = '/images/error.jpg';
            el.classList.remove('lazy-loading');
            el.classList.add('lazy-error');
          };
          img.src = imageUrl;

          // Stop observing once loaded
          observer.unobserve(el);
        }
      });
    }, {
      rootMargin: '50px', // Start loading 50px before entering viewport
      threshold: 0.01,
    });

    observer.observe(el);

    // Store observer for cleanup
    el._lazyObserver = observer;
  },

  unmounted(el) {
    // Cleanup observer
    if (el._lazyObserver) {
      el._lazyObserver.disconnect();
    }
  },
};

export default lazyload;
