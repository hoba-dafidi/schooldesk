import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

window.$ = window.jQuery = $;

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Make Toastify available globally
window.Toastify = Toastify;

// Make Pusher available globally
window.Pusher = Pusher;

// Laravel Echo configuration
window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  forceTLS: true,
  encrypted: true,
  wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
  wsPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
  wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
  enabledTransports: ['ws', 'wss'],
  authEndpoint: '/broadcasting/auth',
  auth: {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  }
});
