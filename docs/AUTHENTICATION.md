# Authentication System Setup Guide

This guide explains how to set up and use the authentication system for the Patient Memo Application.

## Database Setup

### 1. Create Users Table

Run the SQL migration script to create the users table and default admin user:

```bash
# For PostgreSQL (as used in this project)
sudo -u postgres psql -d patient_memos < scripts/create_users_table.sql
```

Or manually execute the SQL:

```sql
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin user (password: admin123)
INSERT INTO users (username, email, password) 
VALUES ('admin', 'admin@example.com', '$2y$10$GiVRitNmPVBCRv.GtILbruSjwPFsSHNEvkSC0GwRiCU0889qDuGni')
ON CONFLICT (username) DO NOTHING;
```

### 2. Grant Database Permissions

```sql
-- Grant permissions on users table
GRANT ALL PRIVILEGES ON TABLE users TO matsuo;
GRANT USAGE, SELECT ON SEQUENCE users_id_seq TO matsuo;

-- Grant permissions on patient_memos table (if not already done)
GRANT ALL PRIVILEGES ON TABLE patient_memos TO matsuo;
GRANT USAGE, SELECT ON SEQUENCE patient_memos_id_seq TO matsuo;
```

## Default Credentials

After running the migration, you can log in with:
- **Username:** `admin`
- **Password:** `admin123`

**Important:** Change the admin password immediately after first login in production!

## Features

### Authentication
- User login with username/password
- Secure password storage using bcrypt (PASSWORD_DEFAULT)
- Session management with 30-minute timeout
- Logout functionality

### Route Protection
All memo-related routes are protected and require authentication:
- `/memos/list` - View all memos
- `/memos/create` - Create new memo
- `/memos/edit` - Edit existing memo
- `/memos/display` - Display memo details
- `/memos/get_ptid` - Filter by patient ID

### Public Routes
These routes are accessible without authentication:
- `/login` - Login page
- `/register` - Registration page (optional)

## Security Features

1. **Password Hashing:** All passwords are hashed using PHP's `password_hash()` with PASSWORD_DEFAULT (bcrypt)
2. **Session Security:** 
   - HttpOnly cookies (prevents XSS access to session cookie)
   - SameSite Strict (prevents CSRF attacks)
   - 30-minute session timeout
3. **Input Sanitization:** All user inputs are sanitized with `htmlspecialchars()`
4. **Prepared Statements:** All database queries use prepared statements (prevents SQL injection)
5. **Generic Error Messages:** Login/registration errors use generic messages to prevent user enumeration

## Configuration

### Session Timeout
Default session timeout is 30 minutes (1800 seconds). To change it, modify the middleware:

```php
// In src/Middleware/AuthMiddleware.php
AuthMiddleware::check(3600); // 60 minutes
```

### HTTPS in Production
For production environments using HTTPS, uncomment the cookie_secure setting in `public/index.php`:

```php
ini_set('session.cookie_secure', 1);
```

## User Management

### Creating New Users

Users can register through the `/register` page, or you can create them manually:

```php
use App\Models\User;

User::create('username', 'email@example.com', 'password');
```

### Changing Passwords

To change a password, generate a new hash:

```bash
php -r "echo password_hash('newpassword', PASSWORD_DEFAULT);"
```

Then update the database:

```sql
UPDATE users SET password = '<generated_hash>' WHERE username = 'admin';
```

## Testing

### Manual Testing Checklist

- [x] Login with correct credentials succeeds
- [x] Login with incorrect credentials fails with error message
- [x] Accessing memo pages without login redirects to login page
- [x] Accessing memo pages after login shows content
- [x] Logout functionality works correctly
- [x] Session persists across page refreshes
- [x] Password is stored as hash in database
- [x] Unauthenticated access is blocked

### Testing with cURL

```bash
# Test login
curl -c cookies.txt -L -X POST http://localhost:8080/login \
  -d "username=admin&password=admin123"

# Test accessing protected route
curl -b cookies.txt http://localhost:8080/memos/list

# Test logout
curl -b cookies.txt -L http://localhost:8080/logout
```

## Troubleshooting

### Database Connection Errors
Ensure PostgreSQL is running and credentials in `src/Database/Database.php` are correct.

### Permission Denied Errors
Make sure the database user has the correct permissions on all tables (see Database Setup section).

### Session Not Persisting
Check that session files are writable:
```bash
ls -la /var/lib/php/sessions/
```

### Login Redirects Immediately
This usually means the session is not being created. Check PHP error logs:
```bash
tail -f /var/log/php8.3-fpm.log
```

## Additional Security Recommendations

For production environments, consider implementing:

1. **Rate Limiting:** Limit failed login attempts to prevent brute-force attacks
2. **Password Strength Requirements:** Enforce minimum length and complexity
3. **Two-Factor Authentication (2FA):** Add an extra layer of security
4. **Account Lockout:** Temporarily lock accounts after multiple failed attempts
5. **Password Expiry:** Force password changes after a certain period
6. **Audit Logging:** Log all authentication events for security monitoring
7. **HTTPS Only:** Always use HTTPS in production environments
