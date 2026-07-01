# Quality & Testing Guide

This document describes additional quality checks added to the repository:

1) Load test: `tests/load/k6/50_concurrent_users.js`

- This is a k6 script that simulates 50 virtual users for 30s calling `/api/assets`.
- Run locally (requires `k6` installed):

```bash
# run against local app
k6 run tests/load/k6/50_concurrent_users.js

# or target a specific URL
TARGET_URL=https://staging.example.com k6 run tests/load/k6/50_concurrent_users.js
```

2) GitHub Actions: `/.github/workflows/quality-tests.yml`

- Manual workflow dispatch that runs `phpunit` first (PHP 8.3, with GD extension), and optionally runs the `k6` load test when you provide the `target_url` input.

Usage (from Actions UI): choose `Quality & Load Tests`, press `Run workflow`, set `target_url` if you want the k6 job to execute.

3) Frontend integration verification

- We include a simple smoke test in `tests/Feature/ExampleTest.php` to ensure the root route behavior and backend API contract.
- For deeper integration (end-to-end) testing, use Playwright or Cypress against your deployed frontend. Example Playwright/Puppeteer scripts are not included to avoid adding Node deps to backend repo, but you can add them into `tests/e2e/` if desired.

4) Branch & PR

- I recommend creating a feature branch (e.g. `feature/png-label-endpoint`) for these changes and opening a PR so CI runs the `phpunit` job.
