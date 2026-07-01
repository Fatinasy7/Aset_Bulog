# Pull Request Template

## PR Title
```
[Frontend] Khansa: <Feature Name> (<Feature Branch Name>)
```

Example:
- `[Frontend] Khansa: QR Scanner with Geolocation (feature/qr-geotagging)`
- `[Frontend] Khansa: Dashboard Analytics (feature/dashboard-integration)`
- `[Frontend] Khansa: User Management Interface (feature/user-management)`

---

## Description

<!-- Brief summary of what this PR implements -->

**What does this PR implement?**

<!-- 1-2 sentence summary -->

---

## Feature Branch Information

- **Branch Name**: `feature/<name>`
- **Base Branch**: `main`
- **Number of Commits**: <!-- Example: 3 commits -->

### Commits Included:
<!-- List commit messages:
- feat: <description>
- fix: <description>
-->

---

## Changes Overview

### Files Modified
- [ ] `public/index.html` - UI template changes
- [ ] `public/js/app.js` - Main application logic
- [ ] `public/js/api.js` - API client configuration
- [ ] `public/js/auth.js` - Authentication logic
- [ ] `public/js/assets.js` - Asset API wrapper
- [ ] `public/js/qr-scanner.js` - QR scanner module
- [ ] `public/css/style.css` - Styling updates

### API Endpoints Used
- [ ] `GET /api/assets` - Fetch assets list
- [ ] `POST /api/assets` - Create new asset
- [ ] `PUT /api/assets/{id}` - Update asset
- [ ] `DELETE /api/assets/{id}` - Delete asset
- [ ] `POST /api/assets/{id}/scan` - Submit scan result
- [ ] `GET /api/dashboard/summary` - Dashboard data
- [ ] Other: <!-- Specify -->

---

## Related Checklist Items

This PR covers the following requirement checklist items:

- [ ] CHK-XX: <!-- Requirement description -->
- [ ] CHK-XX: <!-- Requirement description -->

**Completion Impact**: X items covered, bringing total to X/50 (X%)

---

## Testing

### Local Testing Performed
- [ ] Tested on Windows 10/11
- [ ] Tested on Chrome browser
- [ ] Tested on Firefox browser (if applicable)
- [ ] Tested on Safari (if applicable)
- [ ] Mobile responsive testing (if UI changes)
- [ ] No console errors or warnings

### Features Tested
- [ ] Feature 1 works as expected
- [ ] Feature 2 works as expected
- [ ] Error handling works correctly
- [ ] Form validation (if applicable)
- [ ] API calls successful (checked Network tab)

### Test Screenshots/Evidence
<!-- Add screenshots showing the feature working -->

---

## Performance Impact

### Performance Metrics (if applicable)

| Metric | Target | Actual | Status |
|--------|--------|--------|--------|
| QR Scan Time | < 3s | X.Xs | ✅ PASS |
| Dashboard Load | < 2s | X.Xs | ✅ PASS |
| Asset List Load | < 2s | X.Xs | ✅ PASS |

---

## Backend Dependencies

### Required API Endpoints

- [x] All endpoints already available
- [ ] Some endpoints still pending:
  - `GET /api/pics` - Needed for PIC management
  - `POST /api/pics` - Needed for PIC creation
  - Other: <!-- Specify -->

**Status**: 
- ✅ Ready to merge (all dependencies available)
- ⏳ Blocked (awaiting endpoint: ...)

---

## Code Quality

### Code Standards
- [x] Code follows JavaScript conventions
- [x] Comments added for complex logic
- [x] No duplicate code
- [x] Variables and functions have clear names
- [x] Error handling implemented

### Browser Compatibility
- [x] Chrome (Latest)
- [x] Firefox (Latest)
- [x] Edge (Latest)
- [x] Safari (if tested)

---

## Breaking Changes

- [ ] No breaking changes
- [ ] Breaking changes (describe below):

<!-- Describe any breaking changes -->

---

## Migration Guide (if needed)

<!-- If there are breaking changes, explain how to migrate -->

---

## Rollback Plan

If this PR needs to be reverted:
```bash
git revert -m 1 <merge-commit-hash>
git push origin main
```

**Estimated rollback time**: <!-- minutes -->

---

## Checklist for Approvers

- [ ] Code review completed
- [ ] All tests pass
- [ ] No merge conflicts
- [ ] Performance acceptable
- [ ] Documentation updated
- [ ] Related issues closed

---

## Reviewer Notes

### For Backend Developer (Fatin):
<!-- Specific points for Fatin to review if applicable -->

### For Project Manager:
<!-- Specific points for PM to review -->

---

## Related Issues/PRs

Closes: <!-- #issue_number -->
Relates to: <!-- #issue_number -->
Supersedes: <!-- #pr_number -->

---

## Screenshots/Demo

### Before
<!-- Screenshots of before state if applicable -->

### After
<!-- Screenshots of after state -->

---

## Additional Context

<!-- Add any additional context that reviewers should know about -->

---

## Sign-off

- **Developer**: Khansa Mufidah
- **Branch**: `feature/<name>`
- **Date**: <!-- YYYY-MM-DD -->
- **Ready for Merge**: <!-- YES / NEEDS CHANGES -->

---

## Merge Request Approval

- [ ] Approved by Backend Developer
- [ ] Approved by Project Manager
- [ ] Ready to merge to main

**Merged by**: <!-- Name -->
**Merge date**: <!-- YYYY-MM-DD HH:MM -->
**Release version**: <!-- v1.0.x -->
