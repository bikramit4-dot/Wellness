# 🌿 Chitrawan Nature Cure Hospital

A complete, database-driven wellness center website built with **vanilla PHP 8** (no framework, no Composer, no npm). It includes a full public website (home, about, treatments, tariff, gallery, blog, contact) and a password-protected **admin panel** for managing content, appointments, and admin users.

---

## ✨ Features

| Area | Details |
|---|---|
| **Public site** | Home, About, Treatments, Physiotherapy, Diet Therapy, Special Therapies, Tariff, Gallery, Blog, Contact pages |
| **Appointment booking** | Contact form saves bookings to the database (with a file-based fallback so bookings never fail) and emails the admin |
| **Admin panel** | `/admin` — manage content, page sections, therapies, posts, gallery, testimonials, team, tariff, offers, features, appointments, and admin users |
| **Page section editor** | Edit almost every heading, paragraph, image, button, and stat on every page from the admin panel (`Admin → Pages`) |
| **Image uploads** | Upload images/logo straight from the admin panel (saved to `public/uploads/`) |
| **Security** | CSRF protection, output escaping, HTML sanitization, bcrypt password hashing, admin idle-lock screen, login rate limiting, hardened headers (CSP, X-Frame-Options, nosniff) |
| **Email** | Transactional email via the **Resend** API (cURL, no packages) — free tier 3,000 emails/month |
| **SEO-friendly URLs** | Pretty URLs via `.htaccess` rewrite rules |

---

## 🧱 Tech Stack & Requirements

- **PHP 8.0+** (uses `str_starts_with`, typed properties, nullable types)
- **MySQL 5.7+ / MariaDB** (utf8mb4)
- **Apache** with **mod_rewrite** enabled
- PHP extensions: **PDO MySQL**, **cURL** (for emails), **mbstring** (recommended)

> No Composer, npm, or build step required — just PHP and MySQL.

---

## 📁 Project Structure

```
wellness/
├── index.php                 # Front controller (all requests route through here)
├── .htaccess                 # Pretty-URL rewrite rules
├── database.sql              # Database schema (creates DB + all tables)
├── README.md
├── app/
│   ├── config/config.php     # ⚙️ ALL configuration lives here (edit this!)
│   ├── core/                 # Router, Controller, Database (PDO), Security, Mailer
│   ├── controllers/          # PageController, AppointmentController, AdminController
│   ├── models/               # One model per content type (TherapyModel, PostModel, ...)
│   ├── data/                 # Default content in PHP files (used by the seed script)
│   ├── scripts/              # seed.php, admin-password.php, and helper scripts
│   └── views/                # page templates (pages/), admin templates (admin/), partials/
├── public/
│   ├── css/style.css         # All styling (no framework)
│   ├── js/main.js            # Front-end interactions
│   └── uploads/              # Uploaded images & media (admin panel writes here)
├── storage/
│   ├── appointments.txt      # File fallback for appointment bookings
│   └── login_attempts.json   # Brute-force protection counter (auto-created)
└── cj.txt                    # (dev scratch file — not used by the app)
```

---

## 🚀 Quick Start — Local Development (XAMPP)

### 1. Prerequisites
Install [XAMPP](https://www.apachefriends.org/) (Apache + PHP + MySQL). On macOS the PHP binary lives at:
```
/Applications/XAMPP/xamppfiles/bin/php
```
(On Windows it's `C:\xampp\php\php.exe`.)

### 2. Copy the project into htdocs
```
/Applications/XAMPP/xamppfiles/htdocs/wellness
```
Open XAMPP Control and **start Apache and MySQL**.

### 3. Create the database
Open **phpMyAdmin** (`http://localhost/phpmyadmin`) and import `database.sql` — this creates the `wellness` database and all tables.

Or from a terminal:
```bash
mysql -u root < database.sql
```

### 4. Seed the content
The app ships with default content in `app/data/*.php`. Load it into the database:

```bash
/Applications/XAMPP/xamppfiles/bin/php app/scripts/seed.php
```

### 5. Open the site
```
http://localhost/wellness
```

The base URL (`BASE_URL`) is **auto-detected**, so the app works whether it lives at `localhost/wellness`, `localhost`, or a renamed folder.

---

## ⚙️ Configuration (`app/config/config.php`)

Everything important is configured in **one file**: `app/config/config.php`.

| Setting | Default | Description |
|---|---|---|
| `BASE_URL` | auto-detected | Base path of the app (e.g. `/wellness`). Set the `BASE_URL` env var to override. |
| `SITE_NAME` | `Chitrawan Nature Cure Hospital` | Site/brand name used in emails and headers. |
| `SITE_EMAIL` | `nchchitwan@gmail.com` | Public contact email. |
| `DB_HOST` | `localhost` | MySQL host (`DB_HOST` env var overrides). |
| `DB_NAME` | `wellness` | Database name (`DB_NAME` env var overrides). |
| `DB_USER` | `root` | Database user (`DB_USER` env var overrides). |
| `DB_PASS` | *(empty)* | Database password (`DB_PASS` env var overrides). |
| `ADMIN_USERNAME` | `admin` | Admin login username. |
| `ADMIN_PASSWORD_HASH` | bcrypt hash | Admin password hash — see *Changing the admin password* below. |
| `RESEND_API_KEY` | *(empty)* | Resend API key for emails (`RESEND_API_KEY` env var overrides). |
| `MAILER_FROM` | `onboarding@resend.dev` | From-address for sent emails. |
| `MAILER_FROM_NAME` | `SITE_NAME` | Display name for sent emails. |
| `ADMIN_NOTIFY_EMAIL` | `nchchitwan@gmail.com` | Where new-appointment notifications are sent. |
| `ADMIN_IDLE_LOCK_SECONDS` | `0` | Seconds of inactivity before the admin lock screen appears (0 = disabled). |
| `ADMIN_SESSION_TIMEOUT` | `1800` | Hard server-side session expiry (30 min). |
| `ADMIN_LOCK_ON_ENTRY` | `false` | Show the lock screen on entry (false = disabled). |

> **Environment variables** (e.g. `DB_NAME`, `RESEND_API_KEY`) can override these values — handy on hosting where you can set env vars via cPanel.

---

## 🔐 Admin Panel

The admin panel is at **`{BASE_URL}/admin`**.

**Default credentials (local install):**
- Username: `admin`
- Password: `Admin@Chitrawan2026`

> ⚠️ **Change the default password immediately after deploying** — see below.

### Changing the admin password

**Option A — from the admin panel:** Log in → **Admin → Change Password**. (This rewrites `app/config/config.php`, so the file must be writable by the web server.)

**Option B — from the terminal** (recommended on shared hosting):

```bash
php app/scripts/admin-password.php "YourNewPassword"
```

The script prints a new `ADMIN_PASSWORD_HASH` line. Paste it into `app/config/config.php`, replacing the existing one. The old password stops working immediately.

> How login works: the admin panel first checks the `admin_users` **database table**, then falls back to `ADMIN_USERNAME`/`ADMIN_PASSWORD_HASH` from config. You can manage additional admin users at **Admin → Users**.

### Admin lock screen
The panel can lock itself after `ADMIN_IDLE_LOCK_SECONDS` of inactivity. Set to `0` to disable idle lock. Even if the lock is bypassed client-side, the server-side session expires after `ADMIN_SESSION_TIMEOUT` (30 min).

---

## 📧 Email Notifications (Resend)

The site sends a notification email whenever a new appointment is booked. It uses the **Resend API** (no SMTP config needed):

1. Create a free account at [resend.com](https://resend.com) (3,000 emails/month free).
2. Copy your **API key** and paste it into `app/config/config.php` as `RESEND_API_KEY` (or set the `RESEND_API_KEY` env var).
3. For **testing**, leave `MAILER_FROM` as `onboarding@resend.dev` — mail goes to your Resend account inbox.
4. For **production**, verify your own domain in Resend and set `MAILER_FROM` to an address on it (e.g. `info@yourdomain.com`).

If no API key is set, the app still works — emails are simply skipped (and logged to the PHP error log).

---

## 🗄️ Database & Content

- `database.sql` creates the database and all **12 tables** (appointments, therapies, posts, gallery, testimonials, team, tariff_plans, tariff_services, features, offers, admin_users, page_sections, ...).
- Default content lives in `app/data/*.php`. Re-apply it at any time:
  ```bash
  php app/scripts/seed.php
  ```
  Seeding **upserts** therapies and posts (safe to re-run) and **replaces** gallery/testimonials/offers/features/team/tariff/page_sections with the file defaults. It also migrates any old appointments from `storage/appointments.txt` into the database.

If you are upgrading an existing database to support rejected appointment requests, run this once after seeding:
```bash
php app/scripts/migration-add-appointment-rejected-status.php
```
- Appointment bookings go straight into the database, with `storage/appointments.txt` as a graceful fallback if MySQL is down.

---

## 🌐 Deploying to cPanel (Shared Hosting)

Follow these steps to put the site live on cPanel hosting.

### Step 1 — Create the MySQL database
1. Log into your **cPanel**.
2. Open **MySQL® Databases**.
3. Under *Create New Database*, enter a name (e.g. `wellness`) → **Create Database**. The final name will be prefixed, e.g. `cpaneluser_wellness`.
4. Under *MySQL Users*, create a user (e.g. `wellness_user`) with a **strong password** → **Create User**.
5. Under *Add User to Database*, select your user and database → **Add** → tick **ALL PRIVILEGES** → **Make Changes**.

> Keep the full names: **database** = `cpaneluser_wellness`, **user** = `cpaneluser_wellness_user`.

### Step 2 — Upload the files
You can upload to the domain root (`public_html/`) or a subfolder (`public_html/wellness/`) — the app auto-detects its base URL, so both work.

- **File Manager** (cPanel → File Manager → `public_html` → **Upload**): upload the whole project **including hidden files** (enable *Settings → Show Hidden Files (dotfiles)* so `.htaccess` is included).
- **FTP**: connect with your FTP credentials and drag the entire project folder into `public_html`.

### Step 3 — Edit the database settings
Edit `app/config/config.php` (via File Manager → right-click → **Edit**) and update the database constants with your cPanel values:

```php
define('DB_HOST', 'localhost');                       // usually stays localhost
define('DB_NAME', 'cpaneluser_wellness');
define('DB_USER', 'cpaneluser_wellness_user');
define('DB_PASS', 'YourStrongPassword');
```

Also update (optional but recommended):
```php
define('SITE_EMAIL', 'info@yourdomain.com');
define('ADMIN_NOTIFY_EMAIL', 'you@yourdomain.com');
define('RESEND_API_KEY', 're_YourResendApiKey');
```

> Tip: you can skip editing and instead set the `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` environment variables in cPanel, but editing the file is simpler.

### Step 4 — Import the database schema
1. Open **phpMyAdmin** in cPanel.
2. Select your new database (`cpaneluser_wellness`) from the left sidebar.
3. Go to **Import** → **Choose File** → select `database.sql` → **Go**.
4. All tables are created (no data yet — that comes next).

### Step 5 — Seed the default content
You need to run the seed script once so the site has content. Choose **one** of these methods:

**A) cPanel Terminal (easiest):** If your plan has **Terminal** (or SSH access):
```bash
cd ~/public_html
php app/scripts/seed.php
```

**B) Cron job:** cPanel → **Cron Jobs** → add a job that runs once (or run it and then delete the job):
```
* * * * * cd /home/YOURCPANELUSER/public_html && php app/scripts/seed.php >/dev/null 2>&1
```
Wait a minute for it to run, check the site has content, then **remove the cron job**.

**C) Seed locally, then export:** If you cannot run PHP via CLI at all, run the seed script on your local XAMPP machine against your local DB, export the `wellness` database via phpMyAdmin, then import that dump into cPanel. (Simpler: ask your host to enable the Terminal feature — most cPanel hosts offer it.)

### Step 6 — Fix folder permissions
The web server needs to **write** to a few folders:

| Path | Recommended permission |
|---|---|
| `storage/` | `755` (owner) — if uploads/bookings fail, try `775` or `777` |
| `public/uploads/` | `755` or `775` — needed for admin image uploads |
| `app/config/config.php` | `644` — writable only if you want to change the admin password from inside the panel |

In File Manager: right-click the folder → **Change Permissions**. On modern cPanel, PHP runs as your account user, so `755` with the correct owner is normally enough.

### Step 7 — Select the PHP version
cPanel → **MultiPHP Manager** → select **PHP 8.1 or newer** for your domain. (The app requires PHP 8.0+.)

### Step 8 — Go live & secure it
1. Visit `https://yourdomain.com` — the site should load with all content.
2. Log in at `https://yourdomain.com/admin` with the default credentials and **change the password immediately** (Admin → Change Password, or the `admin-password.php` script).
3. Optionally set `ADMIN_IDLE_LOCK_SECONDS` to something like `300` (5 minutes) in `.env` if you want idle lock for production.
4. If you use a subfolder like `https://yourdomain.com/wellness`, everything still works — `BASE_URL` is detected automatically.
5. If you plan to send real appointment emails, verify your domain in Resend and update `MAILER_FROM`.

### Step 9 — Verify
- ☑ Home page loads with images and content
- ☑ All sub-pages (treatments, tariff, gallery, blog, contact) load without 404s
- ☑ Booking an appointment from the contact page saves it in the admin panel
- ☑ Admin login works

---

## 🧰 Useful Scripts

| Script | What it does |
|---|---|
| `app/scripts/seed.php` | Creates tables and loads default content from `app/data/*.php` into the DB. **Re-run safely anytime.** |
| `app/scripts/admin-password.php` | Generates a new bcrypt `ADMIN_PASSWORD_HASH` for `config.php`. |
| `app/scripts/fix-data-files.php` | Repairs `app/data/*.php` files after the image-localization migration. |
| `app/scripts/localize-images.php` | One-off migration that rewrites remote Unsplash image URLs to local `public/uploads/photos/` files. |

---

## 🛠️ Troubleshooting

| Problem | Likely cause / fix |
|---|---|
| **Blank page or 500 error** | PHP < 8.0 — raise the PHP version (cPanel: MultiPHP Manager). Also check `storage/` write permissions. |
| **Home loads, but other pages 404** | `.htaccess` was not uploaded (hidden file) or mod_rewrite is off. Re-upload with *Show Hidden Files* enabled. |
| **`Database` errors on every page** | Wrong `DB_NAME`/`DB_USER`/`DB_PASS` in `config.php`, or the database wasn't created/imported. |
| **Pages load but show no content** | The seed script hasn't been run — run `php app/scripts/seed.php`. |
| **Images broken** | Uploads folder missing or not writable; or `BASE_URL` wrong (it should normally auto-detect). |
| **Appointment emails not arriving** | `RESEND_API_KEY` empty, or `MAILER_FROM` domain not verified in Resend. Check PHP error log. |
| **Admin image upload fails** | `public/uploads/` is not writable by the web server — change permissions. |
| **Admin lock screen appears too often** | Set `ADMIN_IDLE_LOCK_SECONDS=0` in `.env` to disable idle lock. |
| **Still stuck?** | Check the PHP error log (cPanel → Error Logs / `~/logs/`), and the app logs `[Database]`, `[Mailer]` messages there. |

---

## 🔒 Security Notes

- The admin area enforces CSRF tokens on all POST actions, output-escaping everywhere, and bcrypt password hashes.
- Login attempts are rate-limited via `storage/login_attempts.json`.
- Uploads are restricted by MIME type, capped at 5 MB, given random filenames, and the `public/uploads/` folder blocks PHP execution (`.htaccess`).
- HTTP security headers (CSP, nosniff, frame options, referrer policy) are sent on every request.
- **Always** change the default admin password and use strong database credentials in production.

---

## 📄 License

This project is provided as-is for the website owner. Third-party content (images, fonts) is referenced/used under its respective licenses.
