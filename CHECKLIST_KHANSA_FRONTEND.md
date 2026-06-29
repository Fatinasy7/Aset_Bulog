# ✅ FRONTEND CHECKLIST - KHANSA (Finalisasi Frontend)

**Status:** In Progress  
**Tanggal Mulai**: 29 Juni 2026  
**Deadline**: 30 Juni 2026  

---

## 🔴 FASE 1: LOGIN INTEGRATION (Day 1)

### Step 1.1: Update `auth.js` Login Function

```javascript
// [ ] Open public/js/auth.js

// [ ] Replace login function dengan:

function login(email, password) {
    // Validate input
    if (!email || !password) {
        showToast('Email dan password harus diisi', 'error');
        return;
    }

    // Show loading
    const loginBtn = document.getElementById('loginBtn');
    loginBtn.disabled = true;
    loginBtn.textContent = 'Loading...';

    // Send to backend
    axios.post('/api/auth/login', {
        email: email,
        password: password
    })
    .then(response => {
        // [ ] Save token
        const token = response.data.token || response.data.access_token;
        localStorage.setItem('auth_token', token);

        // [ ] Save user data
        localStorage.setItem('user', JSON.stringify(response.data.user));

        // [ ] Show success message
        showToast('Login berhasil!', 'success');

        // [ ] Redirect to dashboard
        setTimeout(() => {
            window.location.href = '/';
        }, 1000);
    })
    .catch(error => {
        // [ ] Show error message
        const message = error.response?.data?.message || 'Login gagal';
        showToast(message, 'error');
        console.error('Login error:', error);
    })
    .finally(() => {
        // [ ] Reset button
        loginBtn.disabled = false;
        loginBtn.textContent = 'Login';
    });
}

// [ ] Call login saat form submitted
document.getElementById('loginForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    login(email, password);
});
```

---

### Step 1.2: Update Axios Interceptor

```javascript
// [ ] Open public/js/api.js

// [ ] Add request interceptor:

axios.interceptors.request.use(config => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => Promise.reject(error));

// [ ] Add response interceptor:

axios.interceptors.response.use(
    response => response,
    error => {
        // [ ] Handle 401 - redirect to login
        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            window.location.href = '/';
        }
        
        // [ ] Log other errors
        console.error('API error:', error.response?.data);
        
        return Promise.reject(error);
    }
);
```

---

### Step 1.3: Test Login Function

```bash
1. [ ] Open browser DevTools (F12)
2. [ ] Go to http://localhost:8000 (or wherever frontend is)
3. [ ] Look for login form
4. [ ] Input credentials:
   Email: admin@bulog.test
   Password: password
5. [ ] Click Login
6. [ ] Check browser console (F12 → Console tab):
   [ ] No red errors?
   [ ] Message "Login berhasil"?
7. [ ] Check Network tab (F12 → Network):
   [ ] POST /api/auth/login in request list?
   [ ] Status 200?
   [ ] Response contains token?
8. [ ] Check localStorage (F12 → Application → Storage → Local Storage):
   [ ] auth_token exists?
   [ ] user data exists?
```

**If login fails:**
- [ ] Check Network tab → POST /api/auth/login response
- [ ] Check console for error message
- [ ] Verify backend `/api/auth/login` endpoint working (use Postman)
- [ ] Verify field names match (email, password)

---

## 🟡 FASE 2: ROLE-BASED UI (Day 1 afternoon)

### Step 2.1: Create Role Detection Function

```javascript
// [ ] Open public/js/app.js

// [ ] Add function di awal:

function getCurrentUserRole() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    return user.role || 'unknown';
}

function isUserRole(role) {
    return getCurrentUserRole() === role;
}

function hasAdminAccess() {
    return isUserRole('admin_it');
}

function hasPicAccess() {
    return isUserRole('user_pic');
}
```

---

### Step 2.2: Update Menu Visibility

```javascript
// [ ] Open public/index.html

// [ ] Find navigation/menu section and add id's:

<nav class="sidebar">
    <a href="#" onclick="showPage('dashboard')">Dashboard</a>
    
    <a href="#" onclick="showPage('daftar-aset')">Daftar Aset</a>
    
    <div id="menu-scan" style="display:none;">
        <a href="#" onclick="showPage('scan-qr')">Scan QR</a>
    </div>
    
    <div id="menu-manajemen-pic" style="display:none;">
        <a href="#" onclick="showPage('manajemen-pic')">Manajemen PIC</a>
    </div>
    
    <div id="menu-user-management" style="display:none;">
        <a href="#" onclick="showPage('user-management')">User Management</a>
    </div>
    
    <div id="menu-audit" style="display:none;">
        <a href="#" onclick="showPage('audit-trail')">Audit Trail</a>
    </div>
</nav>

// [ ] Add script di body (sebelum closing </body>):

<script>
function initializeMenuVisibility() {
    // [ ] Set role-based menu visibility
    
    if (hasAdminAccess()) {
        // Admin dapat akses semua
        document.getElementById('menu-scan').style.display = 'block';
        document.getElementById('menu-manajemen-pic').style.display = 'block';
        document.getElementById('menu-user-management').style.display = 'block';
        document.getElementById('menu-audit').style.display = 'block';
    } else if (hasPicAccess()) {
        // PIC hanya scan
        document.getElementById('menu-scan').style.display = 'block';
    }
    // Manajemen role - default visible semua
}

// [ ] Call saat page load
window.addEventListener('load', () => {
    initializeMenuVisibility();
});
</script>
```

---

### Step 2.3: Verify Role Display

```bash
1. [ ] Login as admin (role: admin_it)
   [ ] Should see: Dashboard, Daftar Aset, Scan QR, Manajemen PIC, User Management
   
2. [ ] Logout and login as PIC (if available)
   [ ] Should see: Dashboard, Daftar Aset, Scan QR
   
3. [ ] Check F12 Console:
   [ ] No errors?
```

---

## 🟢 FASE 3: TEST ALUR UTAMA (Day 1 evening)

### Step 3.1: Test Login → Dashboard Flow

```bash
Scenario: Fresh user opens app

1. [ ] Open http://localhost:8000
2. [ ] See login form
3. [ ] Input email: admin@bulog.test, password: password
4. [ ] Click Login
5. [ ] Check console (F12 → Console):
   [ ] No errors?
   [ ] "Login berhasil" message?
6. [ ] Redirected to dashboard
7. [ ] Check header:
   [ ] Shows "Selamat datang, Admin IT" (or similar)?
8. [ ] Check Network tab:
   [ ] POST /api/auth/login (Status 200)?
   [ ] GET /api/dashboard/summary (Status 200)?
9. [ ] Check dashboard content:
   [ ] Counter cards display (Total Assets, Laptops, etc)?
   [ ] Charts visible (doughnut + bar chart)?
```

**If fails:**
- [ ] Check console for error message
- [ ] Check Network tab for API responses
- [ ] Verify backend `/api/dashboard/summary` returning data

---

### Step 3.2: Test Fetch Assets

```bash
Scenario: User navigates to Daftar Aset

1. [ ] Click "Daftar Aset" menu
2. [ ] Check console (F12 → Console):
   [ ] No errors?
3. [ ] Check Network tab:
   [ ] GET /api/assets in request list?
   [ ] Status 200?
   [ ] Response contains asset array?
4. [ ] Asset table displays:
   [ ] Shows asset columns (Kode Aset, Nama, Kondisi, Lokasi)?
   [ ] Shows asset rows from API?
   [ ] Pagination buttons visible?
5. [ ] Try filtering:
   [ ] Select kondisi filter → table updates
   [ ] Type in search box → waits 300ms → table updates

[ ] ✅ Everything works?
```

**If fails:**
- [ ] Check Network tab → GET /api/assets response format
- [ ] Verify response structure (data array vs paginated?)
- [ ] Check console for parsing errors
- [ ] Adjust frontend code to match backend response format

---

### Step 3.3: Test Scan QR Flow

```bash
Scenario: User scans asset

1. [ ] Click "Scan QR" menu
2. [ ] Browser asks for camera permission → Click "Allow"
3. [ ] Camera view opens
4. [ ] Generate test QR code:
   [ ] Use online QR generator
   [ ] Input: { "id": 1, "kode_aset": "ASET-001", "jenis": "laptop", "nama_aset": "Laptop Dell" }
   [ ] Generate QR code
   [ ] Print or display on second device
5. [ ] Point camera at QR code
6. [ ] Wait for scan success (should auto-detect)
7. [ ] Check console (F12 → Console):
   [ ] No errors?
8. [ ] Check Network tab:
   [ ] POST /api/assets/1/scan in request list?
   [ ] Status 200/201?
   [ ] Request body contains latitude/longitude?
9. [ ] Asset detail displays:
   [ ] Shows asset name, kondisi, PIC, etc?
   [ ] Shows geolocation coordinates (if captured)?

[ ] ✅ Everything works?
```

**If fails:**
- [ ] Check Network tab → POST /api/assets/{id}/scan response
- [ ] Verify geolocation captured (check latitude/longitude in request)
- [ ] Check console for geolocation errors
- [ ] If endpoint missing, report to Fatin

---

### Step 3.4: Test Export Report

```bash
Scenario: User exports report

1. [ ] Click "Laporan" menu
2. [ ] (Optional) Set filters
3. [ ] Click "Export to PDF"
4. [ ] Check Network tab:
   [ ] GET /api/reports/assets?format=pdf in request list?
   [ ] Status 200?
   [ ] Response has Content-Type: application/pdf?
5. [ ] PDF file downloads
   [ ] [ ] ✅ File appears in Downloads folder?
   [ ] [ ] ✅ File is not corrupted (can open in PDF reader)?
6. [ ] Click "Export to Excel"
7. [ ] Check Network tab:
   [ ] GET /api/reports/assets?format=excel in request list?
   [ ] Status 200?
   [ ] Response has Content-Type: application/vnd.ms-excel?
8. [ ] Excel file downloads
   [ ] [ ] ✅ File appears in Downloads folder?
   [ ] [ ] ✅ File is not corrupted (can open in Excel)?

[ ] ✅ Both exports work?
```

**If fails:**
- [ ] Check Network tab response headers
- [ ] Verify backend returning file correctly
- [ ] Check console for download errors

---

## 🔵 FASE 4: BUG FIXING (Day 2 morning)

### Step 4.1: Console Error Audit

```bash
1. [ ] Open F12 → Console tab
2. [ ] Filter by "Errors" (red text)
3. [ ] List semua errors:
   [ ] Error 1: _________________
   [ ] Error 2: _________________
   [ ] Error 3: _________________
4. [ ] For each error, determine if:
   - [ ] Frontend bug (JS logic)?
   - [ ] Backend issue (API not returning expected format)?
5. [ ] Fix frontend bugs
6. [ ] Report backend issues to Fatin with:
   - Error message
   - API endpoint
   - Expected vs actual response
```

---

### Step 4.2: Network Request Audit

```bash
1. [ ] Open F12 → Network tab
2. [ ] Filter by "XHR/Fetch"
3. [ ] Perform main operations:
   [ ] Login
   [ ] Fetch assets
   [ ] Scan QR
   [ ] Export report
4. [ ] For each request, check:
   - [ ] Method correct (POST/GET/PUT)?
   - [ ] URL correct?
   - [ ] Headers include Authorization: Bearer?
   - [ ] Status code is 2xx or expected 4xx?
   - [ ] Response body is valid JSON?
5. [ ] Fix frontend if request format wrong
6. [ ] Report backend if response format wrong
```

---

### Step 4.3: Common Fixes

**Issue: 401 Unauthorized on protected route**
```javascript
// [ ] Check localStorage has token
console.log(localStorage.getItem('auth_token'));

// [ ] Check Axios sending Authorization header
// Add logging to request interceptor:
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('auth_token');
    console.log('Token:', token);
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});
```

**Issue: 422 Validation Error**
```javascript
// [ ] Check request body format
// [ ] Verify field names match backend expectations
// [ ] Add error display:

.catch(error => {
    if (error.response?.status === 422) {
        console.error('Validation errors:', error.response.data.errors);
        // Display errors to user
    }
});
```

**Issue: Response format mismatch**
```javascript
// [ ] Check response structure in Network tab
// [ ] Adjust parsing code:

// If backend returns:
// { data: [...] }
const assets = response.data.data;

// If backend returns:
// [...]
const assets = response.data;

// Use conditional:
const assets = Array.isArray(response.data) 
    ? response.data 
    : response.data.data;
```

---

## 🟣 FASE 5: FINAL TEST (Day 2 afternoon)

### Step 5.1: Full User Journey Test

```
Scenario: Complete workflow test

1. [ ] Login as admin
2. [ ] See dashboard with data
3. [ ] Navigate to Daftar Aset
4. [ ] Add new asset
   [ ] Click "Tambah Aset" button
   [ ] Fill form
   [ ] Submit
   [ ] Asset appears in list
5. [ ] Edit existing asset
   [ ] Click "Edit" button
   [ ] Modify field
   [ ] Submit
   [ ] List updates
6. [ ] Navigate to Scan QR
7. [ ] Scan an asset
8. [ ] View scan result
9. [ ] Navigate to Laporan
10. [ ] Export to PDF
11. [ ] Export to Excel
12. [ ] Logout
    [ ] Check localStorage cleared?
    [ ] Redirected to login?
13. [ ] Try accessing protected page directly
    [ ] Should redirect to login

[ ] ✅ All steps completed?
```

---

### Step 5.2: Performance Check

```bash
1. [ ] Open F12 → Performance tab
2. [ ] Perform main operation: Login → Dashboard
3. [ ] Check Network tab load times:
   [ ] Fastest: Login request < 1 second?
   [ ] Dashboard summary < 1 second?
   [ ] Assets list < 2 seconds?
4. [ ] QR scan:
   [ ] Scan to result display < 3 seconds? (target)
5. [ ] Report export:
   [ ] PDF export < 5 seconds?
   [ ] Excel export < 5 seconds?

[ ] ✅ Performance acceptable?
```

---

### Step 5.3: Responsive Design Check

```bash
1. [ ] Open DevTools (F12)
2. [ ] Toggle Device Toolbar (Ctrl+Shift+M)
3. [ ] Test on different screen sizes:
   [ ] Desktop (1920x1080)
   [ ] Tablet (768x1024)
   [ ] Mobile (375x667)
4. [ ] Verify:
   [ ] Menu responsive (hamburger menu on mobile)?
   [ ] Table scrollable on small screens?
   [ ] Forms inputs visible and usable?
   [ ] Buttons clickable (not too small)?

[ ] ✅ Responsive on all sizes?
```

---

## 🟣 FASE 6: DOCUMENTATION (Day 2)

### Step 6.1: Update or Create LAPORAN_KHANSA_FRONTEND_LOGIC.md

```markdown
# Laporan Pengerjaan Frontend Core Logic — Khansa Mufidah

## Ringkasan
- Periode pengerjaan: [tanggal] s.d. [tanggal]
- Total PR/branches: 13
- Status: Selesai ✅

## Fitur yang Diselesaikan
| Fitur | Commit/Branch | Status | Catatan |
|-------|---------------|--------|---------|
| Setup Frontend + Auth | feature/frontend-setup | ✅ Done | HTTP Client + JWT |
| Login Integration | feature/auth-integration | ✅ Done | Backend connected |
| Asset CRUD | feature/asset-list-integration | ✅ Done | All CRUD ops working |
| Asset Form | feature/asset-form-integration | ✅ Done | Validation implemented |
| QR Scanner | feature/qr-scanner | ✅ Done | Camera + QR code |
| Geotagging | feature/qr-geotagging | ✅ Done | Location capture |
| Dashboard | feature/dashboard-integration | ✅ Done | Charts + counters |
| Report Export | feature/report-export | ✅ Done | PDF + Excel |
| User Management | feature/user-management | ✅ Done | Admin only |
| PIC Management | feature/pic-management | ⏳ Waiting | Backend endpoints |
| Audit Trail | feature/audit-trail | ⏳ Waiting | Backend endpoints |

## Hasil Testing
- [x] Login test: PASS
- [x] Fetch assets: PASS
- [x] Scan QR: PASS (1.8s average)
- [x] Export report: PASS
- [x] Role-based UI: PASS
- [x] Error handling: PASS

## Performance Metrics
- QR scan to display: 1.8 seconds (target: <3s) ✅ PASS
- Asset list load: ~0.8 seconds
- Dashboard load: ~0.9 seconds
- Report export: ~1.5 seconds

## Browser Compatibility
- [x] Chrome 120+
- [x] Firefox 121+
- [x] Safari 17+
- [x] Edge 120+

## Known Issues
- (List any remaining issues)

## Catatan untuk PM
- Frontend 100% complete dan ready untuk UAT
- Waiting on backend for PIC Management & Audit Trail endpoints
- All user-facing features tested and working
- Git branches organized and documented

## Deliverables
- public/js/ (application logic)
- public/index.html (main template)
- public/css/ (styling)
- Git branch structure
- Documentation (this file)
```

---

### Step 6.2: Create Git Commit

```bash
[ ] cd c:\laragon\www\Aset_Bulog

[ ] git add .

[ ] git commit -m "frontend: complete integration with backend API and finalize all features"

[ ] git push origin main

[ ] Verify push successful
```

---

## ✅ FINAL CHECKLIST - KHANSA

**Before saying "DONE":**
- [ ] Login works with backend
- [ ] Role-based menu displays correctly
- [ ] Dashboard shows data from API
- [ ] Asset list fetches and displays
- [ ] CRUD operations work (create/read/update/delete)
- [ ] Scan QR captures coordinates
- [ ] Report export works (PDF + Excel)
- [ ] No console errors
- [ ] No 4xx/5xx API errors (except expected 404/401)
- [ ] All major flows tested end-to-end
- [ ] LAPORAN_KHANSA_FRONTEND_LOGIC.md updated
- [ ] Code committed to git

**Sign-off:**
- [ ] Frontend ready for UAT

---

## 🤝 Coordination Points

**When finished, coordinate with Fatin to verify:**

1. [ ] API response formats match expectations
2. [ ] Field names consistent between request/response
3. [ ] Error messages clear and helpful
4. [ ] CORS configured properly
5. [ ] Token handling working correctly

**Then do end-to-end test together:**

1. [ ] Login flow
2. [ ] Dashboard loading
3. [ ] Asset list with filters
4. [ ] QR scan flow
5. [ ] Report export
6. [ ] Logout

---

*Frontend Finalisasi Checklist*  
*For: Khansa Mufidah (Frontend Developer)*  
*Project: BULOG Asset Management System*
