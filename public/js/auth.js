function login(username, password, role) {
  return window.api.post('/auth/login', { username, password, role })
    .then((res) => {
      const token = res.data?.access_token || res.data?.token || res.data?.data?.token;
      let user = res.data?.user || res.data?.data?.user || res.data?.data || res.data;

      if (token) localStorage.setItem('auth_token', token);
      if (user && typeof user === 'object') {
        if (!user.role && role) user.role = role;
        localStorage.setItem('currentUser', JSON.stringify(user));
      }
      return res;
    });
}

function redirectToLogin() {
  localStorage.removeItem('auth_token');
  localStorage.removeItem('currentUser');
  if (typeof window.showLoginPage === 'function') {
    window.showLoginPage();
  } else {
    window.location.href = '/login';
  }
}

function logout() {
  return window.api.post('/auth/logout')
    .catch(() => {})
    .finally(() => {
      redirectToLogin();
    });
}

function guardRoute() {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    redirectToLogin();
    return false;
  }
  return true;
}

function isAuthenticated() {
  return !!localStorage.getItem('auth_token');
}

function getCurrentUser() {
  try {
    const raw = localStorage.getItem('currentUser');
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
}

window.auth = {
  login,
  logout,
  guardRoute,
  isAuthenticated,
  getCurrentUser
};
window.logout = logout;

function bindLoginForm() {
  const form = document.getElementById('loginForm');
  if (!form || form.dataset.authBound === 'true') return;
  form.dataset.authBound = 'true';
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const username = document.getElementById('loginUsername')?.value;
    const password = document.getElementById('loginPassword')?.value;
    const role = document.getElementById('loginRole')?.value;

    login(username, password, role)
      .then(() => {
        window.location.href = '/';
      })
      .catch((err) => {
        const message = err?.response?.data?.message || 'Login gagal';
        if (window.showToast) {
          window.showToast(message, 'error');
        } else {
          alert(message);
        }
      });
  });
}

document.addEventListener('DOMContentLoaded', bindLoginForm);
