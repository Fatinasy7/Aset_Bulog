import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach(function (button) {
                if (button.disabled) {
                    return;
                }
                button.disabled = true;
                button.classList.add('is-loading');

                if (button.tagName.toLowerCase() === 'button') {
                    if (!button.dataset.origHtml) {
                        button.dataset.origHtml = button.innerHTML;
                    }
                    const loadingText = button.dataset.loadingText || 'Memproses...';
                    button.innerHTML = `<span class="btn-spinner" aria-hidden="true"></span>${loadingText}`;
                }

                if (button.tagName.toLowerCase() === 'input') {
                    if (!button.dataset.origValue) {
                        button.dataset.origValue = button.value;
                    }
                    button.value = button.dataset.loadingText || 'Memproses...';
                }
            });

            // Only disable submit buttons so AJAX forms can still update state after submit.
            // Non-submit controls remain interactive where the page may not navigate away immediately.
        });
    });
});
