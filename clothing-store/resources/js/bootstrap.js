import axios from 'axios';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

window.axios = axios;
window.Alpine = Alpine;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Alpine.plugin(collapse);
Alpine.plugin(focus);

// Do NOT call Alpine.start() here - it will be called in the layout before @livewireScripts