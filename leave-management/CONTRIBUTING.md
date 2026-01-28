# Contributing to Leave Management System

Thank you for your interest in contributing to the Leave Management System! This document provides guidelines and instructions for contributing to this project.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Project Structure](#project-structure)
- [Coding Standards](#coding-standards)
- [Making Changes](#making-changes)
- [Testing](#testing)
- [Submitting Changes](#submitting-changes)
- [Reporting Bugs](#reporting-bugs)
- [Feature Requests](#feature-requests)

## Code of Conduct

### Our Pledge

We are committed to providing a welcoming and inclusive experience for everyone. We expect all contributors to:

- Use welcoming and inclusive language
- Be respectful of differing viewpoints
- Accept constructive criticism gracefully
- Focus on what is best for the community
- Show empathy towards other community members

## Getting Started

1. **Fork the repository**
2. **Clone your fork**
   ```bash
   git clone https://github.com/yourusername/leave-management-system.git
   cd leave-management-system
   ```
3. **Run the installation script**
   ```bash
   ./install.sh  # Linux/Mac
   install.bat   # Windows
   ```

## Development Setup

### Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Git
- Text editor or IDE (VS Code, PhpStorm, etc.)

### Local Environment

```bash
# Import database
mysql -u root -p leave_management < database/schema.sql

# Start development server
cd public
php -S localhost:8000
```

### Configuration

Copy `.env.example` to `.env` and configure:

```env
DB_HOST=localhost
DB_NAME=leave_management
DB_USER=root
DB_PASSWORD=your_password
```

## Project Structure

```
leave-management/
├── api/              # API endpoints and routing
├── classes/          # PHP classes (Auth, Leave, Reports)
├── config/           # Configuration files
├── database/         # Database schemas and migrations
├── public/           # Frontend files (HTML, CSS, JS)
└── docs/             # Documentation
```

### Key Files

- `api/index.php` - Main API router
- `classes/Auth.php` - Authentication logic
- `classes/Leave.php` - Leave management logic
- `classes/Reports.php` - Report generation
- `public/app.js` - Frontend application logic
- `public/index.html` - Main UI template

## Coding Standards

### PHP Code Style

Follow PSR-12 coding standards:

```php
<?php
// Class names in PascalCase
class LeaveManager {
    // Properties in camelCase
    private $leaveBalance;
    
    // Methods in camelCase
    public function calculateBalance($userId) {
        // Code here
    }
}
```

**Guidelines:**
- Use 4 spaces for indentation (no tabs)
- Opening braces on the same line for functions
- Always use strict type comparisons (===)
- Add PHPDoc comments for all classes and methods
- Keep functions focused and single-purpose

### JavaScript Code Style

```javascript
// Use camelCase for variables and functions
const leaveBalance = 0;

function calculateTotalDays(startDate, endDate) {
    // Code here
}

// Use const for constants, let for variables
const API_BASE = '/api';
let currentUser = null;
```

**Guidelines:**
- Use ES6+ features where appropriate
- Add JSDoc comments for complex functions
- Keep functions pure when possible
- Use async/await for asynchronous code

### SQL Style

```sql
-- Use UPPERCASE for keywords
SELECT u.id, u.first_name, u.last_name
FROM users u
JOIN departments d ON u.department_id = d.id
WHERE u.role = 'employee'
ORDER BY u.last_name;
```

### CSS Style

```css
/* Use kebab-case for class names */
.leave-card {
    /* Properties in alphabetical order */
    background: var(--bg-card);
    border-radius: 16px;
    padding: 1.5rem;
}

/* Use CSS custom properties for theming */
:root {
    --primary: #0F172A;
    --accent: #3B82F6;
}
```

## Making Changes

### Branching Strategy

- `main` - Production-ready code
- `develop` - Development branch
- `feature/feature-name` - New features
- `bugfix/bug-description` - Bug fixes
- `hotfix/critical-fix` - Critical production fixes

### Creating a Feature Branch

```bash
git checkout develop
git pull origin develop
git checkout -b feature/add-email-notifications
```

### Commit Messages

Use clear, descriptive commit messages:

```
feat: Add email notification system
fix: Resolve leave balance calculation error
docs: Update API documentation
style: Format code according to PSR-12
refactor: Simplify leave request validation
test: Add unit tests for Auth class
```

**Format:**
```
<type>: <subject>

<body>

<footer>
```

**Types:**
- `feat` - New feature
- `fix` - Bug fix
- `docs` - Documentation changes
- `style` - Code style changes (formatting)
- `refactor` - Code refactoring
- `test` - Adding tests
- `chore` - Maintenance tasks

### Code Review Checklist

Before submitting:
- [ ] Code follows project style guidelines
- [ ] Comments added for complex logic
- [ ] No console.log() or var_dump() left in code
- [ ] Database queries use prepared statements
- [ ] Input validation is present
- [ ] Error handling is implemented
- [ ] Code has been tested locally
- [ ] Documentation is updated
- [ ] No sensitive data in commits

## Testing

### Manual Testing

Test your changes thoroughly:

1. **Functional Testing**
   - Test the feature/fix works as expected
   - Test edge cases
   - Test error scenarios

2. **Cross-browser Testing**
   - Chrome
   - Firefox
   - Safari
   - Edge

3. **Responsive Testing**
   - Mobile (320px - 768px)
   - Tablet (768px - 1024px)
   - Desktop (1024px+)

### Testing Checklist

- [ ] Feature works as described
- [ ] No console errors
- [ ] No PHP errors or warnings
- [ ] Works on different browsers
- [ ] Works on different screen sizes
- [ ] Database operations complete successfully
- [ ] No security vulnerabilities introduced

## Submitting Changes

### Pull Request Process

1. **Update your branch**
   ```bash
   git checkout develop
   git pull origin develop
   git checkout feature/your-feature
   git merge develop
   ```

2. **Push your branch**
   ```bash
   git push origin feature/your-feature
   ```

3. **Create Pull Request**
   - Go to GitHub repository
   - Click "New Pull Request"
   - Select your branch
   - Fill in the PR template

### Pull Request Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
Describe how you tested these changes

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings
- [ ] Tests added/updated
- [ ] Works on all browsers

## Screenshots (if applicable)
Add screenshots here
```

## Reporting Bugs

### Before Submitting

1. Check existing issues
2. Try to reproduce on latest version
3. Gather relevant information

### Bug Report Template

```markdown
**Description**
Clear description of the bug

**Steps to Reproduce**
1. Go to '...'
2. Click on '...'
3. See error

**Expected Behavior**
What should happen

**Actual Behavior**
What actually happens

**Environment**
- OS: [e.g., Windows 10]
- Browser: [e.g., Chrome 120]
- PHP Version: [e.g., 8.1]
- MySQL Version: [e.g., 8.0]

**Screenshots**
If applicable

**Additional Context**
Any other relevant information
```

## Feature Requests

We welcome feature requests! Please provide:

1. **Use Case**: Why is this feature needed?
2. **Description**: What should the feature do?
3. **Alternatives**: What alternatives have you considered?
4. **Additional Context**: Mockups, examples, etc.

### Feature Request Template

```markdown
**Is your feature request related to a problem?**
A clear description of the problem

**Describe the solution you'd like**
What you want to happen

**Describe alternatives considered**
Other solutions you've considered

**Additional context**
Mockups, examples, etc.
```

## Development Guidelines

### Adding New Features

1. **Plan the feature**
   - Database changes needed?
   - API endpoints required?
   - UI components affected?

2. **Update database schema**
   - Add migration SQL if needed
   - Update schema.sql

3. **Implement backend**
   - Add/modify classes
   - Create/update API endpoints
   - Add validation

4. **Implement frontend**
   - Update UI components
   - Add JavaScript logic
   - Style with CSS

5. **Test thoroughly**
   - Manual testing
   - Edge cases
   - Error scenarios

6. **Update documentation**
   - README if needed
   - API documentation
   - Code comments

### Adding New API Endpoints

```php
// In api/index.php
if ($endpoint === 'new-feature' && $method === 'POST') {
    // Validate authentication
    if (!$auth->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit();
    }
    
    // Validate permissions
    if (!$auth->hasRole(['hr', 'admin'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    
    // Implement feature
    $result = $feature->doSomething($input);
    echo json_encode($result);
    exit();
}
```

### Database Changes

Always create migration scripts:

```sql
-- migrations/001_add_feature.sql
ALTER TABLE users ADD COLUMN new_field VARCHAR(255);

-- Add indexes if needed
CREATE INDEX idx_new_field ON users(new_field);
```

## Getting Help

- **Questions**: Open a GitHub Discussion
- **Bugs**: Create an Issue
- **Security**: Email security@yourcompany.com
- **General**: Check documentation first

## Recognition

Contributors will be recognized in:
- README.md Contributors section
- Release notes
- Project website (if applicable)

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to the Leave Management System! 🎉
