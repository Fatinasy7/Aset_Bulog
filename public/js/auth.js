function parseJson(raw) {
  try {
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
}

function getLocalUsers() {
  const stored = localStorage.getItem('users');
  return parseJson(stored) || [];
}

function saveLocalUsers(users) {
  localStorage.setItem('users', JSON.stringify(users));
}

function ensureDemoUsers() {
  const users = getLocalUsers();
  if (users.length === 0) {
    const defaultUsers = [
      { username: 'admin', password: 'admin123', role: 'admin' },
      { username: 'pic', password: 'pic123', role: 'pic' }
    ];
    saveLocalUsers(defaultUsers);
    return defaultUsers;
  }
  return users;
}

function findLocalUser(username, password, role) {
  const users = ensureDemoUsers();
  return users.find(u => u.username === username && u.password === password && (!role || u.role === role));
}

function login(username, password, role) {
  if (!username || !password || !role) {
    return Promise.reject(new Error('Username, password, dan role wajib diisi'));
  }

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
    })
    .catch((err) => {
      const localUser = findLocalUser(username, password, role);
      if (localUser) {
        const token = `local-${username}-${Date.now()}`;
        localStorage.setItem('auth_token', token);
        localStorage.setItem('currentUser', JSON.stringify({ username: localUser.username, role: localUser.role }));
        return Promise.resolve({ data: { token, user: localUser } });
      }
      return Promise.reject(err);
    });
}

function redirectToLogin() {
  localStorage.removeItem('auth_token');
  localStorage.removeItem('currentUser');
  if (typeof window.showLoginPage === 'function') {
    window.showLoginPage();
  } else {
    window.location.reload();
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
        if (typeof window.showMainApp === 'function') {
          window.location.reload();
        } else {
          window.location.href = '/';
        }
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
