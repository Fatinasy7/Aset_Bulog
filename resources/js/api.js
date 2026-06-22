const axiosClient = window.axios || (typeof axios !== 'undefined' ? axios : null);

const api = axiosClient.create({
  baseURL: '/api',
  headers: { 'Content-Type': 'application/json' }
});

// Auto-attach token to each request
api.interceptors.request.use(
  (config) => {
    try {
      const token = localStorage.getItem('auth_token');
      if (token) config.headers.Authorization = `Bearer ${token}`;
    } catch (e) {}
    return config;
  },
  (error) => Promise.reject(error)
);

// Redirect to login on 401
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error?.response?.status === 401) {
      try {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('currentUser');
      } catch (e) {}
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Expose for quick access in non-module pages
window.api = api;
export default api;
