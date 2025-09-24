import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const userId = localStorage.getItem('user_id'); // must be set after login

export const echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001, // default for Laravel WebSockets
    forceTLS: false,
    disableStats: true,
    authEndpoint: 'http://127.0.0.1:8000/broadcasting/auth', // Laravel auth
    auth: {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
    },
});
