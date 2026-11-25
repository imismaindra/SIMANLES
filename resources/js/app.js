import ApexCharts from 'apexcharts';

window.ApexCharts = ApexCharts;

// Auto-skeleton for lazy loaded images with fail-safe fallback
function initImageSkeletons() {
    document.querySelectorAll('img[loading="lazy"]').forEach((img) => {
        if (!img.classList.contains('img-skeleton')) {
            img.classList.add('img-skeleton');
        }

        const removeShimmer = () => {
            if (img.previousElementSibling && img.previousElementSibling.classList.contains('skeleton-shimmer')) {
                img.previousElementSibling.remove();
            }
        };

        const handleLoaded = () => {
            img.classList.remove('opacity-0');
            img.classList.add('img-loaded');
            removeShimmer();
        };

        const handleError = () => {
            removeShimmer();
            img.classList.remove('opacity-0');
            const parent = img.parentElement;
            if (parent && parent.classList.contains('relative') && parent.nextElementSibling) {
                parent.style.display = 'none';
                parent.nextElementSibling.classList.remove('hidden');
            }
        };

        if (img.complete) {
            if (img.naturalWidth > 0) {
                handleLoaded();
            } else {
                handleError();
            }
        } else {
            img.addEventListener('load', handleLoaded, { once: true });
            img.addEventListener('error', handleError, { once: true });
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initImageSkeletons);
} else {
    initImageSkeletons();
}

window.initImageSkeletons = initImageSkeletons;


