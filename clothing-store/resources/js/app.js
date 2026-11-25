import './bootstrap';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        window.Alpine.plugin(collapse);
        window.Alpine.plugin(focus);
    }
});
