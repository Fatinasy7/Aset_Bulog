# 📚 API DOCUMENTATION - Sumber Kebenaran Koordinasi

**Created**: 29 Juni 2026  
**For**: Fatin (Backend) & Khansa (Frontend)  
**Status**: To be agreed upon

---

## 🎯 Tujuan Dokumen

Ini adalah **satu sumber kebenaran** untuk semua endpoint API yang akan dikembangkan backend dan digunakan frontend.

**Instruksi:**
1. Backend (Fatin): Implement sesuai spec ini
2. Frontend (Khansa): Test menggunakan spec ini
3. Jika ada perubahan → **update dokumen ini DULU**, baru implement

---

## 🔐 AUTHENTICATION ENDPOINTS

### 1. POST /api/auth/login

**Tujuan**: Login user dan dapatkan token

**Request**:
```json
{
  "email": "admin@bulog.test",
  "password": "password"
}
```

**Response (Status 200 / 201)**:
```json
{
  "token": "xxxxxxxxxxxxxxxxxxxxx",
  "access_token": "xxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Admin IT",
    "email": "admin@bulog.test",
    "role": "admin_it"
  }
}
```

**Possible Errors**:
- Status 401: Invalid credentials
- Status 422: Validation error (missing fields)

**Frontend Usage**:
```javascript
const response = await axios.post('/api/auth/login', {
  email: 'admin@bulog.test',
  password: 'password'
});

const token = response.data.token || response.data.access_token;
localStorage.setItem('auth_token', token);
localStorage.setItem('user', JSON.stringify(response.data.user));
```

---

### 2. GET /api/user (Protected)

**Tujuan**: Dapatkan data user yang sedang login

**Headers**:
```
Authorization: Bearer <token>
```

**Response (Status 200)**:
```json
{
  "id": 1,
  "name": "Admin IT",
  "email": "admin@bulog.test",
  "role": "admin_it",
  "created_at": "2026-01-01T00:00:00Z"
}
```

**Frontend Usage**:
```javascript
const response = await axios.get('/api/user');
console.log(response.data.role); // 'admin_it'
```

---

### 3. POST /api/auth/logout (Protected)

**Tujuan**: Logout user (revoke token)

**Headers**:
```
Authorization: Bearer <token>
```

**Response (Status 200)**:
```json
{
  "message": "Logged out successfully"
}
```

**Frontend Usage**:
```javascript
await axios.post('/api/auth/logout');
localStorage.removeItem('auth_token');
localStorage.removeItem('user');
window.location.href = '/';
```

---

## 📦 ASSET ENDPOINTS

### 4. GET /api/assets (Protected)

**Tujuan**: Fetch daftar aset dengan opsional filter dan pagination

**Headers**:
```
Authorization: Bearer <token>
```

**Query Parameters**:
```
page=1                    # Pagination (default: 1)
per_page=10              # Items per page (default: 10)
search=laptop            # Search by nama or kode
kondisi=baik             # Filter by kondisi
jenis=laptop             # Filter by jenis (laptop/printer)
lokasi=Kantor1           # Filter by lokasi
```

**Example Requests**:
```bash
# Get semua aset
GET /api/assets

# Get aset dengan filter
GET /api/assets?page=1&per_page=10&kondisi=baik&jenis=laptop

# Search aset
GET /api/assets?search=Dell
```

**Response Option A (Paginated - Recommended)**:
```json
{
  "data": [
    {
      "id": 1,
      "kode_aset": "ASET-001",
      "nama_aset": "Laptop Dell XPS",
      "jenis": "laptop",
      "merk_type": "Dell XPS 13",
      "kondisi": "baik",
      "lokasi": "Kantor 1",
      "pic_id": 5,
      "pic_name": "Budi",
      "koordinat_lat": -6.1751,
      "koordinat_lng": 106.8650,
      "tgl_perolehan": "2024-01-15",
      "harga": 15000000,
      "created_at": "2026-01-01T00:00:00Z"
    }
  ],
  "current_page": 1,
  "per_page": 10,
  "total": 100,
  "last_page": 10
}
```

**Response Option B (Simple Array)**:
```json
[
  {
    "id": 1,
    "kode_aset": "ASET-001",
    "nama_aset": "Laptop Dell XPS",
    ...
  }
]
```

**[IMPORTANT]** Frontend & Backend must agree on one response format!

**Frontend Usage**:
```javascript
const response = await axios.get('/api/assets?page=1&kondisi=baik');

// If response is paginated:
const assets = response.data.data;
const currentPage = response.data.current_page;

// If response is array:
const assets = response.data;
```

---

### 5. POST /api/assets (Protected)

**Tujuan**: Buat aset baru

**Headers**:
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body**:
```json
{
  "kode_aset": "ASET-NEW-001",
  "nama_aset": "Laptop Baru",
  "jenis": "laptop",
  "merk_type": "Dell XPS 15",
  "serial_number": "SN12345678",
  "kondisi": "baik",
  "lokasi": "Kantor 1",
  "tgl_perolehan": "2026-06-29",
  "harga": 15000000,
  "keterangan": "Aset baru"
}
```

**Response (Status 201)**:
```json
{
  "id": 999,
  "kode_aset": "ASET-NEW-001",
  "nama_aset": "Laptop Baru",
  "jenis": "laptop",
  ...
}
```

**Possible Errors**:
- Status 422: Validation error (field invalid)
  ```json
  {
    "errors": {
      "kode_aset": ["The kode_aset has already been taken"],
      "harga": ["The harga must be an integer"]
    }
  }
  ```

**Frontend Usage**:
```javascript
try {
  const response = await axios.post('/api/assets', {
    kode_aset: 'ASET-NEW-001',
    nama_aset: 'Laptop Baru',
    jenis: 'laptop',
    kondisi: 'baik',
    lokasi: 'Kantor 1'
  });
  showToast('Asset created!', 'success');
} catch (error) {
  if (error.response?.status === 422) {
    console.error('Validation errors:', error.response.data.errors);
  }
}
```

---

### 6. GET /api/assets/{id} (Protected)

**Tujuan**: Get satu aset detail

**Headers**:
```
Authorization: Bearer <token>
```

**Response (Status 200)**:
```json
{
  "id": 1,
  "kode_aset": "ASET-001",
  "nama_aset": "Laptop Dell XPS",
  "jenis": "laptop",
  ...
}
```

**Possible Errors**:
- Status 404: Asset not found

**Frontend Usage**:
```javascript
const response = await axios.get(`/api/assets/${assetId}`);
console.log(response.data.nama_aset);
```

---

### 7. PUT /api/assets/{id} (Protected)

**Tujuan**: Update aset

**Headers**:
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body** (semua field opsional):
```json
{
  "nama_aset": "Laptop Dell XPS (Updated)",
  "kondisi": "rusak_ringan",
  "lokasi": "Kantor 2"
}
```

**Response (Status 200)**:
```json
{
  "id": 1,
  "kode_aset": "ASET-001",
  "nama_aset": "Laptop Dell XPS (Updated)",
  "kondisi": "rusak_ringan",
  "lokasi": "Kantor 2",
  ...
}
```

**Frontend Usage**:
```javascript
const response = await axios.put(`/api/assets/${assetId}`, {
  kondisi: 'rusak_ringan'
});
```

---

### 8. DELETE /api/assets/{id} (Protected)

**Tujuan**: Hapus aset

**Headers**:
```
Authorization: Bearer <token>
```

**Response (Status 200)**:
```json
{
  "message": "Asset deleted successfully"
}
```

**Frontend Usage**:
```javascript
await axios.delete(`/api/assets/${assetId}`);
showToast('Asset deleted', 'success');
```

---

## 📱 SCAN ENDPOINTS

### 9. POST /api/assets/{id}/scan (Protected)

**Tujuan**: Record QR code scan dengan lokasi

**Headers**:
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body**:
```json
{
  "latitude": -6.1751,
  "longitude": 106.8650,
  "scanned_at": "2026-06-29T10:15:30Z"
}
```

**Response (Status 200 or 201)**:
```json
{
  "asset": {
    "id": 1,
    "kode_aset": "ASET-001",
    "nama_aset": "Laptop Dell XPS",
    "kondisi": "baik",
    "pic_name": "Budi",
    "lokasi": "Kantor 1"
  },
  "scan": {
    "id": 555,
    "latitude": -6.1751,
    "longitude": 106.8650,
    "scanned_at": "2026-06-29T10:15:30Z"
  },
  "message": "Asset scanned successfully"
}
```

**Possible Errors**:
- Status 404: Asset not found

**Frontend Usage**:
```javascript
const response = await axios.post(`/api/assets/${assetId}/scan`, {
  latitude: position.coords.latitude,
  longitude: position.coords.longitude,
  scanned_at: new Date().toISOString()
});

console.log(response.data.asset.nama_aset); // Show asset name
```

---

### 10. GET /api/assets/{id}/qrcode (Protected)

**Tujuan**: Get QR code data untuk aset

**Headers**:
```
Authorization: Bearer <token>
```

**Response (Status 200)**:
```json
{
  "asset": {
    "id": 1,
    "kode_aset": "ASET-001",
    ...
  },
  "qr_text": "{\"id\":1,\"kode_aset\":\"ASET-001\",\"jenis\":\"laptop\",\"nama_aset\":\"Laptop Dell XPS\"}",
  "qr_data": "eyJpZCI6MSwiY29kZV9hc2V0IjoiQVNFVC0wMDEiLCJqZW5pcyI6ImxhcHRvcCIsIm5hbWFfYXNldCI6IkxhcHRvcCBEZWxsIFhQUyJ9"
}
```

**Frontend Usage**:
```javascript
const response = await axios.get(`/api/assets/${assetId}/qrcode`);
const qrPayload = JSON.parse(response.data.qr_text);
console.log(qrPayload.kode_aset); // Use untuk generate QR code
```

---

## 📊 DASHBOARD ENDPOINTS

### 11. GET /api/dashboard/summary (Protected)

**Tujuan**: Get ringkasan data untuk dashboard

**Headers**:
```
Authorization: Bearer <token>
```

**Response (Status 200)**:
```json
{
  "total_assets": 150,
  "total_laptops": 80,
  "total_printers": 50,
  "total_in_repair": 20,
  "active_pics": 5,
  "kondisi_breakdown": {
    "baik": 100,
    "rusak_ringan": 30,
    "rusak_berat": 15,
    "dalam_perbaikan": 5
  },
  "lokasi_breakdown": {
    "Kantor 1": 80,
    "Kantor 2": 70
  }
}
```

**Frontend Usage**:
```javascript
const response = await axios.get('/api/dashboard/summary');
document.getElementById('total-assets').textContent = response.data.total_assets;

// Draw chart
new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: Object.keys(response.data.kondisi_breakdown),
    datasets: [{
      data: Object.values(response.data.kondisi_breakdown)
    }]
  }
});
```

---

## 📄 REPORT ENDPOINTS

### 12. GET /api/reports/assets (Protected)

**Tujuan**: Get report aset dengan opsional filter dan format

**Headers**:
```
Authorization: Bearer <token>
```

**Query Parameters**:
```
format=preview         # preview | pdf | excel (default: preview)
kondisi=baik          # Filter by kondisi
jenis=laptop          # Filter by jenis
lokasi=Kantor1        # Filter by lokasi
date_from=2026-01-01  # Filter by date range
date_to=2026-12-31
```

**Example Requests**:
```bash
# Preview report
GET /api/reports/assets?format=preview

# Export to PDF
GET /api/reports/assets?format=pdf&kondisi=baik

# Export to Excel
GET /api/reports/assets?format=excel&jenis=laptop
```

**Response (format=preview, Status 200)**:
```json
{
  "data": [
    {
      "id": 1,
      "kode_aset": "ASET-001",
      "nama_aset": "Laptop Dell XPS",
      "jenis": "laptop",
      "kondisi": "baik",
      "lokasi": "Kantor 1",
      "pic_name": "Budi"
    }
  ],
  "total": 100,
  "generated_at": "2026-06-29T10:00:00Z"
}
```

**Response (format=pdf, Status 200)**:
- Returns binary PDF file
- Header: `Content-Type: application/pdf`
- Header: `Content-Disposition: attachment; filename="report.pdf"`

**Response (format=excel, Status 200)**:
- Returns binary Excel file
- Header: `Content-Type: application/vnd.ms-excel`
- Header: `Content-Disposition: attachment; filename="report.xlsx"`

**Frontend Usage**:
```javascript
// Preview report
const response = await axios.get('/api/reports/assets?format=preview');
console.log(response.data.data); // Array of assets

// Download PDF
window.location.href = '/api/reports/assets?format=pdf&kondisi=baik';

// Download Excel
window.location.href = '/api/reports/assets?format=excel';
```

---

## 👥 PIC ENDPOINTS (If implemented)

### 13. GET /api/pics (Protected)

**Tujuan**: Fetch daftar PIC

**Response (Status 200)**:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Budi",
      "email": "budi@bulog.test",
      "phone": "08123456789",
      "departemen": "IT",
      "assets_count": 10
    }
  ]
}
```

---

### 14. POST /api/pics (Protected - Admin only)

**Request Body**:
```json
{
  "name": "Budi Santoso",
  "email": "budi@bulog.test",
  "phone": "08123456789",
  "departemen": "IT"
}
```

**Response (Status 201)**:
```json
{
  "id": 1,
  "name": "Budi Santoso",
  ...
}
```

---

### 15. POST /api/assets/{id}/assign-pic (Protected - Admin only)

**Request Body**:
```json
{
  "pic_id": 1
}
```

**Response (Status 200)**:
```json
{
  "asset": {
    "id": 1,
    "pic_id": 1,
    "pic_name": "Budi",
    ...
  },
  "message": "PIC assigned successfully"
}
```

---

## 📋 AUDIT TRAIL ENDPOINTS (If implemented)

### 16. GET /api/asset-histories (Protected)

**Tujuan**: Fetch riwayat perubahan aset

**Query Parameters**:
```
asset_id=1            # Filter by asset
date_from=2026-01-01  # Filter by date
date_to=2026-12-31
per_page=20
```

**Response (Status 200)**:
```json
{
  "data": [
    {
      "id": 1,
      "asset_id": 1,
      "asset_name": "Laptop Dell",
      "action": "update",
      "field_changed": "kondisi",
      "old_value": "baik",
      "new_value": "rusak_ringan",
      "changed_by": "Budi",
      "changed_at": "2026-06-29T10:00:00Z"
    }
  ]
}
```

---

## 🔄 GENERAL RULES

### HTTP Status Codes
- **200**: Request berhasil
- **201**: Resource created
- **400**: Bad request (invalid input)
- **401**: Unauthorized (no token or invalid token)
- **403**: Forbidden (no permission)
- **404**: Resource not found
- **422**: Validation error
- **500**: Server error

### Response Format (Consistent)
```json
{
  "data": { ... } | [ ... ],
  "message": "Success or error message",
  "errors": { ... }  // If validation error
}
```

### Date Format
- ISO 8601: `2026-06-29T10:15:30Z` or `2026-06-29T10:15:30+07:00`
- Frontend should store as ISO, display in local time

### Pagination
```json
{
  "data": [ ... ],
  "current_page": 1,
  "per_page": 10,
  "total": 100,
  "last_page": 10
}
```

### Error Response
```json
{
  "message": "Validation error",
  "errors": {
    "email": ["Email already taken"],
    "password": ["Password must be at least 8 characters"]
  }
}
```

---

## 🔀 APPROVAL CHECKLIST

**To be signed off by both Fatin & Khansa:**

- [ ] Fatin: Agree with all endpoint specs
- [ ] Khansa: Agree with all endpoint specs
- [ ] Fatin: Implement according to spec
- [ ] Khansa: Test according to spec
- [ ] Both: Verify response formats match
- [ ] Both: Verify error handling works
- [ ] Both: End-to-end test all flows

---

**This is the SOURCE OF TRUTH for API integration.**  
**Any changes must be documented and agreed upon by both teams.**

*Created: 2026-06-29*  
*For: BULOG Asset Management System*
