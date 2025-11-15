# Project Architecture & Database Diagram

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                      University Events System                   │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                    Web Browser (Client)                 │   │
│  └───────────────────────┬──────────────────────────────────┘   │
│                          │                                       │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │              Frontend Layer (HTML/CSS)                   │   │
│  │                                                          │   │
│  │  ┌────────────────────────────────────────────────┐    │   │
│  │  │    index.php          (Home Page)              │    │   │
│  │  └────────────────────────────────────────────────┘    │   │
│  │           │                            │               │   │
│  │           ├──────────┬──────────────────┤               │   │
│  │           ▼          ▼                  ▼               │   │
│  │  ┌──────────────┐  ┌──────────────────────────┐        │   │
│  │  │ post_event   │  │ upcoming_events.php      │        │   │
│  │  │ .php         │  │ (SELECT & Display)       │        │   │
│  │  │ (INSERT Form)│  │                          │        │   │
│  │  └──────────────┘  └──────────────────────────┘        │   │
│  │                                                          │   │
│  │  ┌────────────────────────────────────────────────┐    │   │
│  │  │        css/styles.css (Responsive Design)      │    │   │
│  │  └────────────────────────────────────────────────┘    │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │           Application Layer (PHP Logic)                  │   │
│  │                                                          │   │
│  │  ┌────────────────────────────────────────────────┐    │   │
│  │  │  config/db.php  (Database Connection)         │    │   │
│  │  └────────────────────────────────────────────────┘    │   │
│  │                      │                                  │   │
│  │  POST Events ────────┼────── SELECT Events             │   │
│  │  (INSERT)           │       (SELECT WHERE)             │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                    │                             │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │        Database Layer (MySQL)                            │  │
│  │                                                           │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │   university_events (Database)                  │   │  │
│  │  │                                                 │   │  │
│  │  │   ┌─────────────────────────────────────┐      │   │  │
│  │  │   │  events (Table)                     │      │   │  │
│  │  │   │  ├─ event_id (PK)                   │      │   │  │
│  │  │   │  ├─ event_title                     │      │   │  │
│  │  │   │  ├─ event_date                      │      │   │  │
│  │  │   │  ├─ location                        │      │   │  │
│  │  │   │  ├─ details                         │      │   │  │
│  │  │   │  └─ created_at                      │      │   │  │
│  │  │   └─────────────────────────────────────┘      │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Database Schema

```
┌────────────────────────────────────────────┐
│              events Table                  │
├────────────────────────────────────────────┤
│ Column        │ Type        │ Attributes   │
├───────────────┼─────────────┼──────────────┤
│ event_id      │ INT         │ PK, AUTO_INC │
│ event_title   │ VARCHAR(255)│ NOT NULL     │
│ event_date    │ DATETIME    │ NOT NULL     │
│ location      │ VARCHAR(255)│ NOT NULL     │
│ details       │ TEXT        │ NULL         │
│ created_at    │ TIMESTAMP   │ AUTO_SET     │
└────────────────────────────────────────────┘
```

## Data Flow Diagram

### INSERT Flow (Post New Event)

```
User Fills Form
      │
      ▼
┌──────────────────────┐
│ Form Validation      │
│ (client-side checks) │
└──────────────────────┘
      │
      ▼
┌──────────────────────┐
│ Form Submission      │
│ POST /post_event.php │
└──────────────────────┘
      │
      ▼
┌──────────────────────┐
│ PHP Validation       │
│ (server-side checks) │
└──────────────────────┘
      │
      ▼
┌──────────────────────┐
│ Prepared Statement   │
│ (SQL injection safe) │
└──────────────────────┘
      │
      ▼
┌──────────────────────┐
│ Execute Query        │
│ INSERT INTO events   │
└──────────────────────┘
      │
      ▼
┌──────────────────────┐
│ Database Insert      │
│ (Data added)         │
└──────────────────────┘
      │
      ▼
┌──────────────────────┐
│ Success Message      │
│ Display to User      │
└──────────────────────┘
```

### SELECT Flow (View Upcoming Events)

```
User Visits Page
      │
      ▼
┌──────────────────────────┐
│ Get Current Date/Time    │
│ (server's time)          │
└──────────────────────────┘
      │
      ▼
┌──────────────────────────┐
│ Prepared Statement       │
│ (SQL injection safe)     │
└──────────────────────────┘
      │
      ▼
┌──────────────────────────┐
│ Execute Query            │
│ SELECT * FROM events     │
│ WHERE event_date > NOW   │
│ ORDER BY event_date ASC  │
└──────────────────────────┘
      │
      ▼
┌──────────────────────────┐
│ Fetch Results            │
│ (all future events)      │
└──────────────────────────┘
      │
      ▼
┌──────────────────────────┐
│ Render HTML              │
│ (event cards grid)       │
└──────────────────────────┘
      │
      ▼
┌──────────────────────────┐
│ Apply CSS Styling        │
│ (responsive design)      │
└──────────────────────────┘
      │
      ▼
Display Events to User
```

## Page Flow Diagram

```
                    ┌─────────────────┐
                    │   index.php     │
                    │   (Home Page)   │
                    └────────┬────────┘
                             │
                ┌────────────┴────────────┐
                ▼                        ▼
        ┌──────────────────┐   ┌──────────────────────┐
        │ post_event.php   │   │ upcoming_events.php  │
        │                  │   │                      │
        │ Admin Form       │   │ Event Display        │
        │                  │   │                      │
        │ - Title          │   │ - Shows future only  │
        │ - Date/Time      │   │ - Sorted by date     │
        │ - Location       │   │ - Formatted display  │
        │ - Details        │   │ - Empty state msg    │
        │                  │   │                      │
        │ ─────INSERT────► │   │ ◄─────SELECT───────  │
        │                  │   │                      │
        └──────────────────┘   └──────────────────────┘
                │                        ▲
                │                        │
                └────────┬───────────────┘
                         │
                 ┌───────▼────────┐
                 │ universe_      │
                 │ events DB      │
                 │ events Table   │
                 └────────────────┘
```

## Security Flow

```
User Input
    │
    ▼
┌─────────────────────────────────┐
│ HTML Form Validation            │
│ (client-side - user experience) │
└─────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────┐
│ PHP Input Validation            │
│ (server-side - security)        │
│ - Check if empty                │
│ - Check data types              │
└─────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────┐
│ Input Sanitization              │
│ - trim() - remove whitespace    │
│ - htmlspecialchars() - XSS safe │
└─────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────┐
│ Prepared Statement              │
│ - Parameterized query           │
│ - SQL injection prevention      │
│ - bind_param() for type safety  │
└─────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────┐
│ Execute Query                   │
│ (data is now safe)              │
└─────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────┐
│ Output Encoding                 │
│ - htmlspecialchars() on display │
│ - Prevent XSS attacks          │
└─────────────────────────────────┘
    │
    ▼
Safe Display to User
```

## Request/Response Cycle

### INSERT Request:

```
CLIENT                          SERVER                          DATABASE
  │                              │                                 │
  ├──POST /post_event.php ──────►│                                 │
  │  (form data)                 │                                 │
  │                              ├─ Validate input                │
  │                              │                                 │
  │                              ├─ Prepare statement            │
  │                              │                                 │
  │                              ├──INSERT query ───────────────►│
  │                              │                                 │
  │                              │◄──Confirmation ───────────────┤
  │                              │                                 │
  │                              ├─ Generate response             │
  │                              │                                 │
  │◄─── HTML Response ───────────┤                                 │
  │  (success message)            │                                 │
  │                              │                                 │
```

### SELECT Request:

```
CLIENT                          SERVER                          DATABASE
  │                              │                                 │
  ├──GET /upcoming_events.php ──►│                                 │
  │                              │                                 │
  │                              ├─ Prepare statement            │
  │                              │                                 │
  │                              ├──SELECT query ────────────────►│
  │                              │                                 │
  │                              │◄──Result set ─────────────────┤
  │                              │  (future events)               │
  │                              │                                 │
  │                              ├─ Process results              │
  │                              ├─ Generate HTML               │
  │                              ├─ Apply CSS                   │
  │                              │                                 │
  │◄─── HTML Response ───────────┤                                 │
  │  (event cards)                │                                 │
  │                              │                                 │
```

## Error Handling Flow

```
Operation Attempted
        │
        ▼
   Does Error Occur?
        │
    ┌───┴───┐
    │ (YES) │
    ▼       │ (NO)
 Catch      │
 Error    ┌─┘
    │      ▼
    │   Display Data
    │   Normally
    │
    ▼
Check Error Type
    │
    ├─ Connection Error ──► Show "Connection failed" message
    │
    ├─ Query Error ───────► Show "Error: [details]" message
    │
    ├─ Validation Error ──► Show "Please fix: [fields]" message
    │
    └─ Other Error ───────► Show "An error occurred" message
```

This comprehensive architecture ensures:

- ✅ Secure database operations
- ✅ Clean separation of concerns
- ✅ Proper data validation
- ✅ User-friendly feedback
- ✅ Scalable structure for future enhancements
