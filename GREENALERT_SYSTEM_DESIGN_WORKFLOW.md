# GreenAlert — System Design, Architecture & Workflow

This document contains the Entity Relationship Diagram, System Architecture Diagrams, and Workflow Diagrams for GreenAlert.

## 1. Entity Relationship Diagram

### 1.1 Database Schema Overview

```
┌─────────────────────────┐
│        USERS            │
├─────────────────────────┤
│ id (PK)                 │
│ name                    │
│ email (UNIQUE)          │
│ password                │
│ role                    │
│ email_verified_at       │
│ created_at              │
│ updated_at              │
│ deleted_at              │
└────────────┬────────────┘
             │
             │ (1 User - Many Incidents)
             │
             ▼
┌─────────────────────────┐
│      INCIDENTS          │
├─────────────────────────┤
│ id (PK)                 │
│ title                   │
│ description             │
│ severity                │
│ status                  │
│ reported_by (FK)        │◄──┐
│ incident_date           │   │
│ created_at              │   │
│ updated_at              │   │
│ deleted_at              │   │
└────────────┬────────────┘   │
             │                │
             │ (1 Incident -  │
             │  Many Audits)  │
             │                │
             ▼                │
┌─────────────────────────┐   │
│      AUDIT_LOGS         │   │
├─────────────────────────┤   │
│ id (PK)                 │   │
│ user_id (FK) ───────────┼───┘
│ incident_id (FK) ───────┼───┐
│ action                  │   │
│ old_values              │   │
│ new_values              │   │
│ ip_address              │   │
│ user_agent              │   │
│ created_at              │   │
└─────────────────────────┘   │
                              │
                    (1 User - Many Audits)
```

### 1.2 Table Definitions

#### USERS table

| Column | Type | Constraints | Description |
|---|---|---|---|
| id | BIGINT | PK, auto increment | Unique user identifier |
| name | VARCHAR(255) | NOT NULL | User full name |
| email | VARCHAR(255) | UNIQUE, NOT NULL | User email address |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| role | ENUM('admin','operator') | NOT NULL | Access level |
| email_verified_at | TIMESTAMP | NULLABLE | Email verification time |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Last update timestamp |
| deleted_at | TIMESTAMP | NULLABLE | Soft delete timestamp |

#### INCIDENTS table

| Column | Type | Constraints | Description |
|---|---|---|---|
| id | BIGINT | PK, auto increment | Unique incident identifier |
| title | VARCHAR(255) | NOT NULL | Incident title |
| description | LONGTEXT | NOT NULL | Detailed description |
| severity | ENUM('Low','Medium','High','Critical') | NOT NULL | Priority level |
| status | ENUM('Open','On Progress','Resolved') | NOT NULL | Current workflow state |
| reported_by | BIGINT | FK users.id | User who reported the incident |
| incident_date | DATE | NOT NULL | Date the incident happened |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Last update timestamp |
| deleted_at | TIMESTAMP | NULLABLE | Soft delete timestamp |

#### AUDIT_LOGS table

| Column | Type | Constraints | Description |
|---|---|---|---|
| id | BIGINT | PK, auto increment | Unique audit record identifier |
| user_id | BIGINT | FK users.id | User who performed the action |
| incident_id | BIGINT | FK incidents.id, NULLABLE | Related incident |
| action | VARCHAR(50) | NOT NULL | Action type: create, update, delete, view, restore |
| old_values | JSON | NULLABLE | Values before change |
| new_values | JSON | NULLABLE | Values after change |
| ip_address | VARCHAR(45) | NOT NULL | Client IP address |
| user_agent | TEXT | NOT NULL | Browser/client info |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Action timestamp |

### 1.3 Relationships

- User → Incidents: one-to-many, because one user can report many incidents.
- User → AuditLogs: one-to-many, because one user can generate many audit log entries.
- Incident → AuditLogs: one-to-many, because one incident can have many audit history records.

---

## 2. System Architecture Diagram

### 2.1 3-Tier Architecture

```
┌────────────────────────────────────────────────────────────┐
│          PRESENTATION LAYER (Browser/Client)              │
│                                                            │
│  ┌──────────────────────────────────────────────────────┐ │
│  │          AdminLTE Dashboard Interface                │ │
│  │  (HTML/CSS/JavaScript - Blade Templates)             │ │
│  │                                                      │ │
│  │  • Dashboard View                                   │ │
│  │  • Incident Management Forms                        │ │
│  │  • Audit Trail Display                              │ │
│  │  • User Profile & Settings                          │ │
│  └──────────────────────────────────────────────────────┘ │
└────────────────────┬─────────────────────────────────────┘
                     │ HTTPS/HTTP
                     ▼
┌────────────────────────────────────────────────────────────┐
│           APPLICATION LAYER (Server-Side)                 │
│                                                            │
│  ┌──────────────────────────────────────────────────────┐ │
│  │           Laravel Framework (PHP 8.2)               │ │
│  │                                                      │ │
│  │  ┌─────────────────────────────────────────────┐   │ │
│  │  │  Controllers                                 │   │ │
│  │  │  • DashboardController                       │   │ │
│  │  │  • IncidentController                        │   │
│  │  │  • UserController                            │   │
│  │  │  • AuthController                            │   │
│  │  └─────────────────────────────────────────────┘   │ │
│  │                                                      │ │
│  │  ┌─────────────────────────────────────────────┐   │ │
│  │  │  Services                                    │   │ │
│  │  │  • IncidentService                           │   │ │
│  │  │  • DashboardService                          │   │ │
│  │  │  • AuditService                              │   │ │
│  │  │  • AuthService                               │   │ │
│  │  └─────────────────────────────────────────────┘   │ │
│  │                                                      │ │
│  │  ┌─────────────────────────────────────────────┐   │ │
│  │  │  DTOs & Validation                           │   │ │
│  │  │  • IncidentDTO                               │   │ │
│  │  │  • FilterDTO                                 │   │ │
│  │  │  • Request Classes                           │   │ │
│  │  └─────────────────────────────────────────────┘   │ │
│  │                                                      │ │
│  │  ┌─────────────────────────────────────────────┐   │ │
│  │  │  Business Logic                              │   │ │
│  │  │  • Filtering & Search                        │   │ │
│  │  │  • Sorting & Pagination                      │   │ │
│  │  │  • Statistics Calculation                    │   │ │
│  │  └─────────────────────────────────────────────┘   │ │
│  │                                                      │ │
│  │  ┌─────────────────────────────────────────────┐   │ │
│  │  │  Traits & Middleware                         │   │ │
│  │  │  • HasAuditLog (auto-logging)                │   │ │
│  │  │  • Auth Middleware                           │   │ │
│  │  │  • CSRF Protection                           │   │ │
│  │  └─────────────────────────────────────────────┘   │ │
│  │                                                      │ │
│  └──────────────────────────────────────────────────────┘ │
└────────────────────┬─────────────────────────────────────┘
                     │ Eloquent ORM
                     ▼
┌────────────────────────────────────────────────────────────┐
│            DATA ACCESS LAYER (Repositories)               │
│                                                            │
│  ┌──────────────────────────────────────────────────────┐ │
│  │  Repositories                                         │ │
│  │  • IncidentRepository                                │ │
│  │  • UserRepository                                    │ │
│  │  • AuditLogRepository                                │ │
│  │                                                      │ │
│  │  Models (Eloquent ORM)                               │ │
│  │  • User Model                                        │ │
│  │  • Incident Model                                    │ │
│  │  • AuditLog Model                                    │ │
│  │                                                      │ │
│  │  Database Query Building                             │ │
│  │  • Scopes & Filters                                  │ │
│  │  • Eager Loading                                     │ │
│  │  • Join Operations                                   │ │
│  └──────────────────────────────────────────────────────┘ │
└────────────────────┬─────────────────────────────────────┘
                     │ SQL Queries
                     ▼
┌────────────────────────────────────────────────────────────┐
│               PERSISTENCE LAYER (Database)                │
│                                                            │
│  ┌──────────────────────────────────────────────────────┐ │
│  │           MySQL 8.0 Database                         │ │
│  │                                                      │ │
│  │  Tables:                                             │ │
│  │  • users                                             │ │
│  │  • incidents                                         │ │
│  │  • audit_logs                                        │ │
│  │  • migrations                                        │ │
│  │  • password_reset_tokens                             │ │
│  │  • sessions                                          │ │
│  │                                                      │ │
│  │  Indexes:                                            │ │
│  │  • Primary Keys                                      │ │
│  │  • Foreign Keys                                      │ │
│  │  • Search Indexes (severity, status, date)           │ │
│  │  • FULLTEXT Indexes (title, description)             │ │
│  └──────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────┘
```

### 2.2 Complete Stack Overview

```
┌─────────────────────────────────────────────────────────────┐
│                        END USER                             │
│                                                             │
│                   Web Browser (Client)                      │
└──────────────────────────┬──────────────────────────────────┘
                           │ HTTP/HTTPS
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    NGINX WEB SERVER                         │
│        (Load Balancing, SSL Termination, Caching)          │
└──────────────────────────┬──────────────────────────────────┘
                           │ FastCGI
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                      PHP-FPM                                │
│              (PHP Execution Environment)                    │
└──────────────────────────┬──────────────────────────────────┘
                           │ PHP Process
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                  LARAVEL APPLICATION                        │
│                                                             │
│  • Controllers                                              │
│  • Services                                                 │
│  • Models                                                   │
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

### 2.3 Component Interactions

USER REQUEST FLOW:

1. Browser sends HTTP request.
2. Nginx receives request on port 80 or 443.
3. Nginx routes the request to PHP-FPM.
4. Laravel router matches the route to a controller.
5. Controller performs auth checks and calls the service layer.
6. Service reads/writes through repositories and models.
7. Repository queries MySQL, optionally using Redis cache.
8. Controller returns a Blade view or JSON response.
9. Browser renders the response.

DATA CHANGE FLOW:

1. User submits a form to create or update an incident.
2. Request validation runs.
3. `HasAuditLog` captures old values.
4. Service saves changes to the database.
5. `HasAuditLog` records the audit entry.
6. Cache entries are invalidated.
7. Dashboard and list pages reflect the new data.

---

## 3. Workflow Diagrams

### 3.1 Incident Lifecycle Workflow

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

### 3.2 Status Change Workflow

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

### 3.3 Dashboard Update Flow

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
11. JavaScript loads charts
            ↓
12. Display to user
            ↓
13. User can interact with dashboard
```

### 3.4 Filtering & Search Workflow

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

## Notes

This file is extracted from `GREENALERT_PROFESSIONAL_DOCUMENTATION_v2.md` and contains the full System Design, Architecture diagrams and Workflow diagrams. For implementation details, refer back to the main documentation or the repository source code.
