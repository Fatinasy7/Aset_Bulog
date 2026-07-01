# Branch Merge & Pull Request Checklist

## Overview
This checklist guides the merging process for all feature branches into the main branch. Follow the steps in order to ensure proper documentation and code quality.

---

## Pre-Merge Requirements

Before merging any feature branch to main, ensure:
- ✅ Code is tested and working locally
- ✅ No merge conflicts exist
- ✅ Commit messages follow convention (feat:, fix:, docs:, etc.)
- ✅ Branch is up-to-date with main
- ✅ Backend API endpoints are available (if needed)

---

## Merge Sequence (Recommended Order)

### Phase 1: Foundation (Required for all other features)
- [ ] `feature/frontend-setup` → main
- [ ] `feature/auth-integration` → main

### Phase 2: Core Asset Management
- [ ] `feature/asset-list-integration` → main
- [ ] `feature/asset-form-integration` → main

### Phase 3: Scanning & Geolocation
- [ ] `feature/qr-scanner` → main
- [ ] `feature/qr-geotagging` → main

### Phase 4: Analytics & Reporting
- [ ] `feature/dashboard-integration` → main
- [ ] `feature/report-export` → main

### Phase 5: User Administration
- [ ] `feature/user-management` → main

### Phase 6: Advanced Features (Pending Backend)
- [ ] `feature/pic-management` → main ⏳ *When backend ready*
- [ ] `feature/audit-trail` → main ⏳ *When backend ready*

### Phase 7: Testing & Documentation
- [ ] `testing/qr-performance` → main
- [ ] `docs/khansa-completion` → main

---

## Merge Process

### Step 1: Update Feature Branch
```bash
git checkout <feature-branch>
git pull origin main
git rebase main  # Or: git merge main (if rebase conflicts)
```

### Step 2: Verify Changes
```bash
git log --oneline -5
git diff main..HEAD  # Review all changes
```

### Step 3: Create Pull Request

**PR Title Format:**
```
[Frontend] Khansa: <Feature Name> (#<branch-name>)
```

**PR Description Template:**
```markdown
## Description
Brief description of what this PR implements.

## Feature Branch
- Branch: feature/<name>
- Commits: <number>

## Testing
- [x] Tested on local environment
- [x] No console errors
- [x] API integration verified (if applicable)
- [x] Mobile responsive (if UI change)

## Checklist Items Covered
- [x] CHK-XXX: Item description
- [x] CHK-YYY: Item description

## Backend Dependencies
- [x] All required endpoints available OR
- [ ] Pending: <endpoint-name> (tracked in separate issue)

## Performance
- [x] No significant performance impact
- [x] Load times acceptable
```

### Step 4: Code Review
- Request review from: `Fatin (Backend)` or Project Manager
- Address feedback before merging
- Ensure all GitHub checks pass

### Step 5: Merge to Main
```bash
git checkout main
git merge --no-ff <feature-branch>
git tag -a v1.0.<increment> -m "Release: <Feature Name>"
git push origin main
git push origin --tags
```

---

## PR Examples

### Example 1: Frontend Setup
```
[Frontend] Khansa: HTTP Client & Folder Structure Setup (#feature/frontend-setup)

Implemented Axios HTTP client with global interceptor for JWT token management.
Organized project folder structure for better maintainability.

Checklist: CHK-01, CHK-02
```

### Example 2: QR Scanner Integration
```
[Frontend] Khansa: QR Code Scanner with Geolocation (#feature/qr-scanner)

Integrated HTML5-QRCode library for camera-based scanning.
Added geolocation capture with 5-second timeout fallback.

Testing: Verified 1.8s average scan performance (target <3s)
Checklist: CHK-15, CHK-16, CHK-17, NFR-03
```

### Example 3: PIC Management (Pending Backend)
```
[Frontend] Khansa: PIC Management Interface (#feature/pic-management)

Awaiting backend API endpoints:
- GET /api/pics
- POST /api/pics
- PUT /api/pics/{id}
- DELETE /api/pics/{id}

Status: BLOCKED on backend implementation
Estimated unblock: <date>
```

---

## Version Tagging

After each phase is merged, create a release tag:

```bash
git tag -a v1.0.0-alpha -m "Alpha: Foundation setup"
git tag -a v1.0.1-alpha -m "Alpha: Core asset management"
git tag -a v1.0.2-beta -m "Beta: Scanning features complete"
git tag -a v1.0.3-beta -m "Beta: Analytics & reporting"
git tag -a v1.0.4-rc -m "Release Candidate: All features merged"
git tag -a v1.0.0 -m "Production Release: BULOG Asset Management System"

git push origin --tags
```

---

## Rollback Procedure

If a merge causes issues:

```bash
# Identify problematic commit
git log --oneline -10

# Revert the merge
git revert -m 1 <merge-commit-hash>
git push origin main

# Or: Reset to previous state (for local only)
git reset --hard HEAD~1
```

---

## Post-Merge Actions

After each merge:

1. Update `GIT_BRANCH_DOCUMENTATION.md` with merge status
2. Close related issues/pull requests
3. Update project board/kanban
4. Notify team of new features
5. Schedule UAT if applicable

---

## Testing After Merge

```bash
# Checkout main and test
git checkout main
npm install  # If dependencies changed
npm run dev  # Start development server

# Verify:
- [ ] No console errors
- [ ] All features work correctly
- [ ] No performance degradation
- [ ] Responsive design maintained
- [ ] API calls successful (check Network tab)
```

---

## Conflict Resolution

If merge conflicts occur:

```bash
# View conflicts
git status

# Edit conflicted files
# Keep both versions or choose one

# Mark resolved
git add <resolved-file>
git commit -m "chore: resolve merge conflicts from <branch-name>"
```

**Common Conflicts:**
- Multiple features modifying `public/js/app.js` - merge `showPage()` and event handlers carefully
- Different CSS modifications - ensure no duplicate selectors
- Package.json changes - review and keep most recent versions

---

## Approval Matrix

| Branch | Approver | Review Focus |
|--------|----------|--------------|
| feature/* | Fatin (Backend) + PM | Functionality, API integration |
| testing/* | Khansa + PM | Test coverage, metrics |
| docs/* | PM + Khansa | Accuracy, completeness |
| feature/pic-* | Fatin (Backend) | Backend endpoints ready |
| feature/audit-* | Fatin (Backend) | Backend endpoints ready |

---

## Status Tracking

### Phase 1: Foundation ✅
- [x] feature/frontend-setup
- [x] feature/auth-integration

### Phase 2: Core Asset Management ✅
- [x] feature/asset-list-integration
- [x] feature/asset-form-integration

### Phase 3: Scanning & Geolocation ✅
- [x] feature/qr-scanner
- [x] feature/qr-geotagging

### Phase 4: Analytics & Reporting ✅
- [x] feature/dashboard-integration
- [x] feature/report-export

### Phase 5: User Administration ✅
- [x] feature/user-management

### Phase 6: Advanced Features ⏳
- [ ] feature/pic-management (Pending backend endpoints)
- [ ] feature/audit-trail (Pending backend endpoints)

### Phase 7: Testing & Documentation ✅
- [x] testing/qr-performance
- [x] docs/khansa-completion

---

**Last Updated**: 2024
**Created By**: Khansa Mufidah (Frontend Core Logic)
**Project**: BULOG Asset Management System
