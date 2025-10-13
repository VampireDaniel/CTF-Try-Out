document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('keydown', (event) => {
        const currentUrl = new URL(window.location.href);
        const currentProductId = currentUrl.searchParams.get('product_id');

        if (!currentProductId) return;

        if (event.key === 'ArrowLeft') {
            const prevLink = document.querySelector('.navigation-buttons a:first-child');
            if (prevLink) {
                window.location.href = prevLink.href;
            }
        } else if (event.key === 'ArrowRight') {
            const nextLink = document.querySelector('.navigation-buttons a:last-child');
            if (nextLink) {
                window.location.href = nextLink.href;
            }
        }
    });
});
