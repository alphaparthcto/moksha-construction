# Moksha Construction CMS — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Build a simple admin panel where Parth & Hari can add, edit, hide, and publish construction projects with image galleries — no coding knowledge needed.

**Architecture:** PHP 8.4 + MySQL on InMotion VPS. Session-based auth. Admin panel at `/admin/`. Public projects page reads from MySQL. Image uploads auto-resize + convert to WebP. Maintenance mode toggle in admin dashboard.

**Tech Stack:** PHP 8.4, MySQL 8, vanilla PHP (no framework), bcrypt auth, GD library for image processing, Alpine.js for admin interactivity.

---

### Task 1: Create MySQL Database + User via cPanel API

**Files:**
- Create: `includes/db.php`
- Create: `database/schema.sql`
- Modify: `includes/config.php` (add DB constants)

**Step 1: Create database and user via cPanel API**

```bash
# Create database: parths5_moksha
curl -sk -b /tmp/cpanel_cookies "${BASE}/execute/Mysql/create_database?name=parths5_moksha"

# Create user: parths5_moksha
curl -sk -b /tmp/cpanel_cookies "${BASE}/execute/Mysql/create_user" \
  --data-urlencode "name=parths5_moksha" \
  --data-urlencode "password=GENERATED_PASSWORD"

# Grant all privileges
curl -sk -b /tmp/cpanel_cookies "${BASE}/execute/Mysql/set_privileges_on_database" \
  --data-urlencode "user=parths5_moksha" \
  --data-urlencode "database=parths5_moksha" \
  --data-urlencode "privileges=ALL PRIVILEGES"
```

**Step 2: Write schema.sql**

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'editor') DEFAULT 'editor',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  type ENUM('residential', 'commercial', 'industrial', 'hospitality', 'religious') NOT NULL,
  size VARCHAR(100),
  location VARCHAR(255),
  year YEAR,
  description TEXT,
  featured_image VARCHAR(500),
  status ENUM('published', 'draft', 'hidden') DEFAULT 'draft',
  sort_order INT DEFAULT 0,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE project_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  image_path VARCHAR(500) NOT NULL,
  alt_text VARCHAR(255),
  sort_order INT DEFAULT 0,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO settings (setting_key, setting_value) VALUES ('maintenance_mode', '1');
```

**Step 3: Write includes/db.php**

```php
<?php
$db = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);
```

**Step 4: Add DB constants to config.php**

```php
// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'parths5_moksha');
define('DB_USER', 'parths5_moksha');
define('DB_PASS', 'GENERATED_PASSWORD');
```

**Step 5: Run schema via cPanel MySQL or phpMyAdmin**

**Step 6: Seed admin user**

```sql
INSERT INTO users (name, email, password, role) VALUES
('Parth Patel', 'parth@moksha.construction', '$2y$12$HASH', 'admin'),
('Hari', 'hari@moksha.construction', '$2y$12$HASH', 'admin');
```

---

### Task 2: Auth System (Login/Logout/Sessions)

**Files:**
- Create: `includes/auth.php`
- Create: `public/admin/login.php`
- Create: `public/admin/logout.php`

**includes/auth.php** — Session management + auth guard:

```php
<?php
session_start();

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function getCurrentUser(PDO $db): ?array {
    if (!isLoggedIn()) return null;
    $stmt = $db->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function login(PDO $db, string $email, string $password): bool {
    $stmt = $db->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        return true;
    }
    return false;
}

function logout(): void {
    session_destroy();
    header('Location: /admin/login.php');
    exit;
}
```

**public/admin/login.php** — Dark-themed login page matching site design. Form with email + password fields. On POST, validate credentials, redirect to `/admin/` on success.

**public/admin/logout.php** — Call `logout()`, redirect to login.

---

### Task 3: Admin Dashboard

**Files:**
- Create: `public/admin/index.php` (dashboard)
- Create: `public/admin/includes/admin-header.php`
- Create: `public/admin/includes/admin-footer.php`
- Create: `public/admin/assets/admin.css` (minimal admin styles)

**Dashboard features:**
- Header: Logo + "Admin Panel" + logged-in user name + Logout link
- Stats row: Total Projects, Published, Draft, Hidden (counts from DB)
- Project list table: Title, Type, Status badge (green/yellow/gray), Date, Actions (Edit / View / Hide/Show)
- "Add New Project" gold CTA button
- Maintenance mode toggle (reads/writes `settings` table)
- "View Live Site" link (opens preview URL)

**Admin styles:** Reuse the site's dark theme colors. Minimal CSS — no Tailwind in admin (keep it simple, inline styles or a single admin.css). Dark cards, gold accents, clean table layout.

---

### Task 4: Project Create Form

**Files:**
- Create: `public/admin/project-create.php`
- Create: `includes/upload.php` (image handling)

**Form fields:**
- Title (text) → auto-generates slug via JS (editable)
- Type (dropdown: Residential, Commercial, Industrial, Hospitality, Religious)
- Size (text, e.g., "280,000 sq ft")
- Location (text)
- Year (number, defaults to current year)
- Description (textarea with basic rich text — use a simple WYSIWYG like Trumbowyg, lightweight)
- Featured Image (file upload with preview)
- Gallery Images (multi-file upload with drag-drop reorder)
- Status (radio: Published / Draft / Hidden)

**On submit:**
1. Validate required fields (title, type)
2. Generate slug from title (lowercase, hyphens, strip special chars)
3. Upload + process featured image → resize to max 2000px, convert to WebP, save to `/assets/images/projects/{slug}/`
4. Upload + process gallery images → same processing
5. INSERT into `projects` table
6. INSERT gallery images into `project_images` table
7. Redirect to dashboard with success message

**includes/upload.php:**
```php
function processImage(string $tmpPath, string $destDir, string $filename, int $maxWidth = 2000): string {
    // Create dest dir if needed
    // Load image with GD (support JPEG, PNG, WebP)
    // Resize if wider than $maxWidth (maintain aspect ratio)
    // Save as WebP at 85% quality
    // Return relative path
}

function generateThumbnail(string $sourcePath, string $destDir, string $filename, int $thumbWidth = 600): string {
    // Same as above but smaller
}
```

---

### Task 5: Project Edit Form

**Files:**
- Create: `public/admin/project-edit.php`

**Same form as create, but:**
- Pre-populated with existing data from DB
- Featured image shows current image with "Change" option
- Gallery shows existing images with delete (X) buttons + reorder + add more
- On submit: UPDATE instead of INSERT
- Handle image deletions (remove from DB + filesystem)
- Handle new image additions
- Handle gallery reorder (sort_order update)

---

### Task 6: Project Delete + Status Toggle

**Files:**
- Create: `public/admin/project-delete.php` (POST endpoint)
- Create: `public/admin/project-toggle.php` (POST endpoint)

**Delete:** Confirm dialog → DELETE from DB → remove image folder from filesystem → redirect to dashboard.

**Toggle:** POST with project ID + new status → UPDATE `projects SET status = ?` → redirect back.

---

### Task 7: Dynamic Public Project Pages

**Files:**
- Create: `public/project.php` (single project detail page)
- Modify: `public/projects.php` (read from DB instead of static HTML)
- Modify: `public/.htaccess` (add route for `/projects/{slug}`)
- Modify: `public/index.php` (featured projects from DB)

**public/project.php** — Single project detail page:
- Hero with featured image
- Title, type badge, size, location, year
- Full description (HTML from rich text editor)
- Image gallery (lightbox on click)
- Related projects (same type, max 3)
- CTA section

**public/projects.php** — Replace static cards with DB query:
```php
$stmt = $db->prepare('SELECT * FROM projects WHERE status = "published" ORDER BY sort_order, created_at DESC');
```
- Keep the Alpine.js filter (filter by `type` from DB)
- Render project cards dynamically

**public/index.php** — Featured projects section:
```php
$stmt = $db->prepare('SELECT * FROM projects WHERE status = "published" ORDER BY sort_order LIMIT 3');
```

**.htaccess** — Add route:
```apache
RewriteRule ^projects/([a-z0-9-]+)$ /project.php?slug=$1 [L]
```

---

### Task 8: Maintenance Mode Toggle (Admin)

**Files:**
- Modify: `public/admin/index.php` (add toggle UI)
- Modify: `public/.htaccess` (read setting dynamically)
- Create: `public/maintenance-check.php` (PHP-based check instead of .htaccess)

Since .htaccess can't read MySQL, switch maintenance mode from .htaccess redirect to PHP check:

**Approach:** Remove maintenance redirect from `.htaccess`. Instead, add a PHP check at the top of every public page (via `header.php`):

```php
// In includes/header.php, before any output:
require_once __DIR__ . '/db.php';
$stmt = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'maintenance_mode'");
$maintenance = $stmt->fetchColumn();
if ($maintenance === '1' && !isset($_COOKIE['moksha_preview'])) {
    // Not admin, not preview — show maintenance
    include __DIR__ . '/../public/maintenance.html';
    exit;
}
```

**Admin toggle:** Simple form on dashboard that POSTs to update the `settings` table.

---

### Task 9: Seed Existing 5 Projects into Database

**Files:**
- Create: `database/seed-projects.sql`

Migrate the 5 existing static projects (Exhibition Center, Office Building, Hotel, Lotus Villa, Retail Center) into the database with their existing images and descriptions.

---

### Task 10: Deploy to InMotion

**Steps:**
1. Create MySQL database + user via cPanel API
2. Run schema.sql via cPanel API or phpMyAdmin
3. Seed admin users (generate bcrypt passwords)
4. Seed existing projects
5. Upload all new/modified files via FTP
6. Test admin login
7. Test project CRUD
8. Test public project pages
9. Test maintenance mode toggle

---

## File Structure After CMS

```
moksha-construction/
├── public/
│   ├── admin/
│   │   ├── index.php              # Dashboard
│   │   ├── login.php              # Login page
│   │   ├── logout.php             # Logout
│   │   ├── project-create.php     # Add project form
│   │   ├── project-edit.php       # Edit project form
│   │   ├── project-delete.php     # Delete handler (POST)
│   │   ├── project-toggle.php     # Status toggle (POST)
│   │   ├── settings.php           # Maintenance toggle (POST)
│   │   ├── includes/
│   │   │   ├── admin-header.php
│   │   │   └── admin-footer.php
│   │   └── assets/
│   │       └── admin.css
│   ├── project.php                # Dynamic project detail page
│   ├── projects.php               # (modified — reads from DB)
│   ├── index.php                  # (modified — featured from DB)
│   └── ... (existing pages)
├── includes/
│   ├── db.php                     # PDO connection
│   ├── auth.php                   # Session auth
│   ├── upload.php                 # Image processing
│   ├── config.php                 # (modified — DB constants added)
│   └── ... (existing includes)
└── database/
    ├── schema.sql
    └── seed-projects.sql
```
