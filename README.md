# University Events Management System

A PHP-based event management system for university websites to post and display upcoming events and announcements.

## Project Structure

```
php_project/
├── config/
│   └── db.php              # Database connection configuration
├── pages/
│   ├── post_event.php      # Admin form to post new events
│   └── upcoming_events.php # Page to display upcoming events
├── css/
│   └── styles.css          # Styling for the application
├── database.sql            # SQL schema and sample data
└── README.md              # This file
```

## Features

- **Post New Event**: Admin form to insert new events into the database
- **View Upcoming Events**: Display all future events with detailed information
- **Future Events Filter**: Only shows events where the event_date is in the future
- **Input Validation**: Form validation to ensure data integrity
- **Responsive Design**: Mobile-friendly user interface
- **SQL Injection Prevention**: Uses prepared statements for secure database queries

## Database Schema

### Events Table

| Column      | Type               | Description                           |
| ----------- | ------------------ | ------------------------------------- |
| event_id    | INT AUTO_INCREMENT | Primary key                           |
| event_title | VARCHAR(255)       | Title of the event                    |
| event_date  | DATETIME           | Date and time of the event            |
| location    | VARCHAR(255)       | Location where the event will be held |
| details     | TEXT               | Detailed description of the event     |
| created_at  | TIMESTAMP          | Creation timestamp (auto-generated)   |

## Setup Instructions

### 1. Create the Database

1. Open your MySQL client (phpMyAdmin, MySQL Workbench, or command line)
2. Execute the SQL script in `database.sql` to create the database and table:
   ```sql
   -- Copy and paste the contents of database.sql
   ```

### 2. Configure Database Connection

Edit `config/db.php` and update the credentials if needed:

```php
$db_host = 'localhost';      // Your database host
$db_user = 'root';           // Your database username
$db_password = '';           // Your database password
$db_name = 'university_events'; // Database name
```

### 3. Place Files on Web Server

- Copy the entire `php_project` folder to your web server root directory (e.g., `htdocs` for XAMPP, `www` for WAMP)

### 4. Access the Application

Open your browser and navigate to:

- **Post Event**: `http://localhost/php_project/pages/post_event.php`
- **View Events**: `http://localhost/php_project/pages/upcoming_events.php`

## File Descriptions

### config/db.php

Contains the database connection configuration using MySQLi (procedural style). Establishes a connection to the MySQL database and sets the charset to UTF-8.

### pages/post_event.php

Admin form page to post new events:

- Form fields: Event Title, Event Date & Time, Location, Details
- Validates input before insertion
- Uses prepared statements to prevent SQL injection
- Displays success/error messages
- Preserves form data on validation errors

### pages/upcoming_events.php

Displays upcoming events:

- Queries events where `event_date > current_date`
- Displays events in a responsive grid layout
- Shows formatted date/time for each event
- Displays location and details
- Shows a message if no events are available

### css/styles.css

Professional styling with:

- Gradient header
- Responsive grid layout for event cards
- Hover effects and transitions
- Mobile-friendly design
- Alert styles for messages

## Usage Examples

### Adding a New Event

1. Go to the "Post New Event" page
2. Fill in the form:
   - Event Title: "Physics Seminar"
   - Event Date & Time: "2025-12-01 14:00"
   - Location: "Auditorium A"
   - Details: "Renowned physicist discussing quantum mechanics"
3. Click "Post Event"
4. The event will be added to the database and appear on the upcoming events page

### Viewing Events

1. Go to the "View Upcoming Events" page
2. All future events will be displayed in card format
3. Events are automatically sorted by date
4. Only events with dates in the future are shown

## Security Features

- **Prepared Statements**: Prevents SQL injection attacks
- **Input Validation**: Validates and sanitizes user input
- **HTML Escaping**: Uses `htmlspecialchars()` to prevent XSS attacks
- **Charset Setting**: Properly handles UTF-8 encoded data

## Requirements

- PHP 7.0 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)
- Basic knowledge of MySQL and PHP

## Troubleshooting

### Database Connection Error

- Ensure MySQL service is running
- Check credentials in `config/db.php`
- Verify the database `university_events` exists

### Events Not Showing

- Check that event dates are in the future (after current date/time)
- Verify the database contains events
- Check the MySQL time zone settings

### Form Submission Error

- Ensure all required fields are filled
- Check PHP error logs
- Verify file permissions

## Sample Events

The `database.sql` file includes 5 sample events:

1. Physics Seminar - December 1, 2025
2. Career Fair 2025 - December 5, 2025
3. Winter Concert - December 10, 2025
4. Science Expo - December 15, 2025
5. Graduation Ceremony - December 20, 2025

## Future Enhancements

- Event editing and deletion functionality
- User authentication for admin access
- Event categories/filtering
- Email notifications
- Event registration system
- Search functionality
- Event image uploads
- Calendar view

## License

This project is open source and available for educational purposes.
