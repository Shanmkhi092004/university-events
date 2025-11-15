# System Requirements & Dependencies

## Server Requirements

### Minimum Requirements:

- **PHP**: 7.0 or higher (recommended: 7.4 or 8.0+)
- **MySQL**: 5.7 or higher (recommended: 8.0+)
- **Web Server**: Apache 2.4+ or Nginx 1.10+
- **RAM**: 512MB minimum
- **Disk Space**: 100MB minimum

### PHP Extensions Required:

- `mysqli` (MySQLi - MySQL Improved Extension)
- `filter` (for input filtering - usually enabled by default)
- `date` (for date/time functions - usually enabled by default)

## Verify Your Setup

### Check PHP Version:

```bash
php -v
```

### Check MySQL Version:

```bash
mysql --version
```

### Check if MySQLi is Enabled:

```bash
php -r "phpinfo();" | grep -i mysqli
```

Or create a test file with:

```php
<?php phpinfo(); ?>
```

And look for the "mysqli" section.

## Installation Methods

### 1. XAMPP (Recommended for Windows)

- Download from https://www.apachefriends.org/
- Includes PHP, MySQL, Apache
- Easiest setup process

### 2. WAMP (Windows)

- Download from https://www.wampserver.com/
- Similar to XAMPP
- Good for Windows development

### 3. LAMP (Linux)

- Linux, Apache, MySQL, PHP stack
- Install via package manager:

```bash
sudo apt-get install apache2 mysql-server php php-mysqli
```

### 4. Local PHP Server

- Built-in PHP development server (PHP 5.4+)
- Run from project directory:

```bash
php -S localhost:8000
```

- Access at http://localhost:8000

## Browser Compatibility

This project works with all modern browsers:

- ✓ Chrome (latest)
- ✓ Firefox (latest)
- ✓ Safari (latest)
- ✓ Edge (latest)
- ✓ Opera (latest)

## File Structure & Permissions

### Linux/Mac - Recommended Permissions:

```bash
chmod 755 /path/to/php_project
chmod 644 /path/to/php_project/*.php
chmod 644 /path/to/php_project/css/*.css
chmod 755 /path/to/php_project/config
chmod 644 /path/to/php_project/config/db.php
```

### Windows:

- Standard NTFS permissions are usually sufficient
- Ensure the web server user has read access to all files

## Database Setup

### MySQL User Setup (Recommended):

Create a dedicated user instead of using root:

```sql
CREATE USER 'events_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON university_events.* TO 'events_user'@'localhost';
FLUSH PRIVILEGES;
```

Then update `config/db.php`:

```php
$db_user = 'events_user';
$db_password = 'secure_password';
```

## Environment Variables (Optional)

For production, consider using environment variables:

Create `.env` file (not included in the project):

```
DB_HOST=localhost
DB_USER=events_user
DB_PASS=secure_password
DB_NAME=university_events
```

Then in `config/db.php`:

```php
$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
// etc...
```

## Performance Recommendations

### For Production:

1. Use a dedicated MySQL user (not root)
2. Enable MySQL query caching
3. Consider using persistent connections in `config/db.php`
4. Implement pagination for large event lists
5. Add database indexes on frequently queried columns

### MySQL Index Example:

```sql
CREATE INDEX idx_event_date ON events(event_date);
CREATE INDEX idx_event_title ON events(event_title);
```

## Security Checklist

- ✓ Store credentials in `config/db.php` (keep outside web root in production)
- ✓ Use prepared statements (already implemented)
- ✓ Validate all user input (already implemented)
- ✓ Sanitize output with `htmlspecialchars()` (already implemented)
- ✓ Use HTTPS in production
- ✓ Set appropriate file permissions
- ✓ Keep PHP and MySQL updated
- ✓ Disable PHP error display in production
- ✓ Use environment variables for sensitive data
- ✓ Implement user authentication (future enhancement)

## Troubleshooting Setup Issues

### PHP not found:

- Ensure PHP is installed and in your system PATH
- Restart your terminal after installing PHP

### MySQL connection fails:

- Verify MySQL service is running
- Check credentials in `config/db.php`
- Ensure database exists: `SHOW DATABASES;`

### MySQLi extension not loaded:

- Check PHP configuration file (php.ini)
- Uncomment line: `;extension=mysqli` → `extension=mysqli`
- Restart web server

### Permission denied errors:

- Ensure web server user has read/write access
- Check file permissions with `ls -la`
- Use `chmod` to fix permissions on Linux/Mac

### Port conflicts (if using built-in PHP server):

- If port 8000 is in use, try: `php -S localhost:8001`
- Or kill the process using the port

## Testing the Installation

1. Create a test file `test.php`:

```php
<?php
echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Test MySQLi
if (extension_loaded('mysqli')) {
    echo "MySQLi extension is loaded.<br>";
} else {
    echo "MySQLi extension is NOT loaded.<br>";
}

// Test date functions
echo "Server time: " . date('Y-m-d H:i:s') . "<br>";
?>
```

2. Access it via browser and verify all checks pass

## Upgrade Path

### If you need to upgrade:

1. Backup your database: `mysqldump -u root -p university_events > backup.sql`
2. Backup your project folder
3. Download and extract the new version
4. Update `config/db.php` if needed
5. Run any new migration scripts
6. Test thoroughly before going to production

## Version History

- **v1.0** (2025-11-15): Initial release
  - Post events functionality
  - View upcoming events
  - Responsive design
  - SQL injection prevention

## Support & Resources

- **PHP Documentation**: https://www.php.net/manual/
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **MySQLi Tutorial**: https://www.php.net/manual/en/book.mysqli.php
- **XAMPP**: https://www.apachefriends.org/
- **WAMP**: https://www.wampserver.com/

---

Last Updated: November 15, 2025
