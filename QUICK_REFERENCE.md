# Quick Reference Guide

## Getting Started (5 Minutes)

### 1. Import Database

```bash
# Windows Command Prompt
mysql -u root -p < database.sql

# Or use phpMyAdmin: SQL tab → paste database.sql contents → Go
```

### 2. Update Database Credentials (if needed)

Edit `config/db.php`:

```php
$db_host = 'localhost';      // Your host
$db_user = 'root';           // Your username
$db_password = '';           // Your password
$db_name = 'university_events';
```

### 3. Start Web Server

```bash
# Option A: XAMPP - Start Apache from Control Panel
# Option B: WAMP - Start from system tray
# Option C: Built-in PHP server
php -S localhost:8000
```

### 4. Access Application

```
Home:              http://localhost/php_project/index.php
Post Event:        http://localhost/php_project/pages/post_event.php
Upcoming Events:   http://localhost/php_project/pages/upcoming_events.php
```

---

## Project Files at a Glance

| File                        | Purpose         | Key Features                             |
| --------------------------- | --------------- | ---------------------------------------- |
| `index.php`                 | Home page       | Welcome, navigation links                |
| `pages/post_event.php`      | POST page       | Form, INSERT query, validation           |
| `pages/upcoming_events.php` | VIEW page       | SELECT query, WHERE clause, grid display |
| `config/db.php`             | Database config | Connection, credentials                  |
| `css/styles.css`            | Styling         | Responsive, modern design                |
| `database.sql`              | DB schema       | Table, sample data                       |

---

## Core SQL Operations

### INSERT (Post New Event)

```sql
INSERT INTO events (event_title, event_date, location, details)
VALUES ('Event Title', '2025-12-01 14:00:00', 'Location', 'Details');
```

### SELECT (View Upcoming Events)

```sql
SELECT event_id, event_title, event_date, location, details
FROM events
WHERE event_date > '2025-11-15 12:00:00'
ORDER BY event_date ASC;
```

### DELETE (Remove Event - for future use)

```sql
DELETE FROM events WHERE event_id = 1;
```

### UPDATE (Modify Event - for future use)

```sql
UPDATE events
SET event_title = 'New Title'
WHERE event_id = 1;
```

---

## Troubleshooting

### "Connection failed"

```
✓ MySQL running?
✓ Credentials correct in config/db.php?
✓ Database created?
```

### Events not showing

```
✓ Event dates in the future?
✓ Server time correct?
✓ Database populated?
```

### Form not submitting

```
✓ All fields filled?
✓ PHP errors in console?
✓ Database table exists?
```

### Styling not loading

```
✓ CSS file path correct?
✓ Browser cache cleared?
✓ File permissions correct?
```

---

## Key Code Snippets

### Connect to Database

```php
include '../config/db.php';  // Already connects
```

### Prepared Statement Insert

```php
$sql = "INSERT INTO events (event_title, event_date, location, details)
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $title, $date, $location, $details);
$stmt->execute();
```

### Prepared Statement Select

```php
$sql = "SELECT * FROM events WHERE event_date > ? ORDER BY event_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_date);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    // Process each row
}
```

### Validate Form Input

```php
$errors = [];
if (empty($_POST['event_title'])) {
    $errors[] = "Event title is required.";
}
if (!empty($errors)) {
    // Show errors
}
```

---

## Database Schema

```
TABLE: events
├─ event_id (INT, PK, AUTO_INCREMENT)
├─ event_title (VARCHAR 255, NOT NULL)
├─ event_date (DATETIME, NOT NULL)
├─ location (VARCHAR 255, NOT NULL)
├─ details (TEXT)
└─ created_at (TIMESTAMP, AUTO)
```

---

## Common Tasks

### Add Sample Event via SQL

```sql
INSERT INTO events (event_title, event_date, location, details)
VALUES
('Seminar', '2025-12-01 14:00:00', 'Auditorium', 'Technical seminar'),
('Workshop', '2025-12-10 10:00:00', 'Lab', 'Hands-on workshop');
```

### View All Events

```sql
SELECT * FROM events ORDER BY event_date ASC;
```

### View Future Events Only

```sql
SELECT * FROM events WHERE event_date > NOW() ORDER BY event_date ASC;
```

### Count Events by Month

```sql
SELECT MONTH(event_date) as month, COUNT(*) as count
FROM events
WHERE event_date > NOW()
GROUP BY MONTH(event_date);
```

### Delete Past Events

```sql
DELETE FROM events WHERE event_date < NOW();
```

### Update Event

```sql
UPDATE events
SET event_title = 'New Title',
    event_date = '2025-12-05 16:00:00'
WHERE event_id = 1;
```

---

## Form Fields

### Post Event Form

```
Event Title:      Text input (max 255 chars, required)
Event Date/Time:  Datetime input (required)
Location:         Text input (max 255 chars, required)
Details:          Textarea (required)
Submit Button:    POST to post_event.php
```

---

## Security Notes

| Threat        | Prevention             | File                                    |
| ------------- | ---------------------- | --------------------------------------- |
| SQL Injection | Prepared statements    | `post_event.php`, `upcoming_events.php` |
| XSS           | htmlspecialchars()     | `post_event.php`, `upcoming_events.php` |
| Invalid Input | Server-side validation | `post_event.php`                        |
| Missing Data  | Required field checks  | `post_event.php`                        |

---

## PHP Functions Used

| Function             | Purpose           | File                                    |
| -------------------- | ----------------- | --------------------------------------- |
| `mysqli_connect()`   | Connect to DB     | `db.php`                                |
| `$conn->prepare()`   | Prepare statement | `post_event.php`, `upcoming_events.php` |
| `bind_param()`       | Bind parameters   | `post_event.php`, `upcoming_events.php` |
| `execute()`          | Run query         | `post_event.php`, `upcoming_events.php` |
| `get_result()`       | Fetch results     | `upcoming_events.php`                   |
| `fetch_assoc()`      | Get row as array  | `upcoming_events.php`                   |
| `htmlspecialchars()` | Escape HTML       | `post_event.php`, `upcoming_events.php` |
| `trim()`             | Remove whitespace | `post_event.php`                        |
| `date()`             | Get current date  | `upcoming_events.php`                   |
| `DateTime` class     | Format date       | `upcoming_events.php`                   |

---

## Testing Checklist

✓ Form validates  
✓ Events insert  
✓ Events display  
✓ Future only shown  
✓ Dates format correctly  
✓ Navigation works  
✓ Responsive design  
✓ No errors

---

## Performance Tips

1. Add database indexes:

```sql
CREATE INDEX idx_event_date ON events(event_date);
```

2. Limit results in production:

```php
LIMIT 50  // Show 50 events per page
```

3. Use caching for static pages
4. Minify CSS/JS in production
5. Use CDN for assets if needed

---

## Useful Links

- [PHP MySQLi Documentation](https://www.php.net/manual/en/book.mysqli.php)
- [MySQL Date Functions](https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html)
- [HTML Form Elements](https://www.w3schools.com/html/html_forms.asp)
- [CSS Grid](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Grid_Layout)
- [OWASP Security](https://owasp.org/www-community/attacks/)

---

## Directory Structure Quick View

```
php_project/
├── config/db.php              ← Database connection
├── pages/
│   ├── post_event.php         ← Form to add events
│   └── upcoming_events.php    ← View events
├── css/styles.css             ← Styling
├── index.php                  ← Home page
├── database.sql               ← DB schema
├── README.md                  ← Documentation
├── SETUP_GUIDE.md            ← Setup instructions
├── TESTING_GUIDE.md          ← Testing guide
├── REQUIREMENTS.md           ← Requirements
├── ARCHITECTURE.md           ← Architecture docs
└── PROJECT_SUMMARY.md        ← This project summary
```

---

## Common MySQL Commands

```bash
# Connect to MySQL
mysql -u root -p

# List databases
SHOW DATABASES;

# Use database
USE university_events;

# Show tables
SHOW TABLES;

# Describe table structure
DESCRIBE events;

# Run SQL file
SOURCE database.sql;

# Exit MySQL
EXIT;
```

---

## Browser Developer Tools Tips

1. **Check Network Errors** (F12 → Network tab)
2. **View Console Errors** (F12 → Console tab)
3. **Test Responsive Design** (F12 → Toggle device toolbar)
4. **Check Styles** (F12 → Inspect element)
5. **Debug PHP** (Check server error logs)

---

## Production Deployment Checklist

- [ ] Change database password from default
- [ ] Move `config/db.php` outside web root
- [ ] Disable PHP error display
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Backup database
- [ ] Test all functionality
- [ ] Monitor error logs
- [ ] Set up monitoring/alerts
- [ ] Document deployment steps

---

## Support Resources

**If you encounter issues:**

1. Check `REQUIREMENTS.md` for system setup
2. Review `TESTING_GUIDE.md` for known issues
3. Check PHP error logs: `C:\xampp\php\logs\`
4. Check MySQL logs: `C:\xampp\mysql\data\`
5. Test database connection with phpMyAdmin

---

**Version**: 1.0  
**Last Updated**: November 15, 2025  
**Status**: ✅ Ready for Use
