# Taste of Ethiopia

A PHP and MySQL web project for exploring Ethiopian dishes. It includes dish listings, a cart interface, a blog, and a contact form.

## Repository layout

- [frontend](frontend) contains the public pages, starting at [frontend/index.php](frontend/index.php).
- [backend](backend) contains server-side handlers and database configuration.
- [includes](includes) and [asset](asset) contain shared templates and assets.
- [web-project.sql](web-project.sql) contains the database schema and seed statements.

## Run locally

1. Install PHP with MySQL support and start a local MySQL server.
2. Create a dedicated development database. Review the SQL file, then import the schema and any sample data you need locally.
3. Update [backend/config.php](backend/config.php) locally to point to your development database. Keep your database credentials out of version control.
4. From the repository root, run `php -S localhost:8000`, then open [http://localhost:8000/frontend/index.php](http://localhost:8000/frontend/index.php).

The project requires a PHP runtime and a MySQL database. GitHub Pages can serve static assets, but cannot execute the PHP application.
