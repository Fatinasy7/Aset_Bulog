# Optional PNG QR Label Backend

This repository includes a dedicated SVG label endpoint and an optional PNG label endpoint.

Summary of relevant endpoints:

- `GET /api/assets/{asset}/qrcode` — download raw QR code (`.svg` or `.png` depending on stored file)
- `GET /api/assets/{asset}/qrcode/label` — download SVG label (always available)
- `GET /api/assets/{asset}/qrcode/label.png` — PNG label endpoint (falls back to SVG if server lacks Imagick)
- `GET /api/assets/{asset}/qrcode/label.force.png` — optional always-PNG endpoint (requires extra deps)

Enabling the always-PNG backend
--------------------------------
To enable the `label.force.png` endpoint (guaranteed PNG generation), install the optional PHP library and ensure the GD extension is enabled.

1. Install dependency (on your development/CI machine):

```bash
composer require endroid/qr-code:^5.0
```

2. Ensure `ext-gd` is available in your PHP runtime. On Debian/Ubuntu:

```bash
sudo apt-get install php-gd
# restart your PHP-FPM/Apache as needed
```

3. After installation, run the Feature test suite to verify everything:

```bash
php -d extension=pdo_sqlite vendor/bin/phpunit --configuration phpunit.xml --testsuite Feature
```

Notes
-----
- The project already includes a safe fallback: when Imagick is not available the PNG endpoint `label.png` will return the SVG label instead.
- `composer.json` has been updated to list `endroid/qr-code` and `ext-gd` as optional additions. If you prefer not to modify `composer.json` directly, run the `composer require` command above.
- If you encounter dependency conflicts, consider using `composer update endroid/qr-code --with-all-dependencies` or installing a compatible version for your PHP runtime.

Testing & CI
-----------
All Feature tests (including new label tests) currently pass in the repository test environment. After installing optional deps locally, re-run tests to ensure PNG backend works in your environment.
