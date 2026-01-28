# Leave Management System

A comprehensive, modern leave management system built with PHP and vanilla JavaScript, designed for deployment on Vercel with MySQL database support.

## 🚀 Quick Start

### Option 1: Docker (Recommended for Local Development)

The fastest way to get started locally:

```bash
# Clone the repository
git clone <repository-url>
cd leave-management

# Copy Docker environment file
cp .env.docker .env

# Start all services (Linux/Mac)
./docker-start.sh

# Or on Windows
docker-start.bat

# Or manually with Docker Compose
docker-compose up -d
```

**Then access:**
- **Application**: http://localhost:8000
- **PHPMyAdmin**: http://localhost:8080
- **Default Login**: admin@company.com / admin123

For detailed Docker documentation, see [DOCKER.md](./DOCKER.md)

### Option 2: Manual Local Setup

See [QUICKSTART.md](./QUICKSTART.md) for manual installation without Docker.

## 🌟 Features

### For All Employees
- **Personal Dashboard** - View leave balance, statistics, and request history
- **Leave Request Management** - Submit new leave requests with automatic working day calculation
- **Multiple Leave Types** - Annual leave, sick leave, compassionate leave, maternity, paternity, study leave, and unpaid leave
- **Real-time Balance Tracking** - See total days, used days, and remaining days for each leave type
- **Request Status Tracking** - Monitor pending, approved, and rejected requests

### For Managers
- **Department View** - View and manage leave requests for their department
- **Quick Approval/Rejection** - Review and process leave requests with reasons
- **Department Reports** - Access department-specific leave statistics

### For HR/Admin
- **Company-wide Dashboard** - Overview of all leave activities across the organization
- **Employee Management** - Add new employees with different roles and levels
- **Comprehensive Reports** - 
  - Individual employee reports
  - Department-level summaries
  - Company-wide analytics
- **Leave Type Breakdown** - See which types of leave are most utilized
- **Department Analysis** - Compare leave usage across departments
- **Advanced Approval System** - Manage all leave requests across the company

## 🎨 Design Features

- Modern, professional dark theme interface
- Responsive design for desktop, tablet, and mobile
- Smooth animations and transitions
- Gradient accents and visual hierarchy
- Clean typography with Outfit and Fraunces fonts
- Intuitive navigation with role-based menu items

## 📋 System Components

### Employee Levels
- **Staff** - Regular employees with standard leave allocations
- **Management** - Managers and senior staff with extended leave allocations

### User Roles
- **Employee** - Regular staff member
- **Manager** - Department manager with approval rights
- **HR** - HR officer with full access to reports and employee management
- **Admin** - System administrator with complete access

### Leave Types (Default Configuration)
- **Annual Leave** - Staff: 21 days, Management: 25 days
- **Sick Leave** - Staff: 10 days, Management: 15 days
- **Compassionate Leave** - Staff: 5 days, Management: 7 days
- **Maternity Leave** - 90 days (both levels)
- **Paternity Leave** - Staff: 10 days, Management: 14 days
- **Study Leave** - Staff: 5 days, Management: 10 days
- **Unpaid Leave** - Available as needed

## 🗂️ Project Structure

```
leave-management/
├── api/
│   └── index.php              # API router and endpoints
├── classes/
│   ├── Auth.php               # Authentication handling
│   ├── Leave.php              # Leave management logic
│   └── Reports.php            # Report generation
├── config/
│   └── database.php           # Database configuration
├── database/
│   └── schema.sql             # Database schema and seed data
├── public/
│   ├── index.html             # Main application interface
│   └── app.js                 # Frontend JavaScript logic
├── composer.json              # PHP dependencies
├── vercel.json                # Vercel deployment configuration
└── README.md                  # This file
```

## 🚀 Deployment on Vercel

### Prerequisites
1. Vercel account ([sign up here](https://vercel.com/signup))
2. MySQL database (you can use):
   - [PlanetScale](https://planetscale.com/) (recommended for Vercel)
   - [Railway](https://railway.app/)
   - [AWS RDS](https://aws.amazon.com/rds/)
   - [DigitalOcean Managed Database](https://www.digitalocean.com/products/managed-databases)

### Step-by-Step Deployment

#### 1. Setup Database

Create a MySQL database and run the schema:

```sql
-- Run the contents of database/schema.sql
-- This will create all tables and insert default data
```

#### 2. Configure Environment Variables

In your Vercel project settings, add these environment variables:

```
DB_HOST=your_database_host
DB_NAME=leave_management
DB_USER=your_database_user
DB_PASSWORD=your_database_password
```

#### 3. Deploy to Vercel

**Option A: Using Vercel CLI**

```bash
# Install Vercel CLI
npm i -g vercel

# Login to Vercel
vercel login

# Deploy
vercel
```

**Option B: Using Git Integration**

1. Push your code to GitHub/GitLab/Bitbucket
2. Go to [Vercel Dashboard](https://vercel.com/dashboard)
3. Click "Add New Project"
4. Import your repository
5. Vercel will auto-detect the configuration
6. Add environment variables in the project settings
7. Deploy!

#### 4. Initialize Leave Balances

After deployment, you need to initialize leave balances for existing users:

1. Login as HR/Admin
2. Use the system or run this SQL for each user:

```sql
-- Replace USER_ID with actual user IDs
CALL initialize_user_balance(USER_ID);
```

Or use the API endpoint (requires HR/Admin authentication):

```bash
POST /api/initialize-balance
{
  "user_id": 1,
  "year": 2024
}
```

## 🔐 Default Login Credentials

After running the schema, you can login with:

**Administrator**
- Email: `admin@company.com`
- Password: `admin123`

**HR Manager**
- Email: `hr@company.com`
- Password: `admin123`

**⚠️ IMPORTANT:** Change these passwords immediately after first login in production!

## 💻 Local Development

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Setup

1. **Clone and Setup Database**

```bash
# Create database
mysql -u root -p
CREATE DATABASE leave_management;
exit;

# Import schema
mysql -u root -p leave_management < database/schema.sql
```

2. **Configure Database Connection**

Edit `config/database.php` with your local credentials:

```php
$this->host = 'localhost';
$this->db_name = 'leave_management';
$this->username = 'root';
$this->password = 'your_password';
```

3. **Start Development Server**

```bash
# Using PHP built-in server
cd leave-management/public
php -S localhost:8000

# Or configure your Apache/Nginx virtual host
```

4. **Access the Application**

Open your browser and go to:
- `http://localhost:8000`

## 📊 API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/current-user` - Get current user info
- `POST /api/register` - Register new user (HR/Admin only)

### Leave Management
- `GET /api/leave-types` - Get all leave types
- `GET /api/leave-balance` - Get user leave balance
- `POST /api/initialize-balance` - Initialize leave balance
- `GET /api/leave-requests` - Get leave requests
- `POST /api/leave-request` - Create new leave request
- `PUT /api/leave-request-status` - Approve/reject request

### Statistics & Reports
- `GET /api/leave-statistics` - Get leave statistics
- `GET /api/leave-breakdown` - Get leave breakdown by type
- `GET /api/report-individual` - Individual employee report
- `GET /api/report-department` - Department report
- `GET /api/report-company` - Company-wide report
- `GET /api/departments` - Get all departments

## 🔧 Configuration

### Adding New Leave Types

```sql
INSERT INTO leave_types (name, description, days_allowed_staff, days_allowed_management, requires_documentation, is_paid)
VALUES ('Sabbatical', 'Extended leave for personal development', 30, 60, TRUE, FALSE);
```

### Adding New Departments

```sql
INSERT INTO departments (name, description, manager_id)
VALUES ('Engineering', 'Software Engineering Department', 2);
```

### Adding New Employees

Use the HR dashboard or API endpoint:

```bash
POST /api/register
{
  "employee_id": "EMP003",
  "email": "john.doe@company.com",
  "password": "secure_password",
  "first_name": "John",
  "last_name": "Doe",
  "role": "employee",
  "department_id": 2,
  "employee_level": "staff",
  "hire_date": "2024-01-15"
}
```

## 🔒 Security Features

- Password hashing with bcrypt
- Prepared statements to prevent SQL injection
- Role-based access control (RBAC)
- Session management
- Input validation and sanitization
- CORS headers configuration
- Environment variable for sensitive data

## 📱 Responsive Design

The system is fully responsive and works seamlessly on:
- Desktop computers (1920px+)
- Laptops (1366px - 1920px)
- Tablets (768px - 1366px)
- Mobile phones (320px - 768px)

## 🐛 Troubleshooting

### Database Connection Issues

If you get "Database connection failed":
1. Check your environment variables in Vercel
2. Ensure your database accepts connections from Vercel's IP addresses
3. Verify database credentials

### Sessions Not Working

If login doesn't persist:
1. Check PHP session configuration
2. Ensure cookies are enabled in browser
3. Verify session storage is writable

### Leave Balance Not Showing

If leave balance is empty:
1. Run the initialization for the user
2. Check that leave types exist in database
3. Verify user's employee_level is set correctly

## 🎯 Future Enhancements

Potential features to add:
- Email notifications for leave requests
- Calendar integration
- Public holidays management
- Leave carry-forward rules
- Approval workflows (multi-level approvals)
- Document upload for leave requests
- Export reports to PDF/Excel
- Mobile app
- Time-off in-lieu tracking
- Half-day leave support

## 📝 License

This project is open source and available under the MIT License.

## 🤝 Support

For issues or questions:
1. Check the troubleshooting section
2. Review API documentation
3. Contact system administrator

## 👥 Credits

Developed with modern web technologies:
- PHP 8.0+
- MySQL
- Vanilla JavaScript
- CSS3 with custom properties
- Google Fonts (Outfit, Fraunces)

---

**Note:** Remember to change default passwords and configure proper backup procedures for production use!
