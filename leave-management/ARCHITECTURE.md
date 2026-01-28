# Leave Management System - Architecture Overview

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     CLIENT LAYER (Browser)                   │
│  ┌─────────────────────────────────────────────────────┐   │
│  │         Modern Web Interface (HTML/CSS/JS)          │   │
│  │  • Responsive Design (Mobile, Tablet, Desktop)      │   │
│  │  • Role-based UI Components                         │   │
│  │  • Real-time Form Validation                        │   │
│  │  • Interactive Dashboards & Charts                  │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ HTTPS/REST API
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                  APPLICATION LAYER (PHP 8+)                  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              API Router (api/index.php)              │  │
│  │  • Request Routing & Method Handling                 │  │
│  │  • Authentication & Authorization                    │  │
│  │  • Session Management                                │  │
│  │  • Input Validation & Sanitization                   │  │
│  └──────────────────────────────────────────────────────┘  │
│                              │                               │
│  ┌──────────────┬────────────────────┬──────────────────┐  │
│  │              │                     │                  │  │
│  ▼              ▼                     ▼                  ▼  │
│ ┌────────┐  ┌────────┐          ┌────────┐      ┌─────────┐│
│ │ Auth   │  │ Leave  │          │Reports │      │Database ││
│ │ Class  │  │ Class  │          │ Class  │      │ Config  ││
│ │────────│  │────────│          │────────│      │─────────││
│ │Login   │  │Balance │          │Individual    │Connection││
│ │Logout  │  │Request │          │Department    │ Handler ││
│ │Register│  │Approve │          │ Company │    │ PDO     ││
│ │Session │  │Calculate          │Analytics│    │         ││
│ └────────┘  └────────┘          └────────┘      └─────────┘│
└─────────────────────────────────────────────────────────────┘
                              │
                              │ PDO/MySQL
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    DATABASE LAYER (MySQL)                    │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                  Core Tables                          │  │
│  │  • users           - Employee records & auth          │  │
│  │  • departments     - Organization structure           │  │
│  │  • leave_types     - Leave categories & policies      │  │
│  │  • leave_balances  - Employee leave allocations       │  │
│  │  • leave_requests  - Leave application history        │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  Relationships:                                              │
│  users ←→ departments (many-to-one)                         │
│  users ←→ leave_balances (one-to-many)                      │
│  users ←→ leave_requests (one-to-many)                      │
│  leave_types ←→ leave_balances (one-to-many)               │
│  leave_types ←→ leave_requests (one-to-many)               │
└─────────────────────────────────────────────────────────────┘
```

## Data Flow Diagrams

### 1. Leave Request Flow

```
Employee                    System                  Manager/HR
   │                          │                          │
   │──── Login ──────────────>│                          │
   │<─── Auth Token ──────────│                          │
   │                          │                          │
   │──── View Balance ───────>│                          │
   │<─── Balance Data ────────│                          │
   │                          │                          │
   │──── Submit Request ─────>│                          │
   │        (dates, type)     │                          │
   │                          │─── Validate ────>│       │
   │                          │<─── Check Balance│       │
   │                          │                          │
   │                          │─── Save Request ─>│      │
   │                          │                          │
   │<─── Confirmation ────────│                          │
   │                          │                          │
   │                          │──── Notification ───────>│
   │                          │                          │
   │                          │<──── Review Request ─────│
   │                          │                          │
   │                          │──── Update Status ──────>│
   │                          │      (Approve/Reject)    │
   │                          │                          │
   │                          │<──── Confirmation ───────│
   │<─── Status Update ───────│                          │
   │    (Email/Dashboard)     │                          │
```

### 2. Report Generation Flow

```
HR/Manager              System                   Database
   │                      │                          │
   │─── Request Report ──>│                          │
   │     (parameters)     │                          │
   │                      │─── Query Users ─────────>│
   │                      │<─── User Data ───────────│
   │                      │                          │
   │                      │─── Query Requests ──────>│
   │                      │<─── Request Data ────────│
   │                      │                          │
   │                      │─── Query Balances ──────>│
   │                      │<─── Balance Data ────────│
   │                      │                          │
   │                      │─── Aggregate ────>│      │
   │                      │─── Calculate ────>│      │
   │                      │─── Format ───────>│      │
   │                      │                          │
   │<─── Report Data ─────│                          │
   │   (JSON/formatted)   │                          │
```

## Component Details

### Frontend Components

1. **Authentication Module**
   - Login form with validation
   - Session management
   - Role-based UI rendering

2. **Dashboard Module**
   - Statistics cards
   - Leave balance display
   - Recent requests table
   - Quick actions

3. **Leave Management Module**
   - Leave request form
   - Calendar integration
   - Status tracking
   - History view

4. **Approval Module** (Manager/HR)
   - Pending requests queue
   - Request details view
   - Approve/reject actions
   - Filtering and sorting

5. **Reports Module** (HR/Admin)
   - Individual reports
   - Department analytics
   - Company-wide statistics
   - Data visualization

### Backend API Endpoints

```
Authentication
POST   /api/login              - User login
POST   /api/logout             - User logout
GET    /api/current-user       - Get session info
POST   /api/register           - Register new user (HR/Admin)

Leave Management
GET    /api/leave-types        - List all leave types
GET    /api/leave-balance      - Get user balance
POST   /api/initialize-balance - Setup new user balance
GET    /api/leave-requests     - Get requests (filtered)
POST   /api/leave-request      - Create new request
PUT    /api/leave-request-status - Approve/reject request

Analytics & Reports
GET    /api/leave-statistics   - Get statistics
GET    /api/leave-breakdown    - Leave type breakdown
GET    /api/report-individual  - Individual report
GET    /api/report-department  - Department report
GET    /api/report-company     - Company report
GET    /api/departments        - List departments
```

### Database Schema

```
users
├── id (PK)
├── employee_id (UNIQUE)
├── email (UNIQUE)
├── password (HASHED)
├── first_name
├── last_name
├── role (employee|manager|hr|admin)
├── department_id (FK → departments)
├── employee_level (staff|management)
├── hire_date
└── timestamps

departments
├── id (PK)
├── name
├── description
├── manager_id (FK → users)
└── created_at

leave_types
├── id (PK)
├── name
├── description
├── days_allowed_staff
├── days_allowed_management
├── requires_documentation
├── is_paid
├── is_active
└── created_at

leave_balances
├── id (PK)
├── user_id (FK → users)
├── leave_type_id (FK → leave_types)
├── year
├── total_days
├── used_days
├── remaining_days
├── carried_forward
└── timestamps

leave_requests
├── id (PK)
├── user_id (FK → users)
├── leave_type_id (FK → leave_types)
├── start_date
├── end_date
├── total_days
├── reason
├── status (pending|approved|rejected|cancelled)
├── approved_by (FK → users)
├── approved_at
├── rejection_reason
├── documentation_path
└── timestamps
```

## Security Architecture

### Authentication & Authorization

```
┌─────────────────────────────────────────┐
│         Security Layers                  │
├─────────────────────────────────────────┤
│ 1. HTTPS Transport (Vercel)             │
│    • SSL/TLS encryption                  │
│    • Secure headers                      │
├─────────────────────────────────────────┤
│ 2. Session Management                    │
│    • PHP sessions                        │
│    • Secure cookie flags                 │
│    • Session timeout                     │
├─────────────────────────────────────────┤
│ 3. Authentication                        │
│    • Bcrypt password hashing             │
│    • Email/password login                │
│    • Session validation                  │
├─────────────────────────────────────────┤
│ 4. Authorization (RBAC)                  │
│    • Role-based access control           │
│    • Employee: Own data only             │
│    • Manager: Department data            │
│    • HR/Admin: All data                  │
├─────────────────────────────────────────┤
│ 5. Data Protection                       │
│    • Prepared statements (SQL injection) │
│    • Input validation                    │
│    • Output sanitization                 │
│    • CORS configuration                  │
└─────────────────────────────────────────┘
```

## Deployment Architecture (Vercel)

```
┌────────────────────────────────────────────────────────┐
│                    Vercel Platform                      │
│  ┌──────────────────────────────────────────────────┐ │
│  │         Edge Network (Global CDN)                 │ │
│  │  • Static assets (HTML, CSS, JS)                  │ │
│  │  • Automatic HTTPS                                │ │
│  │  • DDoS protection                                │ │
│  └──────────────────────────────────────────────────┘ │
│                         │                               │
│  ┌──────────────────────────────────────────────────┐ │
│  │      Serverless Functions (PHP Runtime)          │ │
│  │  • Auto-scaling                                   │ │
│  │  • Regional execution                             │ │
│  │  • Environment variables                          │ │
│  └──────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────┘
                          │
                          │ MySQL Connection
                          ▼
┌────────────────────────────────────────────────────────┐
│              External MySQL Database                    │
│  • PlanetScale / Railway / AWS RDS                     │
│  • Connection pooling                                   │
│  • Automated backups                                    │
│  • SSL encryption                                       │
└────────────────────────────────────────────────────────┘
```

## Scalability Considerations

### Current Architecture
- **Single Database:** All data in one MySQL instance
- **Stateless API:** Each request independent
- **Session Storage:** PHP file-based sessions

### Scaling Path

1. **Phase 1: Current (0-100 users)**
   - Single database
   - Vercel serverless functions
   - File-based sessions

2. **Phase 2: Medium Scale (100-1000 users)**
   - Add Redis for session storage
   - Database connection pooling
   - CDN for static assets (included in Vercel)

3. **Phase 3: Large Scale (1000+ users)**
   - Database read replicas
   - Queue system for email notifications
   - Microservices architecture
   - Separate report generation service

## Performance Optimization

### Database
- Indexed columns: user_id, leave_type_id, status, dates
- Efficient queries with JOIN optimization
- Prepared statements for security and performance

### Frontend
- Minimal JavaScript dependencies
- CSS with hardware acceleration
- Lazy loading for reports
- Client-side caching

### API
- Response caching where appropriate
- Batch operations for bulk updates
- Optimized SQL queries
- Connection pooling

## Monitoring & Maintenance

### Key Metrics to Monitor
- API response times
- Database query performance
- Error rates
- User login success rate
- Leave request approval time
- System uptime

### Regular Maintenance Tasks
- **Daily:** Monitor error logs
- **Weekly:** Review pending requests
- **Monthly:** Generate reports, backup database
- **Quarterly:** Review and optimize queries
- **Annually:** Initialize new year balances, audit users

## Future Enhancements

### Short Term
- Email notifications
- PDF report generation
- Advanced search and filters
- Bulk operations

### Medium Term
- Calendar integration (Google, Outlook)
- Mobile app (React Native)
- Advanced analytics dashboard
- Multi-language support

### Long Term
- AI-powered leave predictions
- Public holiday auto-detection
- Workflow automation
- Integration with payroll systems
