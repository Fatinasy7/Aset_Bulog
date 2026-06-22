import './bootstrap';
import './auth';

// Early route guard for bundled frontend entrypoint.
document.addEventListener('DOMContentLoaded', () => {
  if (window.auth?.guardRoute) {
    window.auth.guardRoute();
  }
});
