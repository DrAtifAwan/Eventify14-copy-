CREATE DATABASE eventifydb;

USE eventif;y

CREATE TABLE organizers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    organizer_id INT,
    FOREIGN KEY (organizer_id) REFERENCES organizers(id)
);

CREATE TABLE rsvps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    attendee_id INT,
    status ENUM('attend', 'maybe', 'decline'),
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (attendee_id) REFERENCES attendees(id)
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    message VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id)
);


--_-_-_-_-_-_-
Database Tables
organizers:

id: INT, AUTO_INCREMENT, PRIMARY KEY

name: VARCHAR(255)

email: VARCHAR(255)

password: VARCHAR(255)

attendees:

id: INT, AUTO_INCREMENT, PRIMARY KEY

name: VARCHAR(255)

email: VARCHAR(255)

password: VARCHAR(255)

events:

id: INT, AUTO_INCREMENT, PRIMARY KEY

name: VARCHAR(255)

date: DATE

location: VARCHAR(255)

description: TEXT

organizer_id: INT, FOREIGN KEY (REFERENCES organizers(id))

rsvps:

id: INT, AUTO_INCREMENT, PRIMARY KEY

event_id: INT, FOREIGN KEY (REFERENCES events(id))

attendee_id: INT, FOREIGN KEY (REFERENCES attendees(id))

status: ENUM('attend', 'maybe', 'decline')