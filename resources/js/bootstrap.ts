import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Enable sending cookies with requests (for Sanctum session-based auth)
window.axios.defaults.withCredentials = true;

// Add CSRF token from meta tag
const token = document.querySelector('meta[name="csrf-token"]') as any;
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Function to get fresh CSRF token
const getFreshCsrfToken = () => {
    const metaToken = document.querySelector('meta[name="csrf-token"]') as any;
    return metaToken?.content || '';
};

// Update CSRF token from meta tag before each request
window.axios.interceptors.request.use((config) => {
    const freshToken = getFreshCsrfToken();
    if (freshToken) {
        config.headers['X-CSRF-TOKEN'] = freshToken;
    }
    return config;
});

// Update meta tag CSRF token from response headers after each request
window.axios.interceptors.response.use(
    (response) => {
        const newToken = response.headers['x-csrf-token'];
        if (newToken) {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.setAttribute('content', newToken);
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
            }
        }
        return response;
    },
    (error) => {
        // Even on error, try to update CSRF token if it's available
        if (error.response?.headers['x-csrf-token']) {
            const newToken = error.response.headers['x-csrf-token'];
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.setAttribute('content', newToken);
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
            }
        }
        return Promise.reject(error);
    }
);

// Expose fresh token function globally for Vue components
(window as any).getFreshCsrfToken = getFreshCsrfToken;

// Log untuk debugging
console.log('Axios configured:', {
    withCredentials: window.axios.defaults.withCredentials,
    csrf_token: token?.content?.substring(0, 20) + '...' || 'NOT FOUND'
});
