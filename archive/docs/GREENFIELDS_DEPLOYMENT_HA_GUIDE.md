# Greenfields Deployment and HA Guide

## 1. Purpose
Dokumen ini menjelaskan cara menjalankan GreenAlert pada environment lokal dan production dengan fokus pada deployment yang stabil dan mudah dipelihara.

## 2. Target Environment
- OS: Ubuntu 22.04 LTS
- Web server: Nginx
- App runtime: PHP 8.2+
- Database: MySQL 8.0+
- Cache: Redis
- Frontend build: Node.js 18+

## 3. Local Deployment Steps
1. Copy file `.env.example` ke `.env`.
2. Jalankan `composer install`.
3. Jalankan `npm install`.
4. Generate app key.
5. Jalankan migrasi dan seeder.
6. Build asset frontend.
7. Jalankan aplikasi dengan `php artisan serve`.

## 4. Production Deployment Steps
1. Update server dan install dependencies system.
2. Install PHP, Nginx, MySQL, Redis, Composer, dan Node.js.
3. Clone repository ke `/var/www/greenalert`.
4. Install package production.
5. Konfigurasi `.env` production.
6. Jalankan migrasi dan seed bila perlu.
7. Set permission storage dan cache.
8. Konfigurasi Nginx virtual host.
9. Enable SSL jika sudah siap live.

## 5. High Availability Notes
- Gunakan service restart policy pada deployment container jika dipakai Docker.
- Simpan backup database secara berkala.
- Pisahkan environment development dan production.
- Gunakan Redis untuk cache agar beban query berkurang.
- Siapkan log monitoring untuk error dan aktivitas penting.

## 6. Backup Strategy
- Backup database harian.
- Backup file penting mingguan.
- Simpan minimal satu salinan di lokasi terpisah.

## 7. Monitoring Checklist
- Aplikasi bisa diakses.
- Database aktif.
- Storage permission benar.
- Cache berjalan.
- Audit log bisa ditulis.
- CSV export berjalan.

## 8. Risk Notes
- Jika data semakin besar, indexing dan query optimization harus dijaga.
- Jika traffic meningkat, aplikasi bisa dipindah ke VM atau container orchestration.
- Jika tim bertambah, role dan permission perlu diperluas.
