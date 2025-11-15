-- Create database
CREATE DATABASE IF NOT EXISTS university_events;
USE university_events;

-- Create events table
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_title VARCHAR(255) NOT NULL,
    event_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO events (event_title, event_date, location, details) VALUES
('Physics Seminar', '2025-12-01 14:00:00', 'Auditorium A', 'Renowned physicist discussing quantum mechanics'),
('Career Fair 2025', '2025-12-05 09:00:00', 'Student Center', 'Meet with top companies and explore job opportunities'),
('Winter Concert', '2025-12-10 18:00:00', 'Concert Hall', 'Annual winter concert by the university orchestra'),
('Science Expo', '2025-12-15 10:00:00', 'Science Building', 'Student projects and demonstrations'),
('Graduation Ceremony', '2025-12-20 10:00:00', 'Main Stadium', 'Winter semester graduation ceremony');
