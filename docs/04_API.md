# API Documentation

## Ringkasan Endpoint

| Area | Method | URL | Auth |
| --- | --- | --- | --- |
| Register | POST | /api/auth/register | No |
| Login | POST | /api/auth/login | No |
| Logout | POST | /api/auth/logout | Yes |
| Current User | GET | /api/user | Yes |
| List Assets | GET | /api/assets | Yes |
| Asset Detail | GET | /api/assets/{asset} | Yes |
| Create Asset | POST | /api/assets | Yes |
| Update Asset | PUT | /api/assets/{asset} | Yes |
| Delete Asset | DELETE | /api/assets/{asset} | Yes |
| Scan Asset | POST | /api/assets/{asset}/scan | Yes |
| Asset Location | GET | /api/assets/{asset}/location | Yes |
| QR Code | GET | /api/assets/{asset}/qrcode | Yes |
| List PICs | GET | /api/pics | Yes |
| Create PIC | POST | /api/pics | Yes |
| Update PIC | PUT | /api/pics/{pic} | Yes |
| Delete PIC | DELETE | /api/pics/{pic} | Yes |
| Assign PIC | POST | /api/assets/{asset}/assign-pic | Yes |
| List Notifications | GET | /api/notifications | Yes |
| Mark Notification Read | PATCH | /api/notifications/{notification}/read | Yes |
| Preview Report | GET | /api/reports/assets | Yes | 
| Export Excel | GET | /api/reports/assets?format=excel | Yes |
| Export PDF | GET | /api/reports/assets?format=pdf | Yes |
| Create Backup | POST | /api/backups | Yes |
| List Backups | GET | /api/backups | Yes |
| Verify Backups | GET | /api/backups/verify | Yes |
| Dashboard Summary | GET | /api/dashboard/summary | Yes |

## Authentication

### Register
- Method: `POST`
- URL: `/api/auth/register`
- Headers:
  - `Accept: application/json`
  - `Content-Type: application/json`
- Body:
```json
{
  "name": "Admin Baru",
  "email": "admin-baru-banget@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "role": "admin_it"
}
```
- Response:
```json
{
  "user": {
    "id": 34,
    "name": "Admin Baru",
    "email": "admin-baru-banget@example.com",
    "role": "admin_it",
    "createdAt": "2026-06-30T12:17:55+00:00",
    "updatedAt": "2026-06-30T12:17:55+00:00"
  },
  "token": "4|x...cd",
  "auth_token": "4|x...cd",
  "token_type": "Bearer"
}
```

### Login
- Method: `POST`
- URL: `/api/auth/login`
- Headers:
  - `Accept: application/json`
  - `Content-Type: application/json`
- Body:
```json
{
  "email": "admin-baru-banget@example.com",
  "password": "Password123!"
}
```
- Response:
```json
{
  "user": {
    "id": 34,
    "name": "Admin Baru",
    "email": "admin-baru-banget@example.com",
    "role": "admin_it",
    "createdAt": "2026-06-30T12:17:55+00:00",
    "updatedAt": "2026-06-30T12:17:55+00:00"
  },
  "token": "4|x...cd",
  "auth_token": "4|x...cd",
  "token_type": "Bearer"
}
```

### Logout
- Method: `POST`
- URL: `/api/auth/logout`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
{
  "message": "Logout berhasil."
}
```

### Current User
- Method: `GET`
- URL: `/api/user`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
{
  "id": 34,
  "name": "Admin Baru",
  "email": "admin-baru-banget@example.com",
  "role": "admin_it",
  "createdAt": "2026-06-30T12:17:55+00:00",
  "updatedAt": "2026-06-30T12:17:55+00:00"
}
```

## Assets

### List Assets
- Method: `GET`
- URL: `/api/assets`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
[
  {
    "id": 1,
    "kodeAset": "AST-100",
    "namaAset": "Laptop Test",
    "merkType": "Dell",
    "serialNumber": "SN001",
    "lokasi": "Gudang A",
    "koordinat": {
      "lat": -6.2,
      "lng": 106.816666
    },
    "kondisi": "baik",
    "tglPerolehan": "2024-01-01",
    "harga": 12000000,
    "keterangan": "Test asset",
    "jenis": "laptop",
    "qrCodePath": null,
    "picId": null,
    "pic": null,
    "createdAt": "2026-06-30T12:17:55+00:00",
    "updatedAt": "2026-06-30T12:17:55+00:00"
  }
]
```

### Asset Detail
- Method: `GET`
- URL: `/api/assets/{asset}`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
{
  "id": 1,
  "kodeAset": "AST-100",
  "namaAset": "Laptop Test",
  "merkType": "Dell",
  "serialNumber": "SN001",
  "lokasi": "Gudang A",
  "koordinat": {
    "lat": -6.2,
    "lng": 106.816666
  },
  "kondisi": "baik",
  "tglPerolehan": "2024-01-01",
  "harga": 12000000,
  "keterangan": "Test asset",
  "jenis": "laptop",
  "qrCodePath": null,
  "picId": null,
  "pic": null,
  "createdAt": "2026-06-30T12:17:55+00:00",
  "updatedAt": "2026-06-30T12:17:55+00:00"
}
```

### Create Asset
- Method: `POST`
- URL: `/api/assets`
- Headers:
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
- Body:
```json
{
  "kode_aset": "AST-101",
  "nama_aset": "Printer Test",
  "merk_type": "HP",
  "serial_number": "SN002",
  "lokasi": "Gudang B",
  "koordinat_lat": -6.3,
  "koordinat_lng": 106.9,
  "kondisi": "baik",
  "tgl_perolehan": "2024-02-01",
  "harga": 4500000,
  "keterangan": "Printer test",
  "jenis": "printer"
}
```
- Response: same asset payload format as detail.

### Update Asset
- Method: `PUT`
- URL: `/api/assets/{asset}`
- Headers:
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
- Body: same fields as Create Asset.
- Response: same asset payload.

### Delete Asset
- Method: `DELETE`
- URL: `/api/assets/{asset}`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
{
  "message": "Asset deleted successfully."
}
```

### Scan Asset
- Method: `POST`
- URL: `/api/assets/{asset}/scan`
- Headers:
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
- Body:
```json
{
  "latitude": -6.2,
  "longitude": 106.816666
}
```
- Response:
```json
{
  "message": "Scan berhasil, lokasi aset diperbarui.",
  "asset": { ... },
  "scannedAt": "2026-06-30 14:00:00"
}
```

### Asset Location
- Method: `GET`
- URL: `/api/assets/{asset}/location`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
{
  "assetId": 1,
  "lokasi": "Gudang A",
  "latitude": -6.2,
  "longitude": 106.816666,
  "lastScan": {
    "latitude": -6.2,
    "longitude": 106.816666,
    "scanned_at": "2026-06-30 14:00:00"
  }
}
```

### Asset QR Code
- Method: `GET`
- URL: `/api/assets/{asset}/qrcode`
- Headers:
  - `Authorization: Bearer <token>`
- Response: download file with `Content-Type: image/svg+xml`

### Dashboard Summary
- Method: `GET`
- URL: `/api/dashboard/summary`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
{
  "totalAssets": 2,
  "totalLaptops": 1,
  "totalPrinters": 1,
  "totalPics": 2,
  "conditionCounts": {
    "BAIK": 1,
    "RUSAK_RINGAN": 0,
    "RUSAK_BERAT": 1,
    "DALAM_PERBAIKAN": 0,
    "TIDAK_AKTIF": 0
  },
  "latestAssets": [
    {
      "id": 2,
      "kodeAset": "AST-102",
      "namaAset": "Printer B",
      "kondisi": "rusak_berat",
      "lokasi": "Gudang B",
      "pic": null,
      "createdAt": "2026-06-30T12:00:00+00:00"
    }
  ]
}
```

## PICs

### List PICs
- Method: `GET`
- URL: `/api/pics`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
[
  {
    "id": 1,
    "nama": "Budi Santoso",
    "jabatan": "PIC",
    "email": "budi.santoso@example.com",
    "telepon": "081234567890",
    "createdAt": "...",
    "updatedAt": "..."
  }
]
```

### Create PIC
- Method: `POST`
- URL: `/api/pics`
- Headers:
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
- Body:
```json
{
  "nama": "Budi Santoso",
  "jabatan": "PIC",
  "email": "budi.santoso@example.com",
  "telepon": "081234567890"
}
```
- Response: same PIC payload.

### Update PIC
- Method: `PUT`
- URL: `/api/pics/{pic}`
- Headers:
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
- Body: same fields as Create PIC.
- Response: same PIC payload.

### Delete PIC
- Method: `DELETE`
- URL: `/api/pics/{pic}`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
{
  "message": "PIC deleted successfully."
}
```

### Assign PIC to Asset
- Method: `POST`
- URL: `/api/assets/{asset}/assign-pic`
- Headers:
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
- Body:
```json
{
  "pic_id": 1,
  "alasan": "Penugasan ulang PIC"
}
```
- Response: asset payload with `pic` field.

## Notifications

### List Notifications
- Method: `GET`
- URL: `/api/notifications`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
[
  {
    "id": 1,
    "userId": null,
    "role": "user_pic",
    "title": "PIC Note",
    "message": "PIC notification",
    "data": null,
    "isRead": false,
    "createdAt": "...",
    "updatedAt": "..."
  }
]
```

### Mark Notification Read
- Method: `PATCH`
- URL: `/api/notifications/{notification}/read`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response:
```json
{
  "id": 1,
  "userId": null,
  "role": "user_pic",
  "title": "PIC Note",
  "message": "PIC notification",
  "data": null,
  "isRead": true,
  "createdAt": "...",
  "updatedAt": "..."
}
```

## Reports

### Preview Asset Report
- Method: `GET`
- URL: `/api/reports/assets`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Optional query params:
  - `kondisi`
  - `jenis`
  - `lokasi`
  - `pic_id`
- Response: array of asset payloads.

### Download Excel Report
- Method: `GET`
- URL: `/api/reports/assets?format=excel`
- Headers:
  - `Authorization: Bearer <token>`
- Response: file download `aset-report.xlsx`

### Download PDF Report
- Method: `GET`
- URL: `/api/reports/assets?format=pdf`
- Headers:
  - `Authorization: Bearer <token>`
- Response: file download `aset-report.pdf`

> Note: Report endpoint `/api/reports/assets` is protected by role middleware and should be accessed only by users with role `admin_it` or `manajemen`.

## Backup

### Create Backup
- Method: `POST`
- URL: `/api/backups`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response: file download or JSON based on controller.

### List Backups
- Method: `GET`
- URL: `/api/backups`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response: JSON listing backup files.

### Verify Backups
- Method: `GET`
- URL: `/api/backups/verify`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer <token>`
- Response: JSON result of integrity check.

> Note: Backup endpoints are currently protected by `auth:sanctum` and are intended for admin-level users.

## Notes
- Semua endpoint selain `auth/register` dan `auth/login` wajib menggunakan header `Authorization: Bearer <token>`.
- Field respons utama menggunakan `camelCase`.
- Input create/update asset/pic tetap menggunakan `snake_case` sesuai validasi backend.
