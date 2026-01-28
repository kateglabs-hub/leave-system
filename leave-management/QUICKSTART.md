# Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### 1. Database Setup (2 minutes)

```bash
# Create MySQL database
mysql -u root -p -e "CREATE DATABASE leave_management;"

# Import schema
mysql -u root -p leave_management < database/schema.sql
```

### 2. Configuration (1 minute)

Create `.env` file from example:
```bash
cp .env.example .env
```

Edit `.env` with your database credentials:
```
DB_HOST=localhost
DB_NAME=leave_management
DB_USER=root
DB_PASSWORD=your_password
```

### 3. Local Development (30 seconds)

```bash
# Start PHP server
cd public
php -S localhost:8000
```

### 4. Login (30 seconds)

Open browser: `http://localhost:8000`

**Default Login:**
- Email: `admin@company.com`
- Password: `admin123`

### 5. Initialize Leave Balance (1 minute)

For each new employee, initialize their leave balance:
- Login as HR/Admin
- The system will auto-initialize on first login

---

## 📦 Vercel Deployment (5 minutes)

### Quick Deploy

1. **Push to Git:**
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin YOUR_REPO_URL
git push -u origin main
```

2. **Deploy on Vercel:**
   - Go to https://vercel.com
   - Click "New Project"
   - Import your repository
   - Add environment variables (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD)
   - Click "Deploy"

3. **Done!** Your app is live at `your-project.vercel.app`

---

## 📋 Common Tasks

### Add New Employee

**Via API:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "employee_id": "EMP003",
    "email": "john@company.com",
    "password": "password123",
    "first_name": "John",
    "last_name": "Doe",
    "role": "employee",
    "department_id": 2,
    "employee_level": "staff",
    "hire_date": "2024-01-15"
  }'
```

**Via SQL:**
```sql
INSERT INTO users (employee_id, email, password, first_name, last_name, role, department_id, employee_level, hire_date)
VALUES ('EMP003', 'john@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', 'employee', 2, 'staff', '2024-01-15');
```

### Add New Leave Type

```sql
INSERT INTO leave_types (name, description, days_allowed_staff, days_allowed_management, requires_documentation, is_paid)
VALUES ('Remote Work', 'Work from home days', 20, 30, FALSE, TRUE);
```

### View Leave Statistics

**Via Browser:**
- Login as HR/Admin
- Go to Reports section
- View company-wide statistics

**Via API:**
```bash
curl http://localhost:8000/api/report-company
```

---

## 🔧 Troubleshooting

### Can't Login?
- Check database connection in `config/database.php`
- Verify user exists: `SELECT * FROM users WHERE email = 'admin@company.com';`
- Clear browser cache and cookies

### No Leave Balance?
- Initialize balance: `POST /api/initialize-balance` with user_id
- Or run: `SELECT * FROM leave_balances WHERE user_id = YOUR_USER_ID;`

### API Errors?
- Check PHP error logs
- Verify database credentials in `.env`
- Ensure all tables exist: `SHOW TABLES;`

---

## 📞 Quick Reference

### Default Credentials
- Admin: `admin@company.com` / `admin123`
- HR: `hr@company.com` / `admin123`

### Default Leave Allocations
- **Staff:** Annual (21), Sick (10), Compassionate (5)
- **Management:** Annual (25), Sick (15), Compassionate (7)

### API Base URL
- Local: `http://localhost:8000/api`
- Production: `https://your-app.vercel.app/api`

### Database Tables
- `users` - Employee information
- `departments` - Company departments
- `leave_types` - Types of leave
- `leave_balances` - Employee leave balances
- `leave_requests` - Leave request history

---

## 🎉 You're All Set!

Your leave management system is ready to use. For detailed documentation, see `README.md`.

**Next Steps:**
1. Change default passwords
2. Add your departments
3. Register employees
4. Start managing leaves!
