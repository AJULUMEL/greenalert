# Greenfields Non-Functional Requirements

## 1. Performance
- Sistem harus menampilkan dashboard dengan cepat untuk data operasional harian.
- Query daftar incident harus dioptimalkan dengan pagination dan filtering.
- Statistik dashboard harus diambil dari query yang ringkas dan efisien.

## 2. Scalability
- Arsitektur harus memungkinkan scale horizontal atau vertical di masa depan.
- Struktur folder harus memudahkan pemisahan logic saat fitur bertambah.
- Skema database harus mudah dikembangkan untuk entity tambahan.

## 3. Availability
- Sistem harus dapat dijalankan pada environment lokal maupun production server.
- Deployment harus mendukung restart service tanpa merusak data.
- Backup database dan file penting harus direncanakan.

## 4. Maintainability
- Kode harus dipisah ke controller, service, repository, DTO, enum, dan trait.
- Business logic tidak boleh menumpuk di controller.
- Struktur kode harus mudah dibaca dan mudah diuji.

## 5. Usability
- UI harus sederhana dan mudah dipahami oleh tim operasional.
- Informasi penting harus mudah terlihat pada dashboard.
- Filter, search, dan status indicator harus jelas.

## 6. Data Integrity
- Data incident harus tervalidasi sebelum disimpan.
- Soft delete harus digunakan agar data bisa dipulihkan.
- Audit trail harus menjaga jejak perubahan.

## 7. Security
- Akses aplikasi harus dibatasi untuk user yang login.
- Password harus disimpan dengan hashing Laravel.
- Input harus divalidasi untuk mencegah data tidak valid.
- Aktivitas penting harus tercatat pada audit log.

## 8. Deployment Readiness
- Aplikasi harus dapat dijalankan dengan Nginx, PHP, MySQL, dan Redis.
- Environment production harus menggunakan konfigurasi yang terpisah.
- Proses build dan deploy harus terdokumentasi.

## 9. Non-Functional Scope for MVP
- Performance baseline yang stabil.
- Struktur yang mudah dirawat.
- Dataset dan log yang konsisten.
- Dashboard yang ringan.
