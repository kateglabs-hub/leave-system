# Deployment Checklist for Vercel

## Pre-Deployment

- [ ] Database is set up and accessible
  - [ ] MySQL database created
  - [ ] Schema imported from `database/schema.sql`
  - [ ] Database accepts remote connections
  - [ ] SSL/TLS enabled (recommended)

- [ ] Environment variables prepared
  - [ ] DB_HOST (your database host)
  - [ ] DB_NAME (leave_management)
  - [ ] DB_USER (your database username)
  - [ ] DB_PASSWORD (your database password)

- [ ] Code is ready
  - [ ] All files committed to Git repository
  - [ ] `.env` not committed (in .gitignore)
  - [ ] Tested locally
  - [ ] Default passwords documented for change

## Deployment Steps

### Step 1: Setup Git Repository

```bash
# If not already initialized
git init
git add .
git commit -m "Initial commit: Leave Management System"

# Push to GitHub/GitLab/Bitbucket
git remote add origin YOUR_REPOSITORY_URL
git push -u origin main
```

### Step 2: Create Vercel Project

1. Go to https://vercel.com/dashboard
2. Click "Add New..." → "Project"
3. Import your Git repository
4. Configure project:
   - Framework Preset: Other
   - Root Directory: ./
   - Build Command: (leave empty)
   - Output Directory: public

### Step 3: Configure Environment Variables

In Vercel Project Settings → Environment Variables, add:

```
DB_HOST=your_database_host.com
DB_NAME=leave_management
DB_USER=your_db_username
DB_PASSWORD=your_secure_password
```

**Important:** Make sure to add these for all environments:
- [ ] Production
- [ ] Preview (optional)
- [ ] Development (optional)

### Step 4: Deploy

1. Click "Deploy" button
2. Wait for build to complete (usually 1-2 minutes)
3. Vercel will provide a URL: `https://your-project.vercel.app`

### Step 5: Post-Deployment Testing

- [ ] Can access the application URL
- [ ] Login page loads correctly
- [ ] Can login with default credentials
- [ ] Dashboard displays without errors
- [ ] Can view leave balance
- [ ] Can create leave request
- [ ] HR features work (if applicable)

## Post-Deployment Configuration

### 1. Change Default Passwords

```sql
-- Update admin password
UPDATE users 
SET password = '$2y$10$YOUR_NEW_HASHED_PASSWORD' 
WHERE email = 'admin@company.com';

-- Update HR password
UPDATE users 
SET password = '$2y$10$YOUR_NEW_HASHED_PASSWORD' 
WHERE email = 'hr@company.com';
```

Or login and use password change feature (to be implemented).

### 2. Add Company Departments

```sql
-- Add your actual departments
INSERT INTO departments (name, description) VALUES
('Human Resources', 'HR and People Operations'),
('Engineering', 'Software Development'),
('Sales', 'Sales and Business Development'),
('Marketing', 'Marketing and Communications');
```

### 3. Register Employees

Use the HR dashboard to add employees or via API:

```bash
curl -X POST https://your-project.vercel.app/api/register \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_SESSION_TOKEN" \
  -d '{
    "employee_id": "EMP001",
    "email": "employee@company.com",
    "password": "temp_password",
    "first_name": "Jane",
    "last_name": "Smith",
    "role": "employee",
    "department_id": 1,
    "employee_level": "staff",
    "hire_date": "2024-01-01"
  }'
```

### 4. Initialize Leave Balances

For each new employee, initialize their leave balance:

```bash
curl -X POST https://your-project.vercel.app/api/initialize-balance \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "year": 2024
  }'
```

## Recommended Database Providers for Vercel

### PlanetScale (Recommended)
- **Pros:** Serverless, auto-scaling, free tier available
- **Setup:** https://planetscale.com/
- **Connection:** Uses connection strings directly

### Railway
- **Pros:** Simple setup, good free tier
- **Setup:** https://railway.app/
- **Connection:** Standard MySQL connection

### AWS RDS
- **Pros:** Enterprise-grade, highly available
- **Setup:** https://aws.amazon.com/rds/
- **Note:** Requires VPC configuration

### DigitalOcean
- **Pros:** Managed databases, good pricing
- **Setup:** https://www.digitalocean.com/products/managed-databases
- **Connection:** Standard MySQL connection

## Database Configuration Tips

### For PlanetScale

```env
DB_HOST=aws.connect.psdb.cloud
DB_NAME=your-database-name
DB_USER=your-username
DB_PASSWORD=your-password
```

Add to your PHP connection string:
```php
"mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4;sslmode=require"
```

### For Railway

```env
DB_HOST=containers-us-west-xxx.railway.app
DB_NAME=railway
DB_USER=root
DB_PASSWORD=your-password
```

Standard connection works fine.

## Monitoring & Maintenance

### Set Up Monitoring

- [ ] Enable Vercel Analytics
- [ ] Set up error tracking (Sentry, Rollbar)
- [ ] Configure uptime monitoring
- [ ] Set up database backups

### Regular Maintenance

- [ ] Weekly: Review leave requests
- [ ] Monthly: Generate reports
- [ ] Quarterly: Archive old data
- [ ] Annually: 
  - Initialize new year leave balances
  - Review and update leave policies
  - Audit user accounts

## Troubleshooting Deployment Issues

### Issue: "Database connection failed"

**Solutions:**
1. Verify environment variables in Vercel
2. Check database accepts connections from `0.0.0.0/0` or Vercel IPs
3. Test connection from command line:
   ```bash
   mysql -h DB_HOST -u DB_USER -pDB_PASSWORD DB_NAME
   ```

### Issue: "Session not persisting"

**Solutions:**
1. Vercel functions are stateless - sessions might not work across requests
2. Consider using JWT tokens for authentication
3. Or use Redis/external session storage

### Issue: "API endpoints returning 404"

**Solutions:**
1. Check `vercel.json` routes configuration
2. Verify API files are in correct location
3. Check deployment logs in Vercel dashboard

### Issue: "CORS errors"

**Solutions:**
1. Update CORS headers in `api/index.php`
2. Add domain to allowed origins
3. Check browser console for specific CORS issues

## Security Best Practices

### Before Going Live

- [ ] Change all default passwords
- [ ] Use strong, unique passwords
- [ ] Enable HTTPS (automatic on Vercel)
- [ ] Set up proper CORS policies
- [ ] Review and limit API access
- [ ] Enable rate limiting (if needed)
- [ ] Set up database backups
- [ ] Document security procedures
- [ ] Train users on security best practices

### Database Security

```sql
-- Create a dedicated database user with limited permissions
CREATE USER 'leave_app'@'%' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE ON leave_management.* TO 'leave_app'@'%';
GRANT DELETE ON leave_management.leave_requests TO 'leave_app'@'%';
FLUSH PRIVILEGES;
```

## Rollback Plan

If something goes wrong:

1. **Revert Deployment**
   - Go to Vercel Dashboard → Deployments
   - Click on previous working deployment
   - Click "Promote to Production"

2. **Database Rollback**
   ```sql
   -- Restore from backup
   mysql -u root -p leave_management < backup_YYYYMMDD.sql
   ```

3. **Check Logs**
   - Vercel Dashboard → Logs
   - Check for errors and warnings

## Support Resources

- **Vercel Documentation:** https://vercel.com/docs
- **PHP on Vercel:** https://vercel.com/docs/runtimes#official-runtimes/php
- **MySQL Documentation:** https://dev.mysql.com/doc/

## Completion Checklist

- [ ] Application deployed successfully
- [ ] Database connected and working
- [ ] Can login with credentials
- [ ] All features tested
- [ ] Default passwords changed
- [ ] Company data added
- [ ] Employees registered
- [ ] Leave balances initialized
- [ ] Documentation updated
- [ ] Team trained on system
- [ ] Monitoring set up
- [ ] Backup procedures in place

---

**Congratulations!** Your Leave Management System is now live! 🎉

For ongoing support and updates, refer to the main README.md file.
