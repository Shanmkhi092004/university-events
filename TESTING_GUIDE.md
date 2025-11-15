# Testing Guide - University Events Management System

## Pre-Testing Checklist

- [ ] PHP 7.0+ installed
- [ ] MySQL 5.7+ installed and running
- [ ] Web server (Apache/Nginx) running or using built-in PHP server
- [ ] All project files copied to web root
- [ ] Database created from `database.sql`
- [ ] `config/db.php` credentials match your MySQL setup

## Test Case 1: Database Connection

### Objective

Verify that the application can connect to the database successfully.

### Steps

1. Navigate to `http://localhost/php_project/pages/post_event.php`
2. Check if the page loads without error

### Expected Result

- ✅ Page loads successfully
- ✅ No "Connection failed" error message
- ✅ Form is displayed
- ✅ Navigation menu is visible

### Troubleshooting

- If connection fails, check `config/db.php` credentials
- Ensure MySQL service is running
- Verify database name matches `config/db.php`

---

## Test Case 2: Form Validation - Empty Fields

### Objective

Test that the form validates empty fields correctly.

### Steps

1. Go to `post_event.php`
2. Click "Post Event" without filling any fields
3. Observe the validation messages

### Expected Result

- ✅ Form doesn't submit
- ✅ "Please fix the following errors:" message appears
- ✅ All 4 required fields are listed:
  - Event title is required
  - Event date is required
  - Location is required
  - Details are required

### Troubleshooting

- If validation doesn't show, check browser console for errors
- Verify PHP error_reporting is enabled

---

## Test Case 3: Form Validation - Partial Input

### Objective

Test that the form validates when only some fields are filled.

### Steps

1. Go to `post_event.php`
2. Fill only "Event Title" field
3. Leave other fields empty
4. Click "Post Event"

### Expected Result

- ✅ Form doesn't submit
- ✅ Error message shows missing fields:
  - Event date is required
  - Location is required
  - Details are required

---

## Test Case 4: Successful Event Insertion

### Objective

Test that events are correctly inserted into the database.

### Steps

1. Go to `post_event.php`
2. Fill in the form with valid data:
   - **Title**: "Test Event - [Today's Date]"
   - **Date**: Select a date 3 days in the future at 14:00
   - **Location**: "Test Auditorium"
   - **Details**: "This is a test event to verify the system works correctly."
3. Click "Post Event"

### Expected Result

- ✅ Form submits successfully
- ✅ Success message appears: "Event posted successfully! Your event has been added..."
- ✅ Form fields are cleared
- ✅ No error messages displayed

### Database Verification

```sql
-- Run this SQL to verify:
SELECT * FROM events WHERE event_title LIKE "Test Event%";
-- Should show your newly inserted event
```

---

## Test Case 5: Form Data Persistence on Validation Error

### Objective

Test that form data is preserved when validation fails.

### Steps

1. Go to `post_event.php`
2. Fill in ALL fields with valid data
3. Clear only the "Location" field
4. Click "Post Event"
5. Observe what remains in the form

### Expected Result

- ✅ Error message appears for Location field
- ✅ Event Title still shows the value you entered
- ✅ Event Date still shows the value you selected
- ✅ Details still shows the text you entered
- ✅ Only Location field is empty

---

## Test Case 6: Display Future Events Only

### Objective

Test that upcoming events page shows only future events.

### Steps

1. In your database, verify dates in the sample data:
   ```sql
   SELECT event_title, event_date FROM events ORDER BY event_date;
   ```
2. Note the current server date/time
3. Navigate to `http://localhost/php_project/pages/upcoming_events.php`
4. Observe which events are displayed

### Expected Result

- ✅ Only events with dates AFTER the current date/time are shown
- ✅ If sample data has dates in 2025 and current date is Nov 15, 2025, events should display
- ✅ Events are sorted chronologically (earliest first)
- ✅ No past events are visible

### Sample Test

Insert a past event and verify it doesn't appear:

```sql
INSERT INTO events (event_title, event_date, location, details)
VALUES ('Past Event', '2025-01-01 10:00:00', 'Past Location', 'This is a past event');
```

Then check that it doesn't appear on the upcoming events page.

---

## Test Case 7: Event Display Formatting

### Objective

Test that events are displayed with proper formatting.

### Steps

1. Go to `upcoming_events.php`
2. Examine the first event card

### Expected Result

- ✅ Event title is displayed as a heading (h2)
- ✅ Event date is formatted as: "Month Day, Year at Time AM/PM"
  - Example: "December 1, 2025 at 2:00 PM"
- ✅ Location shows with location icon (📍)
- ✅ Details text is fully visible
- ✅ All event cards have consistent styling

---

## Test Case 8: Multiple Events Display

### Objective

Test that multiple events display correctly in grid layout.

### Steps

1. Insert 5+ test events with different future dates:
   ```sql
   INSERT INTO events (event_title, event_date, location, details) VALUES
   ('Event 1', '2025-12-01 10:00:00', 'Location 1', 'Details 1'),
   ('Event 2', '2025-12-05 14:00:00', 'Location 2', 'Details 2'),
   ('Event 3', '2025-12-10 18:00:00', 'Location 3', 'Details 3');
   ```
2. Go to `upcoming_events.php`
3. Observe layout on desktop

### Expected Result

- ✅ Events display in a responsive grid (multiple columns on desktop)
- ✅ Each event has its own card
- ✅ Cards are evenly spaced
- ✅ Events are sorted by date (earliest first)
- ✅ All events are fully visible and readable

---

## Test Case 9: Empty Events List

### Objective

Test the behavior when no future events exist.

### Steps

1. Delete all events or insert only past events
2. Navigate to `upcoming_events.php`

### Expected Result

- ✅ Empty state message displays: "No upcoming events at the moment. Check back soon!"
- ✅ No error messages
- ✅ Page layout is still intact
- ✅ Navigation menu is visible

---

## Test Case 10: SQL Injection Prevention

### Objective

Test that SQL injection attempts are prevented.

### Steps

1. Go to `post_event.php`
2. In the Event Title field, enter: `' OR '1'='1`
3. Fill other fields normally
4. Submit the form

### Expected Result

- ✅ Form submits without error
- ✅ Event is inserted with the malicious text as literal data
- ✅ No database corruption occurs
- ✅ The event title displays as: `' OR '1'='1`

### Verification

```sql
SELECT * FROM events WHERE event_title LIKE "%OR%";
-- Should show your test event with the literal text, not as SQL code
```

---

## Test Case 11: XSS Prevention

### Objective

Test that cross-site scripting attempts are prevented.

### Steps

1. Go to `post_event.php`
2. In Event Title field, enter: `<script>alert('XSS')</script>`
3. Fill other fields normally
4. Submit the form
5. View the event on `upcoming_events.php`

### Expected Result

- ✅ Form submits successfully
- ✅ Event appears on upcoming events page
- ✅ No JavaScript alert appears
- ✅ The script tags are displayed as text (escaped)
- ✅ Browser displays: `<script>alert('XSS')</script>` as text, not as code

---

## Test Case 12: Responsive Design - Mobile

### Objective

Test that the application works on mobile devices.

### Steps

1. Open browser Developer Tools (F12)
2. Toggle Device Toolbar (mobile view)
3. Test different screen sizes:
   - iPhone SE (375px)
   - iPad (768px)
   - Desktop (1920px)
4. Navigate between pages

### Expected Result

- ✅ Layout adjusts properly for each screen size
- ✅ Navigation menu is accessible
- ✅ Forms are usable on mobile
- ✅ Event cards stack vertically on small screens
- ✅ Text is readable without zooming
- ✅ No horizontal scrolling needed

---

## Test Case 13: Date and Time Handling

### Objective

Test correct date/time filtering and display.

### Steps

1. Insert test events at specific times:
   ```sql
   INSERT INTO events (event_title, event_date, location, details) VALUES
   ('Event at 23:59', '2025-12-15 23:59:00', 'Location', 'Details'),
   ('Event at 00:00', '2025-12-16 00:00:00', 'Location', 'Details');
   ```
2. Set server time to 2025-12-15 23:58:59
3. Go to `upcoming_events.php`

### Expected Result

- ✅ Both events should display
- ✅ Event at 23:59 should appear before 00:00 event
- ✅ Dates are formatted correctly
- ✅ Time shows in 12-hour format with AM/PM

---

## Test Case 14: Navigation

### Objective

Test navigation between pages.

### Steps

1. Start at `index.php`
2. Click "Post New Event"
3. Verify you're on `post_event.php`
4. Click "View Upcoming Events"
5. Verify you're on `upcoming_events.php`
6. Click "Post New Event" from the upcoming events page
7. Verify navigation works both ways

### Expected Result

- ✅ All navigation links work correctly
- ✅ Active page is highlighted in navigation menu
- ✅ All pages maintain consistent header and navigation
- ✅ URLs are correct

---

## Test Case 15: Error Handling

### Objective

Test that errors are handled gracefully.

### Steps

1. Temporarily change database credentials in `config/db.php` to invalid values
2. Go to `post_event.php`
3. Observe error message
4. Restore correct credentials

### Expected Result

- ✅ Appropriate error message displays
- ✅ Page doesn't crash
- ✅ User gets helpful information
- ✅ No sensitive information (like database password) is revealed

---

## Performance Testing

### Test Case 16: Page Load Time

### Steps

1. Open browser Developer Tools (F12)
2. Go to Network tab
3. Navigate to `upcoming_events.php`
4. Note the load time

### Expected Result

- ✅ Page loads in under 1 second
- ✅ All assets (CSS, HTML) are loaded
- ✅ No 404 errors for assets

---

## Security Testing Summary

| Security Feature | Test Case        | Status |
| ---------------- | ---------------- | ------ |
| SQL Injection    | Test 10          | ✓      |
| XSS Prevention   | Test 11          | ✓      |
| Input Validation | Tests 2-3        | ✓      |
| CSRF Protection  | N/A (basic form) | -      |
| Authentication   | N/A (v1.0)       | -      |

---

## Automated Test Queries

### Run these SQL queries to verify database integrity:

```sql
-- Count total events
SELECT COUNT(*) as total_events FROM events;

-- Show all events sorted by date
SELECT event_id, event_title, event_date, location
FROM events
ORDER BY event_date ASC;

-- Show only future events
SELECT event_id, event_title, event_date, location
FROM events
WHERE event_date > NOW()
ORDER BY event_date ASC;

-- Show event with most recent date
SELECT event_title, event_date
FROM events
ORDER BY event_date DESC
LIMIT 1;

-- Count events by month
SELECT YEAR(event_date) as year, MONTH(event_date) as month, COUNT(*) as count
FROM events
WHERE event_date > NOW()
GROUP BY YEAR(event_date), MONTH(event_date)
ORDER BY event_date ASC;
```

---

## Test Results Log

Keep track of your test results:

| Test Case # | Description              | Date Tested | Result | Notes |
| ----------- | ------------------------ | ----------- | ------ | ----- |
| 1           | Database Connection      | -           | ✓/✗    |       |
| 2           | Empty Fields Validation  | -           | ✓/✗    |       |
| 3           | Partial Input Validation | -           | ✓/✗    |       |
| 4           | Successful Insertion     | -           | ✓/✗    |       |
| 5           | Data Persistence         | -           | ✓/✗    |       |
| 6           | Future Events Only       | -           | ✓/✗    |       |
| 7           | Display Formatting       | -           | ✓/✗    |       |
| 8           | Multiple Events          | -           | ✓/✗    |       |
| 9           | Empty List               | -           | ✓/✗    |       |
| 10          | SQL Injection            | -           | ✓/✗    |       |
| 11          | XSS Prevention           | -           | ✓/✗    |       |
| 12          | Responsive Design        | -           | ✓/✗    |       |
| 13          | Date/Time Handling       | -           | ✓/✗    |       |
| 14          | Navigation               | -           | ✓/✗    |       |
| 15          | Error Handling           | -           | ✓/✗    |       |
| 16          | Performance              | -           | ✓/✗    |       |

---

## Quick Validation Checklist

Before deployment, verify:

- [ ] All form validations work
- [ ] Events insert successfully
- [ ] Only future events display
- [ ] Events sort by date correctly
- [ ] No SQL errors appear
- [ ] Responsive design works
- [ ] Navigation works on all pages
- [ ] Styling loads correctly
- [ ] No console errors (F12)
- [ ] Mobile view works
- [ ] Empty state displays when no events
- [ ] Date format is consistent
- [ ] No sensitive data exposed
- [ ] Error messages are user-friendly
- [ ] All links are working

---

## Deployment Readiness Checklist

- [ ] All tests pass
- [ ] Database backed up
- [ ] Credentials secured (not in repository)
- [ ] Error logging configured
- [ ] Performance acceptable
- [ ] Security measures verified
- [ ] Documentation complete
- [ ] Backup/restore process tested

---

## Common Issues & Solutions

### Issue: Form doesn't validate

**Solution**: Clear browser cache, hard refresh (Ctrl+Shift+R), check PHP error logs

### Issue: Events not saving

**Solution**: Check database credentials, verify database exists, check table structure

### Issue: Events not displaying

**Solution**: Verify event dates are in the future, check server time, run SELECT query in MySQL

### Issue: Styling not loading

**Solution**: Check CSS file path, verify file exists, clear browser cache

### Issue: Navigation broken

**Solution**: Verify file paths are correct, check .htaccess for redirects

---

This comprehensive testing guide ensures your University Events Management System is fully functional and secure!
