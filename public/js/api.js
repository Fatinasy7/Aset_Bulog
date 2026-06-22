const axiosClient = window.axios || (typeof axios !== 'undefined' ? axios : null);

if (!axiosClient) {
  throw new Error('Axios tidak ditemukan. Pastikan <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script> dimuat sebelum api.js');
}

const api = axiosClient.create({
  baseURL: '/api',
  headers: { 'Content-Type': 'application/json' }
});

api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
  },
  (error) => Promise.reject(error)
);

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error?.response?.status === 401) {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('currentUser');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

window.api = api;
