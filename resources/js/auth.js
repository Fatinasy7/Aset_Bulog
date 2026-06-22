import api from './api.js';

// Authentication helper for frontend
export async function login(username, password) {
  try {
    const res = await api.post('/auth/login', { username, password });

    // common token locations
    const token = res.data?.access_token || res.data?.token || res.data?.data?.token;
    const user = res.data?.user || res.data?.data?.user || res.data?.data || res.data;

    if (token) localStorage.setItem('auth_token', token);
    if (user && typeof user === 'object') localStorage.setItem('currentUser', JSON.stringify(user));

    return res;
  } catch (err) {
    throw err;
  }
}

export async function logout() {
  try {
    await api.post('/auth/logout');
  } catch (e) {
    // ignore network errors on logout
  } finally {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('currentUser');
    if (typeof window.showLoginPage === 'function') {
      window.showLoginPage();
    } else {
      window.location.href = '/login';
    }
  }
}

export function guardRoute() {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    if (typeof window.showLoginPage === 'function') {
      window.showLoginPage();
    } else {
      window.location.href = '/login';
    }
    return false;
  }
  return true;
}

export function isAuthenticated() {
  return !!localStorage.getItem('auth_token');
}

export function getCurrentUser() {
  try {
    const raw = localStorage.getItem('currentUser');
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
}

// Attach to window for non-module pages
window.auth = {
  login,
  logout,
  guardRoute,
  isAuthenticated,
  getCurrentUser
};
window.logout = logout;

// Auto-bind login form if present
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');
  if (!form) return;

  // Avoid duplicate binding if app.js already attached login handler
  if (form.dataset.authBound === 'true') return;

  form.dataset.authBound = 'true';
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('loginUsername')?.value;
    const password = document.getElementById('loginPassword')?.value;
    const role = document.getElementById('loginRole')?.value;

    try {
      const response = await login(username, password, role);
      const user = response.data?.user || response.data?.data?.user || response.data?.data || response.data;
      if (user && typeof user === 'object' && !user.role && role) {
        user.role = role;
        localStorage.setItem('currentUser', JSON.stringify(user));
      }
      window.location.href = '/';
    } catch (err) {
      const message = err?.response?.data?.message || 'Login gagal';
      if (window.showToast) {
        window.showToast(message, 'error');
      } else {
        alert(message);
      }
    }
  });
});

export default {
  login,
  logout,
  guardRoute,
  isAuthenticated,
  getCurrentUser
};
