# ✅ BACKEND CHECKLIST - FATIN (Finalisasi Backend)

**Status:** In Progress  
**Tanggal Mulai**: 29 Juni 2026  
**Deadline**: 30 Juni 2026  

---

## 🔴 FASE 1: VERIFIKASI ENDPOINT (Day 1)

### Step 1.1: Pastikan Database Setup
- [ ] Run: `php artisan migrate:fresh --seed`
- [ ] Verify users table has admin account:
  ```bash
  SELECT * FROM users WHERE email = 'admin@bulog.test';
  ```
- [ ] Verify assets table has sample data:
  ```bash
  SELECT COUNT(*) FROM assets;
  ```
- [ ] Check laravel.log untuk errors: `tail -f storage/logs/laravel.log`

---

### Step 1.2: Test Auth Endpoints dengan Postman

#### 1.2.1: Login Endpoint
```
[ ] Create POST request: http://localhost:8000/api/auth/login

Headers:
  Content-Type: application/json
  Accept: application/json

Body (raw JSON):
{
  "email": "admin@bulog.test",
  "password": "password"
}

Expected Response (Status 200 atau 201):
{
  "token": "xxxxx",
  "user": {
    "id": 1,
    "name": "Admin IT",
    "email": "admin@bulog.test",
    "role": "admin_it"
  }
}

[ ] ✅ Status 200/201?
[ ] ✅ Token in response?
[ ] ✅ User data in response?
[ ] ✅ Role in response?
```

**Issue?** → Cek backend/app/Http/Controllers/AuthController.php
- Apakah endpoint ada?
- Apakah return format sesuai?

---

#### 1.2.2: Get User Endpoint (Protected)
```
[ ] Copy token dari login response
[ ] Create GET request: http://localhost:8000/api/user

Headers:
  Authorization: Bearer <token_dari_login>
  Accept: application/json

Expected Response (Status 200):
{
  "id": 1,
  "name": "Admin IT",
  "email": "admin@bulog.test",
  "role": "admin_it"
}

[ ] ✅ Status 200?
[ ] ✅ User data returned?
[ ] ✅ Role in response?
```

**Issue?** → Check:
- Apakah middleware 'auth:sanctum' aktif?
- Apakah token format Bearer <token> benar?

---

#### 1.2.3: Logout Endpoint
```
[ ] Create POST request: http://localhost:8000/api/auth/logout

Headers:
  Authorization: Bearer <token>
  Accept: application/json

Expected Response (Status 200):
{
  "message": "Logged out successfully"
}

[ ] ✅ Status 200?
[ ] ✅ Message returned?
```

---

### Step 1.3: Test Asset Endpoints

#### 1.3.1: Get All Assets
```
[ ] Create GET request: http://localhost:8000/api/assets

Headers:
  Authorization: Bearer <token>
  Accept: application/json

Query params (optional):
  page=1
  per_page=10
  kondisi=baik
  jenis=laptop

Expected Response (Status 200):
Option A (Paginated):
{
  "data": [
    {
      "id": 1,
      "kode_aset": "ASET-001",
      "nama_aset": "Laptop Dell",
      "jenis": "laptop",
      "kondisi": "baik",
      "lokasi": "Kantor 1"
    }
  ],
  "current_page": 1,
  "per_page": 10,
  "total": 100
}

Option B (Array):
[
  {
    "id": 1,
    "kode_aset": "ASET-001",
    ...
  }
]

[ ] ✅ Status 200?
[ ] ✅ Asset data returned?
[ ] ✅ Choose option A or B, then inform Khansa
```

---

#### 1.3.2: Create New Asset
```
[ ] Create POST request: http://localhost:8000/api/assets

Headers:
  Authorization: Bearer <token>
  Content-Type: application/json

Body:
{
  "kode_aset": "ASET-TEST-001",
  "nama_aset": "Test Laptop",
  "jenis": "laptop",
  "kondisi": "baik",
  "lokasi": "Test Location",
  "merk_type": "Dell",
  "tgl_perolehan": "2026-01-01",
  "harga": 10000000
}

Expected Response (Status 201):
{
  "id": 999,
  "kode_aset": "ASET-TEST-001",
  "nama_aset": "Test Laptop",
  ...
}

[ ] ✅ Status 201?
[ ] ✅ Asset created?
[ ] ✅ ID in response?
```

**Issue?** → Check:
- Apakah field names sesuai dengan validasi di controller?
- Apakah validation error? (422 response?)

---

#### 1.3.3: Get Single Asset
```
[ ] Create GET request: http://localhost:8000/api/assets/1

Headers:
  Authorization: Bearer <token>

Expected Response (Status 200):
{
  "id": 1,
  "kode_aset": "ASET-001",
  "nama_aset": "Laptop Dell",
  ...
}

[ ] ✅ Status 200?
[ ] ✅ Asset data returned?

[ ] Test dengan ID yang tidak ada:
   GET /api/assets/99999
   
   Expected: Status 404
   [ ] ✅ Status 404?
```

---

#### 1.3.4: Update Asset
```
[ ] Create PUT request: http://localhost:8000/api/assets/1

Headers:
  Authorization: Bearer <token>
  Content-Type: application/json

Body:
{
  "kondisi": "rusak_ringan",
  "lokasi": "Kantor 2"
}

Expected Response (Status 200):
{
  "id": 1,
  "kondisi": "rusak_ringan",
  "lokasi": "Kantor 2",
  ...
}

[ ] ✅ Status 200?
[ ] ✅ Asset updated?
```

---

#### 1.3.5: Delete Asset
```
[ ] Create DELETE request: http://localhost:8000/api/assets/999

Headers:
  Authorization: Bearer <token>

Expected Response (Status 200):
{
  "message": "Asset deleted successfully"
}

[ ] ✅ Status 200?
[ ] ✅ Message returned?

[ ] Verify deleted:
   GET /api/assets/999 → should return 404
   [ ] ✅ Status 404?
```

---

### Step 1.4: Test Scan Endpoint

#### 1.4.1: Scan Asset
```
[ ] Create POST request: http://localhost:8000/api/assets/1/scan

Headers:
  Authorization: Bearer <token>
  Content-Type: application/json

Body:
{
  "latitude": -6.1751,
  "longitude": 106.8650,
  "scanned_at": "2026-06-29T10:00:00Z"
}

Expected Response (Status 200 atau 201):
{
  "asset": {
    "id": 1,
    "nama_aset": "Laptop Dell",
    ...
  },
  "message": "Asset scanned successfully",
  "scan_id": 123
}

[ ] ✅ Endpoint exists?
[ ] ✅ Status 200/201?
[ ] ✅ Asset data returned?
```

**Issue?** → Check:
- Apakah endpoint ada di routes/api.php?
- Apakah handle di controller?

---

### Step 1.5: Test Dashboard Endpoint

```
[ ] Create GET request: http://localhost:8000/api/dashboard/summary

Headers:
  Authorization: Bearer <token>

Expected Response (Status 200):
{
  "total_assets": 100,
  "total_laptops": 50,
  "total_printers": 30,
  "total_in_repair": 10,
  "kondisi_breakdown": {
    "baik": 70,
    "rusak_ringan": 15,
    "rusak_berat": 10,
    "dalam_perbaikan": 5
  },
  "lokasi_breakdown": {
    "Kantor 1": 40,
    "Kantor 2": 60
  }
}

[ ] ✅ Endpoint exists?
[ ] ✅ Status 200?
[ ] ✅ Summary data returned?
```

---

### Step 1.6: Test Report Endpoint

```
[ ] Create GET request: http://localhost:8000/api/reports/assets?format=preview

Headers:
  Authorization: Bearer <token>

Query params (optional):
  kondisi=baik
  jenis=laptop
  format=preview

Expected Response (Status 200):
{
  "data": [
    {
      "id": 1,
      "kode_aset": "ASET-001",
      "nama_aset": "Laptop Dell",
      ...
    }
  ]
}

[ ] ✅ Endpoint exists?
[ ] ✅ Status 200?
[ ] ✅ Data returned?

[ ] Test PDF export:
   GET /api/reports/assets?format=pdf
   [ ] ✅ Returns PDF file?
   
[ ] Test Excel export:
   GET /api/reports/assets?format=excel
   [ ] ✅ Returns Excel file?
```

---

## 🟡 FASE 2: FIX ISSUES (Day 1 evening)

### Step 2.1: CORS Configuration

```php
// [ ] Edit config/cors.php

'supports_credentials' => true,
'allowed_origins' => ['http://localhost', 'http://127.0.0.1', 'http://localhost:3000'],
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
'allowed_headers' => ['Content-Type', 'Authorization'],

// [ ] Test CORS:
curl -X OPTIONS http://localhost:8000/api/assets \
  -H "Origin: http://localhost" \
  -H "Access-Control-Request-Method: POST" \
  -v
```

**Check response headers:**
- [ ] `Access-Control-Allow-Origin: http://localhost`?
- [ ] `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`?

---

### Step 2.2: JSON Response Format

```php
// [ ] Check semua controller responses konsisten

// ✅ GOOD:
return response()->json([
    'data' => $asset,
    'message' => 'Success'
]);

// ❌ BAD:
return $asset;  // Direct model without wrapper
return ['id' => 1, 'name' => 'Test'];  // Inconsistent

// [ ] Audit all controller methods
// [ ] Update if inconsistent
```

---

### Step 2.3: Error Handling

```php
// [ ] Handle 404 errors properly

return response()->json([
    'message' => 'Asset not found'
], 404);

// [ ] Handle 422 validation errors properly

return response()->json([
    'message' => 'Validation failed',
    'errors' => $validator->errors()
], 422);

// [ ] Handle 401 unauthorized

return response()->json([
    'message' => 'Unauthorized'
], 401);
```

---

## 🟢 FASE 3: TEST & VERIFY (Day 2)

### Step 3.1: Run Unit Tests

```bash
[ ] php artisan test

Expected output:
PASSED  XXX tests

[ ] ✅ All tests passed?
[ ] ✅ No errors in output?
```

If test fails:
- [ ] Check test file yang failed
- [ ] Debug logic di controller
- [ ] Run test again

---

### Step 3.2: End-to-End Manual Test

```bash
[ ] Jalankan semua endpoint flow:

1. Login → Get token
2. Get assets → Check data format
3. Create asset → Check response
4. Get single asset → Check response
5. Update asset → Check response
6. Scan asset → Check response
7. Get dashboard → Check response
8. Get report → Check response
9. Logout → Check response
10. Try protected endpoint tanpa token → Should get 401

[ ] ✅ All flows work?
[ ] ✅ No errors in laravel.log?
```

---

### Step 3.3: Database Integrity Check

```bash
[ ] Check data di database

SELECT COUNT(*) FROM users;                    # [ ] > 0
SELECT COUNT(*) FROM assets;                  # [ ] > 0
SELECT * FROM users WHERE role = 'admin_it';  # [ ] Admin exists?
```

---

## 🔵 FASE 4: DOCUMENTATION (Day 2)

### Step 4.1: Create LAPORAN_FATIN_BACKEND.md

```markdown
# Laporan Pengerjaan Backend — Fatin

## Ringkasan
- Periode: ...
- Endpoint yang tersedia: X
- Status: Ready / Needs fixes

## API Endpoints

### Authentication
- POST /api/auth/login
  Request: { email, password }
  Response: { token, user }
  
- GET /api/user (protected)
  Response: { id, name, email, role }

- POST /api/auth/logout
  Response: { message }

### Assets
- GET /api/assets
  Response: { data, pagination }
  
- POST /api/assets
  Request: { kode_aset, nama_aset, ... }
  Response: { id, kode_aset, ... }

... (continue for all endpoints)

## Database Schema
- Users table: id, name, email, password, role
- Assets table: id, kode_aset, nama_aset, ...
- Scans table: id, asset_id, latitude, longitude, ...

## Testing
- [ ] All endpoints tested with Postman
- [ ] Database seeding successful
- [ ] CORS configured
- [ ] Error handling implemented

## Known Issues
- (List any issues)

## Notes for Frontend Developer
- Token format: Bearer <token>
- All dates in format: YYYY-MM-DD HH:MM:SS
- Role values: admin_it, user_pic, manajemen
```

---

### Step 4.2: Create API Documentation

```
[ ] Generate Postman collection export
[ ] Include cURL examples
[ ] Document all query params
[ ] Document all response fields
```

---

## 🟣 PHASE 5: GIT COMMIT (Day 2)

```bash
[ ] git add .
[ ] git commit -m "backend: finalize API endpoints with CORS and error handling"
[ ] git push origin main
```

---

## ✅ FINAL CHECKLIST - FATIN

**Before saying "DONE":**
- [ ] All endpoints return Status 2xx/4xx correctly
- [ ] JSON response format consistent
- [ ] CORS configured
- [ ] Error handling implemented
- [ ] Database seeded with test data
- [ ] laravel.log has no errors
- [ ] LAPORAN_FATIN_BACKEND.md created
- [ ] Code committed to git

**Sign-off:**
- [ ] Backend ready for frontend integration

---

*Backend Finalisasi Checklist*  
*For: Fatin (Backend Developer)*  
*Project: BULOG Asset Management System*
