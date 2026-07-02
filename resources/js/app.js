import './bootstrap';

// Sidebar toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const appBody = document.querySelector('.app-body');

    // prepare data-icon attributes for collapsed state (simple mapping)
    const iconMap = {
        'Dashboard': '🏠',
        'Data Laptop': '💻',
        'Data Printer': '🖨️',
        'Scan QR Code': '🔎',
        'Laporan': '📊',
        'Pengaturan': '⚙️',
        'Manajemen PIC': '👥',
        'Dashboard Manajemen': '📈'
    };
    const sidebarLinks = sidebar ? Array.from(sidebar.querySelectorAll('.sidebar-link')) : [];
    sidebarLinks.forEach(a => {
        const text = a.textContent.trim();
        a.setAttribute('data-icon', iconMap[text] || text.charAt(0).toUpperCase());
    });

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            const isCollapsedNow = sidebar.classList.toggle('sidebar-collapsed');
            appBody.classList.toggle('sidebar-collapsed');
            // set aria-expanded for accessibility
            sidebarToggle.setAttribute('aria-pressed', isCollapsedNow ? 'true' : 'false');
            // Save preference to localStorage
            localStorage.setItem('sidebarCollapsed', isCollapsedNow);
        });

        // Restore sidebar state from localStorage
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('sidebar-collapsed');
            appBody.classList.add('sidebar-collapsed');
            sidebarToggle.setAttribute('aria-pressed', 'true');
        }
    }
});
