# Quick Setup Guide

## Step 1: Create the Database

### Using phpMyAdmin:

1. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
2. Click on "SQL" tab
3. Copy and paste the entire contents of `database.sql`
4. Click "Go"

### Using MySQL Command Line:

```bash
mysql -u root -p < database.sql
```

## Step 2: Verify Database Configuration

Open `config/db.php` and ensure these settings match your setup:

```php
$db_host = 'localhost';         // Usually localhost
$db_user = 'root';              // Your MySQL username
$db_password = '';              // Your MySQL password
$db_name = 'university_events'; // Database name (from database.sql)
```

## Step 3: Copy Project to Web Root

- **XAMPP Users**: Copy `php_project` folder to `C:\xampp\htdocs\`
- **WAMP Users**: Copy `php_project` folder to `C:\wamp\www\`
- **LAMP/Linux Users**: Copy to your web root (usually `/var/www/html/`)

## Step 4: Start Your Web Server

- **XAMPP**: Start Apache from XAMPP Control Panel
- **WAMP**: Start WAMP from system tray
- **Command Line**: Run `php -S localhost:8000` from project directory

## Step 5: Access the Application

### Home Page:

```
http://localhost/php_project/index.php
```

### Post New Event:

```
http://localhost/php_project/pages/post_event.php
```

### View Upcoming Events:

```
http://localhost/php_project/pages/upcoming_events.php
```

## Testing the Application

1. Go to "Post New Event" page
2. Fill in the form with sample data:
   - **Title**: "Test Event"
   - **Date**: Select a future date
   - **Location**: "Test Location"
   - **Details**: "This is a test event"
3. Click "Post Event"
4. Go to "View Upcoming Events" to see the posted event

## Common Issues & Solutions

### Issue: "Connection failed: Connection refused"

**Solution**: Make sure MySQL is running and credentials in `config/db.php` are correct

### Issue: "Table 'university_events.events' doesn't exist"

**Solution**: Run the SQL script from `database.sql` to create the table

### Issue: Events not displaying

**Solution**: Make sure event dates are in the future (relative to server's current date/time)

### Issue: Form submits but no confirmation message

**Solution**: Check PHP error logs and ensure form fields are properly named

## Project Files Overview

| File                        | Purpose                            |
| --------------------------- | ---------------------------------- |
| `index.php`                 | Home page with project information |
| `config/db.php`             | Database connection configuration  |
| `pages/post_event.php`      | Form to insert new events          |
| `pages/upcoming_events.php` | Display upcoming events            |
| `css/styles.css`            | All styling for the application    |
| `database.sql`              | Database schema and sample data    |
| `README.md`                 | Complete documentation             |

## Key Features Implemented

✓ **INSERT Operations**: `post_event.php` - Posts new events to database
✓ **SELECT Operations**: `upcoming_events.php` - Retrieves future events
✓ **WHERE Clause**: Filters events by future date
✓ **Form Validation**: Ensures data integrity
✓ **Prepared Statements**: Prevents SQL injection
✓ **Responsive Design**: Works on all devices
✓ **Error Handling**: User-friendly error messages

## Database Query Examples

### Insert Event:

```php
INSERT INTO events (event_title, event_date, location, details)
VALUES ('Physics Seminar', '2025-12-01 14:00:00', 'Auditorium A', 'Details...')
```

### Select Future Events:

```php
SELECT event_id, event_title, event_date, location, details
FROM events
WHERE event_date > '2025-11-15 12:00:00'
ORDER BY event_date ASC
```

## Support

If you encounter any issues, check:

1. PHP error logs
2. MySQL error logs
3. `config/db.php` database credentials
4. That the database table exists
5. That the PHP `mysqli` extension is enabled

Enjoy using the University Events Management System!
