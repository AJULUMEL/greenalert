# 🚨 GreenAlert - Incident Management System

A comprehensive **Audit & Incident Logs Management System** built with Laravel 11, designed for operational monitoring and incident tracking at Greenfields Indonesia.

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Quick Start](#quick-start)
- [Project Structure](#project-structure)
- [API Routes](#api-routes)
- [Database Migrations](#database-migrations)
- [Best Practices](#best-practices)
- [Performance Optimization](#performance-optimization)
- [Deployment](#deployment)

---

## Overview

**GreenAlert** is an enterprise-grade incident management system that enables real-time monitoring, prioritization, and tracking of operational incidents. It provides a responsive dashboard focused on audit trails, attention logic, and incident logs for critical situations.

### Use Case
- Incident reporting and tracking
- Severity-based prioritization
- Audit trail logging
- Dashboard monitoring
- Real-time alerts for critical incidents

---

## ✨ Features

### 1. **Authentication & Authorization**
- Secure admin login
- Role-based access control (Admin, Operator)
- Session management

### 2. **Incident Management (CRUD)**
- Create, read, update, delete incidents
- Soft delete functionality
- Search and filtering by severity, status
- Bulk export to CSV

### 3. **Severity Classification**
- **Low** - Minor issues
- **Medium** - Moderate issues
- **High** - Significant issues
- **Critical** - Critical issues

### 4. **Status Tracking**
- **Open** - Newly reported
- **On Progress** - Being handled
- **Resolved** - Completed

### 5. **Dashboard Monitoring**
- Total incidents count
- Critical unresolved badge
- Recent incidents table
- Recent audit logs panel
- Attention logic for critical situations

### 6. **Audit Trail**
- Complete activity logging
- User action tracking
- IP address logging
- Before/after comparison

---

## 🛠️ Technology Stack

- **Laravel 11** - PHP Framework
- **PHP 8.2+** - Language
- **MySQL 8.0** - Database
- **Redis** - Caching
- **Nginx** - Web Server
- **AdminLTE 3** - Dashboard UI
- **Blade Tables & Badges** - Dashboard presentation
 - **Docker** - (containerization support removed; artifacts archived under `archive/docker/`)

---

## 🚀 Quick Start

### Installation (5 minutes)

```bash
# 1. Clone repository
git clone <repository-url>
cd greenalert

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Create database
mysql -u root -p
CREATE DATABASE greenalert;
EXIT;

# 5. Update .env database credentials
DB_DATABASE=greenalert
DB_USERNAME=root
DB_PASSWORD=your_password

# 6. Run migrations and seed
php artisan migrate --seed

# 7. Build assets
npm run build

# 8. Start application
php artisan serve
# Open http://localhost:8000
```

**Default Credentials:**
```
Email: admin@greenalert.local
Password: admin123
```

---

## 📁 Project Structure

```
greenalert/
├── app/Http/Controllers/
│   ├── DashboardController.php
│   ├── IncidentController.php
│   └── ProfileController.php
├── app/Models/
│   ├── User.php
│   ├── Incident.php
│   └── AuditLog.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/views/
│   ├── dashboard.blade.php
│   └── incidents/
│       ├── index.blade.php
│       ├── create.blade.php
│       ├── edit.blade.php
│       └── show.blade.php
├── routes/
│   ├── web.php
│   └── auth.php
└── (docker-compose.yml removed; see `archive/docker/`)
```

---

## 🛣️ API Routes

### Incident Routes
```
GET    /incidents                      - List incidents
GET    /incidents/create               - Create form
POST   /incidents                      - Store incident
GET    /incidents/{id}                 - View incident
GET    /incidents/{id}/edit            - Edit form
PUT    /incidents/{id}                 - Update incident
DELETE /incidents/{id}                 - Delete incident
POST   /incidents/{id}/restore         - Restore deleted
GET    /incidents/export/csv           - Export CSV
```

### Dashboard
```
GET    /dashboard                      - Dashboard view
```

---

## 🗄️ Database Migrations

Run migrations:
```bash
php artisan migrate --seed
```

**Tables:**
- `users` - Admin and operator accounts
- `incidents` - Incident records with soft delete
- `audit_logs` - Activity tracking

---

## 🎯 Best Practices

### 1. Database Optimization
```php
// ✅ Use eager loading
$incidents = Incident::with('reportedBy')->get();

// ✅ Index frequently filtered columns
$incidents = Incident::where('severity', 'Critical')->get();
```

### 2. Security
- All input validated
- Passwords hashed with bcrypt
- CSRF protection enabled
- SQL injection prevented (ORM)

### 3. Code Quality
- Clean, maintainable code
- Proper separation of concerns
- Comprehensive error handling
- Meaningful comments

---

## ⚡ Performance Optimization

### Database Indexes
- `severity`, `status`, `incident_date`
- `reported_by` foreign key
- Full-text search on title/description

### Caching
- Dashboard statistics cached
- Query results optimized
- Asset minification enabled

### Pagination
- 15 items per page by default
- Reduces memory usage
- Improves page load time

---

<!-- Docker deployment instructions removed; artifacts archived under archive/docker/ -->

---

## 📦 Production Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for complete guide:
- Ubuntu 22.04 setup
- Nginx configuration
- SSL certificate setup
- Database backups
- Monitoring

---

## 🔍 Attention Logic

Critical incidents are prioritized by:
1. Database query ordering
2. UI visual highlighting (red)
3. Dashboard alert panels
4. Severity-based sorting

---

## 📚 Documentation

- [DEPLOYMENT.md](DEPLOYMENT.md) - Deployment guide
- [Laravel Docs](https://laravel.com/docs)
- [AdminLTE Docs](https://adminlte.io)

---

## 📞 Support

For issues, check:
1. [DEPLOYMENT.md](DEPLOYMENT.md) troubleshooting
2. Storage logs: `storage/logs/laravel.log`
3. Contact development team

---

**Version**: 1.0.0  
**Status**: Production Ready ✅  
**Last Updated**: May 21, 2024


## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
