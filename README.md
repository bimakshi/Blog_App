# Simple Blog App (PHP + MySQL)

## Overview
A small blog application with user registration/login, and CRUD operations for the user's own blogs.

## Requirements
- PHP 7.4+ (PDO + MySQL)
- MySQL / MariaDB
- Apache (XAMPP, WAMP) or any LAMP stack

## Setup (Local, XAMPP example)
1. Copy project folder into `C:\xampp\htdocs\blog_app` (Windows) or `/opt/lampp/htdocs/blog_app` (Linux).
2. Create DB:
   - Use phpMyAdmin: import `sql/blog_schema.sql` or run the SQL in it.
3. Configure `.env` with DB credentials and `BASE_URL` (e.g. `http://localhost/blog_app`).
4. Visit `http://localhost/blog_app` in your browser.

## Deployment to free hosting
1. Create an account on 000WebHost or InfinityFree.
2. Create a new site / subdomain.
3. In hosting control panel create a MySQL database; note DB host, name, user, password.
4. Import `sql/blog_schema.sql` using phpMyAdmin on the host.
5. Upload files to the host folder:
   - 000WebHost: upload to `public_html/`
   - InfinityFree: upload to `htdocs/`
6. Update `.env` (DB credentials and `BASE_URL`).
7. Visit your site URL.

## Security notes
- Keep `.env` private.
- Use HTTPS in production.
- Consider escaping output (already using `sanitize()`).
- For production, consider more robust CSRF protection, input sanitization, and prepared statements (we already use PDO prepared statements).

## License
MIT (use & adapt freely)
