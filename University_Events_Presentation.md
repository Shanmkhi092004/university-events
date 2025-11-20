# University Events Management System

---

## 1. Project Name & Group Members

- **Project:** University Events Management System
- **Group Members:**
  - [Add Names Here]
  - [Add Roll Numbers Here]

---

## 2. Description

A PHP-based web application for university administrators to post, manage, and display upcoming events and announcements. Features include event creation, future event filtering, responsive UI, and secure database operations.

---

## 3. Requirements

- PHP 7.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx or PHP built-in)
- PHP extensions: mysqli, filter, date
- RAM: 512MB+, Disk: 100MB+

---

## 4. Tech Stack / Tools

- PHP (Backend)
- MySQL (Database)
- HTML5, CSS3, Tailwind CSS (Frontend)
- JavaScript (UI logic)
- Apache/Nginx (Web server)

---

## 5. Frontend (with Pic)

- Responsive design using Tailwind CSS
- Modern glassmorphism UI
- Event cards, admin forms, navigation
- ![Frontend Screenshot](../pages/upcoming_events.php)

---

## 6. Backend

- PHP scripts for form handling, validation, and database operations
- Secure session management for admin
- Prepared statements for SQL injection prevention

---

## 7. Database

- MySQL database: `university_events`
- Table: `events` (event_id, event_title, event_date, location, details, created_at)
- See `database.sql` for schema and sample data

---

## 8. Flow of Site

1. Admin logs in
2. Admin posts new event (post_event.php)
3. Events are stored in MySQL
4. Users view upcoming events (upcoming_events.php)
5. Only future events are shown, sorted by date

---

## 9. Challenges & Solutions

- **Form resubmission warning:** Solved with PRG pattern (redirect after POST)
- **SQL injection:** Prevented using prepared statements
- **Responsive UI:** Achieved with Tailwind CSS and custom CSS
- **Session security:** Used PHP sessions for admin authentication
- **User feedback:** Success/error messages with auto-hide

---

## 10. Thank You

Thank you for reviewing our University Events Management System!

---

_For more details, see the project documentation files._
