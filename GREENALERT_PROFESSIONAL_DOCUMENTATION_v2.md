# GreenAlert
## Incident Monitoring & Management System

### Professional Documentation

---

## TABLE OF CONTENTS

1. [Introduction](#1-introduction)
## 5. System Design, Workflow & Architecture

This content has been moved to a separate document: [GREENALERT_SYSTEM_DESIGN_WORKFLOW.md](GREENALERT_SYSTEM_DESIGN_WORKFLOW.md)

Please open the linked file for the Entity Relationship Diagram, System Architecture diagrams, and Workflow diagrams.
│  • Repositories                                             │
│  • Business Logic                                           │
└──────────────────────────┬──────────────────────────────────┘
                           │ Eloquent ORM
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ▼                  ▼                  ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────────┐
│   MYSQL DB   │  │ REDIS CACHE  │  │   FILE STORAGE   │
│              │  │              │  │                  │
│ • Users      │  │ • Sessions   │  │ • Application    │
│ • Incidents  │  │ • Query Data │  │   Logs           │
│ • Audit Logs │  │ • Cache Tags │  │ • Uploaded Files │
└──────────────┘  └──────────────┘  └──────────────────┘
```

### 6.3 Deployment Architecture

Deployment architecture diagrams and orchestration examples have been moved to a dedicated document. See [GREENALERT_SYSTEM_DESIGN_WORKFLOW.md](GREENALERT_SYSTEM_DESIGN_WORKFLOW.md) and deployment instructions in [GREENALERT_DEPLOYMENT_HA_GUIDELINES.md](GREENALERT_DEPLOYMENT_HA_GUIDELINES.md).

### 6.4 Component Interactions

```
USER REQUEST FLOW:
─────────────────

1. Browser sends HTTP Request
                    ↓
2. Nginx receives request (port 80/443)
                    ↓
3. Nginx routes to PHP-FPM
                    ↓
4. Router matches route to Controller
                    ↓
5. Controller processes request
        ↓           ↓           ↓
     Auth        Service      Business
     Check       Layer        Logic
        │           │           │
        └───────┬───┴────┬──────┘
                │        │
        Check Cache  Query DB
        (Redis)     (MySQL)
                │        │
        ┌───────┴─────┴──────────┐
        │                        │
5. Service processes data
6. Controller formats response
                    ↓
7. View renders (Blade template)
                    ↓
8. Response sent to Browser
                    ↓
9. Browser renders HTML/CSS/JS
                    ↓
10. User sees Dashboard/Interface

DATA CHANGE FLOW:
─────────────────

1. User updates incident
                    ↓
2. Form submission to Controller
                    ↓
3. Validation (Request class)
                    ↓
4. HasAuditLog trait captures old values
                    ↓
5. Service updates in Database
                    ↓
6. HasAuditLog trait creates audit record
                    ↓
7. Cache invalidated
                    ↓
8. Response sent to user
                    ↓
9. Dashboard reflects changes
```

---

## 7. WORKFLOW DIAGRAM

### 7.1 Incident Lifecycle Workflow

```
USER START
    │
    ▼
    ┌─────────────────────────┐
    │   ACCESS DASHBOARD      │
    │                         │
    │ • View statistics       │
    │ • Check critical alerts │
    │ • Browse incidents      │
    └────────┬────────────────┘
             │
             ├─ View Incident Details
             │      ↓
             │  ┌──────────────────┐
             │  │ View History &   │
             │  │ Audit Logs       │
             │  └──────────────────┘
             │      ↓
             │  Back to Dashboard
             │
             ├─ Create New Incident
             │      ↓
             │  ┌──────────────────────────┐
             │  │ Fill Incident Form:      │
             │  │ • Title                  │
             │  │ • Description            │
             │  │ • Severity               │
             │  │ • Incident Date          │
             │  └──────────────────────────┘
             │      ↓
             │  ┌──────────────────────────┐
             │  │ VALIDATION CHECK         │
             │  │ • Required fields        │
             │  │ • Field constraints      │
             │  └──────────────────────────┘
             │      ↓
             │      NO → Display Error
             │      ↑      ↓
             │      └──Back to Form
             │
             │      YES
             │      ▼
             │  ┌──────────────────────────┐
             │  │ CREATE INCIDENT          │
             │  │ • Save to database       │
             │  │ • Set status: Open       │
             │  │ • Set reporter: Current  │
             │  │   user                   │
             │  └──────────────────────────┘
             │      ↓
             │  ┌──────────────────────────┐
             │  │ AUDIT LOGGING            │
             │  │ • Log: CREATE action     │
             │  │ • User: current user     │
             │  │ • IP: user IP            │
             │  │ • New values: incident   │
             │  └──────────────────────────┘
             │      ↓
             │  ┌──────────────────────────┐
             │  │ SUCCESS MESSAGE          │
             │  │ "Incident created"       │
             │  └──────────────────────────┘
             │      ↓
             │  Redirect to Dashboard
             │
             ├─ Manage Incident
             │      ↓
             │  ┌──────────────────────────┐
             │  │ View Incident List       │
             │  └──────────────────────────┘
             │      ↓
             │  ┌──────────────────────────┐
             │  │ APPLY FILTERS            │
             │  │ • By severity            │
             │  │ • By status              │
             │  │ • By search term         │
             │  └──────────────────────────┘
             │      ↓
             │  Select Action:
             │      ├─ Edit Incident
             │      │      ↓
             │      │  Load incident data
             │      │      ↓
             │      │  Update form fields
             │      │      ↓
             │      │  Validation
             │      │      ├─ Error: Back to form
             │      │      └─ Valid: Continue
             │      │      ↓
             │      │  UPDATE in database
             │      │      ↓
             │      │  LOG: UPDATE action
             │      │      ↓
             │      │  Show: "Updated"
             │      │
             │      ├─ Delete Incident
             │      │      ↓
             │      │  Confirm action
             │      │      ├─ Cancel: Back
             │      │      └─ Confirm
             │      │      ↓
             │      │  SOFT DELETE
             │      │      ↓
             │      │  LOG: DELETE action
             │      │      ↓
             │      │  Show: "Deleted"
             │      │
             │      └─ View Details
             │             ↓
             │         Show all info
             │         + Audit trail
             │
             └─ Manage Incidents (Admin)
                    ↓
                Manage Users
                    ↓
                Create/Edit/Delete
                Users
```

### 7.2 Status Change Workflow

```
INCIDENT STATUS PROGRESSION:

┌─────────┐
│  OPEN   │  ← Incident just created
└────┬────┘
     │ (Update status)
     ▼
┌──────────────┐
│ ON PROGRESS  │  ← Team is handling
└────┬─────────┘
     │ (Update status)
     ▼
┌──────────┐
│ RESOLVED │  ← Completed
└──────────┘

EACH STATUS CHANGE:
• Captured in audit log
• Timestamp recorded
• User ID recorded
• Previous status saved
• New status saved
```

### 7.3 Dashboard Update Flow

```
DASHBOARD LOAD:
───────────────

1. User navigates to /dashboard
            ↓
2. DashboardController called
            ↓
3. Auth check (user logged in?)
            ├─ No → Redirect to login
            └─ Yes ↓
4. Check Redis cache for data
            ├─ Cache hit → Use cached data
            └─ Cache miss ↓
5. DashboardService query database
            ├─ Count total incidents
            ├─ Count critical incidents
            ├─ Count urgent incidents
            ├─ Count by status
            ├─ Get severity breakdown
            ├─ Get 30-day trend
            └─ Get recent incidents
            ↓
6. Calculate statistics
            ↓
7. Format data for charts/tables
            ↓
8. Store in Redis cache (5 min TTL)
            ↓
9. Pass to Blade view
            ↓
10. Render HTML with AdminLTE
            ↓
11. JavaScript loads charts (Chart.js)
            ↓
12. Display to user
            ↓
13. User can interact with dashboard
```

### 7.4 Filtering & Search Workflow

```
USER FILTERS INCIDENTS:
──────────────────────

1. User on incidents list page
            ↓
2. Select filters:
   • Severity: Critical
   • Status: Open
   • Search: "server"
            ↓
3. Form submitted to:
   GET /incidents?severity=Critical&status=Open&search=server
            ↓
4. IncidentController@index called
            ↓
5. Create FilterDTO with parameters
            ↓
6. IncidentService::getPaginated(FilterDTO)
            ↓
7. IncidentRepository applies filters:
   ├─ WHERE severity = 'Critical'
   ├─ AND status = 'Open'
   ├─ AND (title LIKE '%server%'
   │       OR description LIKE '%server%')
   ├─ ORDER BY severity DESC
   └─ LIMIT 10 OFFSET 0
            ↓
8. Query executed on database
            ↓
9. Results formatted with:
   • Severity badges
   • Status badges
   • Reporter names
            ↓
10. Pagination links generated
            ↓
11. Results sent to view
            ↓
12. Display filtered table to user
            ↓
13. User can:
    • Click incident for details
    • Apply different filters
    • Sort results
    • Change pagination
```

---

## 8. DASHBOARD OVERVIEW

### 8.1 Dashboard Layout

The GreenAlert dashboard provides a comprehensive operational monitoring interface with the following main sections:

**Header:**
- Application logo and name
- Navigation menu
- User profile dropdown
- Logout button

**Main Dashboard Content:**

#### **Section 1: Critical Alert Banner**
```
┌──────────────────────────────────────────────────────┐
│ 🔴 CRITICAL ALERT: 3 Critical Unresolved Incidents  │
│ Immediate action required!                          │
└──────────────────────────────────────────────────────┘
```
- Displays prominently if critical incidents exist
- Shows count of critical incidents
- Red background for visibility
- Clickable link to critical incidents list

#### **Section 2: Key Metrics Cards**
```
┌─────────────┬─────────────┬─────────────┬──────────────┐
│   TOTAL     │  CRITICAL   │   URGENT    │     OPEN     │
│ INCIDENTS   │ INCIDENTS   │ INCIDENTS   │  INCIDENTS   │
│             │             │             │              │
│     127     │      3      │      12     │      45      │
│             │   (Red)     │  (Orange)   │    (Blue)    │
└─────────────┴─────────────┴─────────────┴──────────────┘

┌─────────────┬─────────────┬──────────────────────────┐
│ IN PROGRESS │   RESOLVED  │    USER INFORMATION      │
│ INCIDENTS   │ INCIDENTS   │                          │
│             │             │  Name: John Operator     │
│      52     │      30     │  Role: Operator          │
│  (Yellow)   │   (Green)   │  Last Login: May 24 14:30│
└─────────────┴─────────────┴──────────────────────────┘
```

#### **Section 3: Statistical Charts**
```
┌──────────────────────────┬──────────────────────────┐
│  SEVERITY DISTRIBUTION   │  STATUS DISTRIBUTION     │
│        (Pie Chart)       │      (Pie Chart)         │
│                          │                          │
│  Critical: 3 (2.4%)      │  Open: 45 (35.4%)        │
│  High: 12 (9.4%)         │  In Progress: 52 (41%)   │
│  Medium: 45 (35.4%)      │  Resolved: 30 (23.6%)    │
│  Low: 67 (52.8%)         │                          │
│                          │  [Pie Chart Visual]      │
│  [Pie Chart Visual]      │                          │
└──────────────────────────┴──────────────────────────┘

┌──────────────────────────────────────────────────────┐
│   30-DAY INCIDENT TREND (Line Chart)                 │
│                                                      │
│   20 ├─────────────┐                                │
│      │          ╱╲  ╲                               │
│   15 ├─        ╱  ╲  ╲─╮                           │
│      │       ╱      ╲   ╲                           │
│   10 ├─    ╱          ╲   ╲───┐                     │
│      │   ╱              ╲      ╲                     │
│    5 ├─╱                  ╲      ╲___╮              │
│      │                        ╲        ╲            │
│    0 └────────────────────────────────────────►     │
│      May 1    May 10   May 20   May 24              │
│                                                      │
│   Legend:                                            │
│   ─── Critical  ─── High  ─── Medium  ─── Low       │
└──────────────────────────────────────────────────────┘
```

#### **Section 4: Recent Incidents Table**
```
┌──────┬──────────────────┬────────────┬──────────┬──────────────┐
│  ID  │     TITLE        │  SEVERITY  │  STATUS  │    DATE      │
├──────┼──────────────────┼────────────┼──────────┼──────────────┤
│ 457  │ Database Down    │ 🔴 Crit.   │ Open     │ 2026-05-24   │
│ 456  │ API Error Rate   │ 🟠 High    │ In Prog. │ 2026-05-24   │
│ 455  │ Slow Query       │ 🔵 Medium  │ Resolved │ 2026-05-23   │
│ 454  │ Email Config     │ 🟢 Low     │ Resolved │ 2026-05-23   │
│ 453  │ Cache Timeout    │ 🟠 High    │ Open     │ 2026-05-22   │
│ ...  │ ...              │ ...        │ ...      │ ...          │
└──────┴──────────────────┴────────────┴──────────┴──────────────┘

Pagination: [Previous] 1 2 3 4 5 [Next]
```

#### **Section 5: Critical Incidents List**
```
┌────────────────────────────────────────────────────────┐
│ 🔴 CRITICAL INCIDENTS (Top 5)                         │
├────────────────────────────────────────────────────────┤
│                                                        │
│ 1. Database Connection Pool Exhausted                 │
│    Reported by: Admin John | Date: 2026-05-24 14:22  │
│    [View Details] [Edit] [Delete]                    │
│                                                        │
│ 2. Primary Server Down - Production                   │
│    Reported by: Operator Mike | Date: 2026-05-24 13:15│
│    [View Details] [Edit] [Delete]                    │
│                                                        │
│ 3. Network Connectivity Lost                          │
│    Reported by: Operator Sarah | Date: 2026-05-23 22:30
│    [View Details] [Edit] [Delete]                    │
│                                                        │
└────────────────────────────────────────────────────────┘
```

#### **Section 6: Urgent Incidents Cards**
```
┌──────────────────────┬──────────────────────┐
│  URGENT INCIDENTS    │                      │
│  (High + Critical)   │                      │
│  Total: 12           │  ID: 457             │
│                      │  Title: Database     │
│ Quick Stats:         │  Down                │
│ • Unresolved: 8      │  Severity: Critical  │
│ • In Progress: 4     │  Status: Open        │
│ • Avg Age: 3h 45m   │  [Details] [Actions] │
│                      │                      │
│ [View All Urgent]   │                      │
└──────────────────────┴──────────────────────┘
```

### 8.2 Dashboard Features

**Real-Time Updates:**
- Statistics update when incidents are created/modified
- Charts refresh automatically
- Badge counts accurate to seconds

**Interactive Elements:**
- Click incident ID to view details
- Click severity/status badges to filter
- Export data to CSV
- Quick action buttons

**Responsive Design:**
- Works on desktop, tablet, mobile
- Adaptive layout for different screen sizes
- Touch-friendly buttons and links

**Performance:**
- Lightweight (< 100KB)
- Fast load time (< 2 seconds)
- Caching of dashboard data
- Lazy loading of charts

---

docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan storage:link
## 9. Deployment & High Availability (HA)

Detailed deployment instructions, Docker usage, security hardening, and scaling/HA guidance have been moved to a separate document: [GREENALERT_DEPLOYMENT_HA_GUIDELINES.md](GREENALERT_DEPLOYMENT_HA_GUIDELINES.md)

Please open the linked file for full prerequisites, installation steps, Docker compose commands, SSL setup, optimization commands, and scaling/HA options.
## 10. CONCLUSION

### 10.1 Summary

GreenAlert successfully delivers an enterprise-grade incident monitoring and management system tailored to Greenfields Indonesia's operational needs.

**Key Achievements:**

✅ **Centralized Incident Management**
- Single source of truth for all operational incidents
- Comprehensive incident lifecycle tracking
- Complete audit trails for compliance

✅ **Intelligent Prioritization**
- Severity-based incident classification
- Critical alert notifications
- Urgent incident highlighting

✅ **Real-Time Visibility**
- Interactive operational dashboard
- Real-time statistics and trends
- 30-day incident analytics

✅ **Enterprise Architecture**
- Clean 3-tier architecture
- Scalable and maintainable codebase
- Production-ready infrastructure
- Security-first design

✅ **Compliance & Auditing**
- Complete audit trails for all operations
- User action tracking
- IP address and user agent logging
- Change history for incident modifications

### 10.2 System Strengths

1. **User-Centric Design**
   - Intuitive interface for operators
   - Quick incident creation and management
   - Administrative controls for system management

2. **High Performance**
   - Dashboard loads in < 2 seconds
   - Optimized database queries
   - Redis caching for frequently accessed data
   - Responsive UI with pagination

3. **Security & Compliance**
   - Role-based access control
   - Secure authentication (bcrypt passwords)
   - HTTPS/TLS encryption
   - Complete audit trail for investigations

4. **Scalability**
   - Docker containerization
   - Horizontal scaling capability
   - Database optimization for large datasets
   - Caching layer for performance

5. **Maintainability**
   - Clean code architecture
   - Service layer pattern
   - Repository pattern for data access
   - Comprehensive documentation

### 10.3 Technical Stack Advantages

| Component | Advantage |
|-----------|-----------|
| **Laravel 11** | Modern, secure, well-documented framework |
| **PHP 8.2** | High performance, strong typing support |
| **MySQL 8.0** | Reliable, scalable relational database |
| **Nginx** | High-performance web server |
| **Docker** | Easy deployment and scaling |
| **AdminLTE** | Professional dashboard template |
| **Redis** | Fast caching and session management |

### 10.4 Future Enhancements

Potential improvements for future versions:

**Phase 2:**
- API REST endpoints for mobile apps
- Real-time notifications (WebSocket)
- Mobile application (iOS/Android)
- Advanced reporting and analytics

**Phase 3:**
- Machine learning for anomaly detection
- Automated incident response workflows
- Integration with third-party monitoring tools
- Multi-tenant support for resellers

**Phase 4:**
- Distributed system for enterprise deployments
- Advanced role-based permissions
- Custom fields for incidents
- Incident categorization and templates

### 10.5 Deployment Ready

GreenAlert is **production-ready** and can be deployed to:

✅ **Traditional Servers**
- Ubuntu 22.04 LTS with manual installation
- High availability with reverse proxy

✅ **Containerized Environments**
- Containerized deployment options have been removed from the main documentation and archived. See project `archive/` for retained artifacts.

✅ **Cloud Platforms**
- Azure App Service / Container Instances
- AWS EC2 / ECS / Elastic Beanstalk
- Google Cloud / Cloud Run
- DigitalOcean / Heroku

### 10.6 Final Remarks

GreenAlert represents a significant improvement in operational incident management for Greenfields Indonesia. The system is:

- **Well-Architected**: Following industry best practices and design patterns
- **Secure**: Implementing security at every layer
- **Performant**: Optimized for speed and scalability
- **Maintainable**: Clean code and comprehensive documentation
- **User-Friendly**: Intuitive interface for all user types

The application is ready for immediate deployment and will provide immediate value in improving operational visibility, response efficiency, and compliance with audit requirements.

---

## APPENDICES

### A. Technology Versions

```
Application: GreenAlert v1.0
Laravel:     11.x
PHP:         8.2+
MySQL:       8.0+
Node.js:     18 LTS+
Nginx:       1.18+
Ubuntu:      22.04 LTS
```

### B. File Structure

```
greenalert/
├── app/
│   ├── DTOs/                  (Data Transfer Objects)
│   ├── Enums/                 (Status, Severity enums)
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/          (Form validation)
│   │   └── Middleware/
│   ├── Models/                (Eloquent models)
│   ├── Repositories/          (Data access layer)
│   ├── Services/              (Business logic)
│   ├── Traits/                (Shared functionality)
│   └── Providers/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   ├── css/                   (Tailwind CSS)
│   ├── js/                    (JavaScript)
│   └── views/                 (Blade templates)
├── routes/
│   ├── web.php                (Web routes)
│   ├── auth.php               (Auth routes)
│   └── console.php
├── tests/
│   ├── Feature/
│   └── Unit/
├── config/                    (Configuration files)
├── public/                    (Public assets)
├── storage/                   (Logs, cache, uploads)
├── bootstrap/                 (Framework bootstrap)
<!-- Docker artifacts removed and archived under archive/docker/ -->
├── .env.example
└── README.md
```

### C. Default Credentials (Development)

> ⚠️ **IMPORTANT**: Change these credentials immediately in production!

**Admin User:**
```
Email:    admin@greenalert.local
Password: adminpass123
Role:     Admin
```

**Operator User:**
```
Email:    operator@greenalert.local
Password: operatorpass123
Role:     Operator
```

### D. Common Commands

```bash
# Development
php artisan serve                    # Start dev server
php artisan tinker                   # Interactive shell
php artisan migrate:rollback         # Rollback migrations

# Testing
php artisan test                     # Run tests
npm run dev                          # Development build
npm run build                        # Production build

# Maintenance
php artisan cache:clear              # Clear cache
php artisan config:cache             # Cache configuration
php artisan route:cache              # Cache routes
php artisan optimize                 # Optimize application
```

---

**Documentation Version:** 2.0  
**Last Updated:** May 24, 2026  
**Status:** Production Ready  
**Audience:** Developers, DevOps, Recruiters

---

*For questions or updates, please refer to the development team or project documentation.*
