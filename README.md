# Single-point-of-entry-authenticated-website-with-browsing-history-state-preservation

<img width="1280" height="677" alt="image" src="https://github.com/user-attachments/assets/06458854-64d8-47fa-a5d3-e318e0f47b03" />
<img width="1277" height="674" alt="image" src="https://github.com/user-attachments/assets/092aa667-0ab6-468f-9466-ba75283bd4e2" />
<img width="1280" height="673" alt="image" src="https://github.com/user-attachments/assets/1f9f5cda-e5ec-41cd-aa2f-9fa4f0583689" />
<img width="1277" height="675" alt="image" src="https://github.com/user-attachments/assets/7762b8ff-7a92-4244-8b46-0b1d503f5e88" />
<img width="1280" height="676" alt="image" src="https://github.com/user-attachments/assets/95703fd3-c648-40f9-a642-2eb563971e05" />


A PHP and MySQL web application built around a single-entry-point (MVC-style) controller pattern, with session-based login authentication and cookie-based restoration of a user's last viewed page and theme after logging back in.

Built as coursework for the "Web Programming Using PHP" module at Birkbeck, University of London.

## Overview

All requests are routed through a single controller (`index.php`), which decides what to render based on the logged-in state, the requested view, and data stored in the session and cookies. Rather than each page being its own PHP file with duplicated logic, one script handles login, logout, navigation, styling and page content, and includes the correct view template.

## Key Features

- **Session-based authentication** — users log in with a username and password, validated against a MySQL database using prepared statements (PDO) to prevent SQL injection
- **Single point of entry** — one controller script (`index.php`) handles routing, authentication state, and rendering for every page, based on a `view` URL parameter
- **Browsing history & preference restoration** — on logout, the user's last-viewed page and selected theme are saved in cookies (valid for a year) so that logging back in restores exactly where they left off
- **Dynamic theme switching** — users can switch between plain, light, and dark stylesheets, with the choice stored in the session and persisted across logins via cookies
- **Access control** — protected pages (`page1`, `page2`) redirect to the home view if accessed without an active session
- **Template-based rendering** — page structure, navigation and forms are built from HTML templates with placeholder tokens (e.g. `[+content+]`, `[+nav+]`) that are substituted server-side, keeping HTML separate from PHP logic
- **Input sanitisation** — user input is trimmed and passed through `htmlentities()` before use, and login errors (unknown user, incorrect password, missing database table) are handled and displayed cleanly
- **Custom 404 handling** — any unrecognised `view` parameter falls back to a 404 page

## Database

User accounts are stored in a MySQL `usersTable` (`username`, `password`), queried using PDO prepared statements rather than raw SQL, to protect against SQL injection. A helper function (`databaseExists`) also checks that the expected table exists before attempting a login query, and clear error messages are shown if it doesn't.

## Tech Stack

- **PHP** — server-side logic and routing
- **MySQL** (via PDO) — user data storage, accessed with prepared statements
- **HTML/CSS** — page templates and theme stylesheets (plain, light, dark)
- **PHP Sessions & Cookies** — authentication state and persisted user preferences

## Project Structure

```
├── index.php              # Single-entry-point controller: routing, auth, rendering
├── functions.php          # Core logic: validation, login/logout, nav building, styling
├── config.php             # Database connection (PDO)
├── home.php               # Home view
├── page1.php              # Protected view (login required)
├── page2.php              # Protected view (login required)
├── 404.php                # Fallback view for unknown routes
├── loginFormTemplate.html   # Login form template
├── logoutFormTemplate.html  # Logout / logged-in-user template
├── pageTemplate.html      # Overall page layout template
├── plain.css / light.css / dark.css   # Selectable themes
```

## How It Works

1. A request comes in to `index.php`, which checks the `view` parameter (or falls back to the session or the home view).
2. If a login form was submitted, credentials are validated against the database; on success, the session is populated and any saved cookie preferences (last page, theme) are restored.
3. If logout is submitted, the current page and theme are saved to cookies, and the session is destroyed.
4. Navigation links and page content are generated based on login state, and the whole page is assembled by substituting placeholders in the HTML templates.

## Setup & Deployment

This project was built to run on Birkbeck's shared PHP/MySQL web server rather than locally, since it depends on a university-hosted MySQL database (`mysqlsrv.dcs.bbk.ac.uk`).

1. Connect to Birkbeck's network using the FortiClient VPN (required for off-campus access to the server).
2. Upload the project files to your Birkbeck web space using an SFTP client such as FileZilla.
3. Create a `usersTable` in your Birkbeck MySQL database (see `database.sql` for the schema) using your university-provided database name and credentials.
4. Update `config.php` with your own database host, name, username and password (do not commit real credentials — use placeholders in any public copy of this file).
5. Visit the deployed URL on Birkbeck's server to use the site.

To adapt this project to run on a different PHP/MySQL host, update the connection details in `config.php` to point at that server instead.

## About

This project was built as coursework for the Web Programming Using PHP module of the BSc Computer Science (Part-Time) degree at Birkbeck, University of London.
