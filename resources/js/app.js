import ApexCharts from 'apexcharts';

window.ApexCharts = ApexCharts;

// Auto-skeleton for lazy loaded images
function initImageSkeletons() {
    document.querySelectorAll('img[loading="lazy"]').forEach((img) => {
        if (!img.classList.contains('img-skeleton')) {
            img.classList.add('img-skeleton');
        }
        if (img.complete && img.naturalWidth > 0) {
            img.classList.add('img-loaded');
        } else {
            img.addEventListener('load', () => img.classList.add('img-loaded'), { once: true });
            img.addEventListener('error', () => img.classList.add('img-loaded'), { once: true });
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initImageSkeletons);
} else {
    initImageSkeletons();
}

window.initImageSkeletons = initImageSkeletons;


