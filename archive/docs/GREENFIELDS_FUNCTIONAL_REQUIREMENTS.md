# Greenfields Functional Requirements

## 1. Tujuan Sistem
Sistem GreenAlert dibuat untuk membantu tim operasional Greenfields memantau, mencatat, memprioritaskan, dan menindaklanjuti incident secara cepat dan terstruktur.

## 2. Functional Requirements
### 2.1 Incident Management
- Sistem harus dapat menambahkan incident baru.
- Sistem harus dapat menampilkan daftar incident.
- Sistem harus dapat melihat detail incident.
- Sistem harus dapat mengubah incident.
- Sistem harus dapat menghapus incident secara soft delete.
- Sistem harus dapat memulihkan incident yang terhapus.

### 2.2 Validasi dan Data Quality
- Sistem harus memvalidasi field utama saat create dan update.
- Sistem harus menjaga konsistensi severity dan status.
- Sistem harus menyimpan data incident dengan format yang seragam.

### 2.3 Monitoring and Prioritization
- Sistem harus menampilkan incident berdasarkan severity.
- Sistem harus menampilkan incident berdasarkan status.
- Sistem harus menyorot incident critical yang belum selesai.
- Sistem harus menyediakan dashboard ringkas untuk melihat kondisi operasional.

### 2.4 Search and Filtering
- Sistem harus mendukung pencarian incident.
- Sistem harus mendukung filter severity.
- Sistem harus mendukung filter status.
- Sistem harus mendukung pagination pada daftar incident.

### 2.5 Audit Trail
- Sistem harus mencatat aktivitas create, update, view, delete, dan restore.
- Sistem harus menyimpan user pelaku, IP address, dan user agent.
- Sistem harus menampilkan riwayat audit pada halaman detail incident.

### 2.6 Reporting
- Sistem harus dapat mengekspor data incident ke CSV.
- Sistem harus menampilkan statistik total incident, severity breakdown, dan status breakdown.

## 3. Functional Scope for MVP
- CRUD incident.
- Dashboard monitoring.
- Search, filter, pagination.
- Audit trail dasar.
- CSV export.

## 4. Acceptance Notes
- Seluruh data utama harus dapat dikelola dari satu aplikasi web.
- Pengguna operasional harus bisa memantau incident tanpa proses manual yang berulang.
- Data penting harus mudah dicari dan diprioritaskan.
