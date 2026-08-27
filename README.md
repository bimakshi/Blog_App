# BlogNest – PHP Blog Application

## Overview

**BlogNest** is a web-based blogging platform built with **PHP and MySQL**.

Users can create and manage their own stories, explore stories published by other users, interact with posts, and organize stories using categories.

The application is designed as a simple, clean, and user-friendly blogging platform.

## Features

### User Authentication

* User registration
* User login and logout
* Session-based authentication
* Protected pages for logged-in users
* CSRF protection
* Flash messages for success and error notifications

### Blogging

* Create a story
* Edit your own stories
* Delete your own stories
* View individual stories
* View all stories in the Explore page
* View your own stories in My Blogs
* Add a cover image to stories
* Preview cover images before publishing
* Story categories
* Story creation and update timestamps

### Explore

* Browse published stories
* Search stories
* Filter stories by category
* View story details
* Display story cover images

### Social Features

* Like stories
* Add comments
* Like comments
* Reply to comments
* Delete your own comments

### Security

* PDO prepared statements
* CSRF token protection
* Output escaping/sanitization
* Login-required actions
* Users can manage only their own stories and comments
* Securely generated image filenames
* Image upload validation

## Technology Stack

### Frontend

* HTML5
* CSS3
* JavaScript

### Backend

* PHP 7.4+
* PDO

### Database

* MySQL / MariaDB

### Development Environment

* Apache
* XAMPP / WAMP / LAMP

## Project Structure

```text
Blog_App/
│
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
│
├── includes/
│   ├── db.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── uploads/
│   └── blogs/
│
├── sql/
│   └── blog_schema.sql
│
├── index.php
├── login.php
├── register.php
├── logout.php
├── explore.php
├── create.php
├── edit.php
├── delete.php
├── myblogs.php
├── blog.php
├── like.php
├── comment.php
├── comment_like.php
├── comment_delete.php
└── README.md
```

> The exact files may change as the project continues to be developed.

## Requirements

* PHP 7.4 or higher
* MySQL or MariaDB
* Apache server
* PDO MySQL extension
* XAMPP, WAMP, or another PHP development environment
* Modern web browser

## Local Setup – XAMPP

### 1. Clone or copy the project

Place the project inside the XAMPP `htdocs` directory:

```text
C:\xampp\htdocs\Blog_App
```

### 2. Start XAMPP

Open XAMPP Control Panel and start:

* Apache
* MySQL

### 3. Create the database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Create a database for BlogNest.

Then import:

```text
sql/blog_schema.sql
```

This will create the required database tables.

### 4. Configure the database

Update the database configuration in the project's database configuration file with your local MySQL credentials.

Typical XAMPP settings are:

```text
Host: localhost
Username: root
Password: 
Database: blog_app
```

Use the actual database name and credentials configured on your machine.

### 5. Run BlogNest

Open:

```text
http://localhost/Blog_App/
```

The BlogNest home page should load.

## Image Uploads

BlogNest allows users to upload cover images for their stories.

Supported formats:

* JPG
* JPEG
* PNG
* WebP

Maximum file size:

```text
5 MB
```

Uploaded images are stored in:

```text
uploads/blogs/
```

The application validates uploaded files before saving them and generates unique filenames to avoid filename conflicts.

Make sure the `uploads/blogs/` directory is writable by the web server.

## Deployment

BlogNest can be deployed to a PHP/MySQL hosting provider such as **InfinityFree** or another hosting service that supports:

* PHP
* MySQL/MariaDB
* Apache
* File uploads
* phpMyAdmin or another MySQL management tool

### General Deployment Steps

1. Create a hosting account.
2. Create a website/subdomain.
3. Create a MySQL database.
4. Note the database:

   * Host
   * Database name
   * Username
   * Password
5. Import the database schema from:

```text
sql/blog_schema.sql
```

6. Upload the project files to the hosting server.
7. Update the database configuration with the hosting database credentials.
8. Update the application's base URL if required.
9. Make sure the `uploads/blogs/` directory exists and is writable.
10. Enable HTTPS if available.
11. Test registration, login, story creation, image uploads, editing, deletion, comments, and likes.

## Important Deployment Notes

Do **not** upload sensitive local configuration or credentials to a public Git repository.

Before deployment, verify:

* Database credentials
* Base URL
* Upload directory permissions
* PHP version
* MySQL connection
* HTTPS
* File upload limits

## Security

BlogNest currently uses several security practices:

* PDO prepared statements
* CSRF protection
* Session-based authentication
* Output sanitization/escaping
* Login authorization checks
* Ownership checks for user content
* Server-side image validation
* Unique filenames for uploaded images

For a production deployment, additional security hardening may be required, including:

* Strong password policies
* Secure session cookie settings
* Rate limiting
* More extensive file upload validation
* Production error handling
* Security headers
* Regular dependency and server updates

## Database

The main database entities include functionality for:

* Users
* Blogs / Stories
* Categories
* Likes
* Comments
* Comment likes
* Comment replies

The database schema is located at:

```text
sql/blog_schema.sql
```

## License

MIT License

You are free to use, modify, and adapt this project.
