# Web-Based Blood Bank System

[![PHP Version](https://img.shields.io/badge/PHP-7.4%20|%208.0%20|%208.3-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/Database-MySQL%20|%20MariaDB-00758F?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Web Server](https://img.shields.io/badge/Server-Nginx%20|%20Apache-009639?logo=nginx&logoColor=white)](https://nginx.org/)
[![Bootstrap](https://img.shields.io/badge/UI-Bootstrap%204%20Modern%20Crimson-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![Tests](https://img.shields.io/badge/Tests-10%2F10%20PASS-16A34A?logo=checkmarx&logoColor=white)](#automated-test-suite--verification)

> **Live Production Demo:** [https://bloodbank.harishp.shop/](https://bloodbank.harishp.shop/)  
> *(Root automatically opens the Login portal with zero PHP warnings)*

A secure, responsive PHP & MySQL web application designed to connect **hospitals** and **blood receivers**. Hospitals publish available blood stocks, monitor inventory, and accept or reject patient requests. Receivers search compatible blood groups in real time, view hospital contact information, and track request status.

![Blood Bank System - Home](docs/screenshots/home.png)

---

## Live Deployment & Cloud Architecture

The application is deployed on an **AWS EC2 Amazon Linux 2023** instance alongside an existing web portfolio without conflict:

- **Live URL:** [https://bloodbank.harishp.shop/](https://bloodbank.harishp.shop/) (HTTP: [http://bloodbank.harishp.shop/](http://bloodbank.harishp.shop/))
- **Web Server:** Nginx 1.30.5 reverse proxying to PHP-FPM 8.3 (`unix:/run/php-fpm/www.sock`).
- **Database Engine:** MariaDB 10.5 on `localhost` with a dedicated, least-privilege `bloodbank` user.
- **Security Isolation:** `/var/www/bloodbank/src/file/config.local.php` is strictly denied in Nginx (`return 404`). Hidden dotfiles (`.git`, `.env`) are denied.
- **DNS Configuration:**
  - **Type:** `A`
  - **Name:** `bloodbank` (or `bloodbank.harishp.shop`)
  - **Value:** `63.178.65.191`
  - **TTL:** 300 seconds

---

## Key Features

### Hospital Module
- **Registration & Authentication:** Secure hospital onboarding with validated phone number, city, and credentials.
- **Stock Inventory Control:** Add available blood groups (`A+`, `A-`, `B+`, `B-`, `AB+`, `AB-`, `O+`, `O-`); duplicate additions are rejected.
- **Sample Removal:** Delete expired or transferred blood samples with owner-isolated permissions.
- **Request Management:** View incoming recipient requests and either **Accept** or **Reject** them.
- **Profile Updates:** Edit address, contact numbers, and update passwords securely.

### Receiver Module
- **Registration & Authentication:** Register recipient accounts with designated blood group and contact details.
- **Search & Filter:** Instantaneous blood availability lookups filtered by blood group and city.
- **Sample Request Workflow:** Request blood units directly from specific hospitals; duplicate requests are rejected.
- **Request Tracking & Cancellation:** Live status tracking (`Pending`, `Accepted`, `Rejected`) and one-click request cancellation.
- **Profile Management:** Update personal contact info and preferences.

### Security & Engineering Standards
- **SQL Injection Prevention:** 100% prepared statements with parameterized queries (`mysqli::prepare`) across all database interactions.
- **Password Security:** Modern bcrypt hashing (`PASSWORD_DEFAULT`) with automated on-the-fly upgrades for legacy demo credentials.
- **Cross-Site Scripting (XSS) Prevention:** Output escaping via `htmlspecialchars` across all dynamic tables, forms, and URL parameters (`?msg=`, `?error=`).
- **Authorization & Ownership Checks:** Strict session role boundaries prevent URL parameter tampering across hospitals and receivers.
- **PHP 7.4 to 8.x Compatibility:** Zero runtime notices, warnings, or deprecated function calls.

---

## Tech Stack
- **Front-End:** HTML5, CSS3, Modern Crimson SaaS Theme, Bootstrap 4, JavaScript, jQuery, Font Awesome
- **Back-End:** PHP 7.4 / 8.0 / 8.2 / 8.3
- **Database:** MySQL 5.7+ / MariaDB 10.4+
- **Server:** Nginx + PHP-FPM / Apache / PHP CLI Built-in Server
- **Containerization:** Docker & Docker Compose

---

## Project Structure
```
web-based-blood-bank-system/
├── src/                    # Application source code
│   ├── css/                # Custom CSS styling (style.css with CSS variables)
│   ├── image/              # Application images & icons
│   ├── file/               # Backend action handlers & database connection
│   │   ├── connection.php  # Database connection & security helpers
│   │   ├── hospitalLogin.php / hospitalReg.php
│   │   ├── receiverLogin.php / receiverReg.php
│   │   ├── infoAdd.php / delete.php
│   │   ├── request.php / cancel.php
│   │   ├── accept.php / reject.php
│   │   └── updateprofile.php
│   ├── abs.php             # Available blood samples & search page
│   ├── bloodinfo.php       # Hospital stock management page
│   ├── bloodrequest.php    # Hospital request management page
│   ├── sentrequest.php     # Receiver sent requests page
│   ├── info.php            # Compatibility matrix & WHO health guidelines
│   ├── login.php           # Unified tabbed login portal
│   ├── register.php        # Unified tabbed registration portal
│   ├── logout.php          # Session termination
│   └── index.php           # Entrypoint (redirects to login / dashboard)
├── database/
│   └── bloodbank.sql       # Database schema, indices & seed data
├── deploy/                 # Production deployment scripts
│   ├── ec2-nginx-setup.sh  # Automated setup for Nginx + PHP-FPM + MariaDB on EC2
│   └── ec2-setup.sh        # Setup script for Apache
├── docs/                   # Academic documentation & screenshots
│   ├── report/             # VTU Mini-Project Report (PDF)
│   ├── synopsis/           # Project synopsis & abstract
│   ├── ppt/                # Seminar presentation notes & slide outlines
│   └── screenshots/        # Desktop & mobile screenshots of the UI
├── tests/                  # Automated verification
│   └── smoke_test.php      # End-to-end integration and security test suite
├── tools/                  # Developer utilities
│   ├── setup_db.php        # Automated database provisioner and table creator
│   └── capture_screenshots.py # Headless browser screenshot generator
├── start.bat               # 1-click Windows launcher (handles MySQL, DB & server)
├── start.sh                # 1-click macOS/Linux launcher
├── Dockerfile              # PHP 8 Apache container configuration
├── docker-compose.yml      # Multi-container orchestration (PHP + MariaDB)
└── README.md
```

---

## How to Run Locally

### Option 1 — 1-Click Windows Launcher (`start.bat`) [Recommended]

Simply **double-click** `start.bat` in the root folder.

The launcher automatically:
1. Detects PHP in your system `PATH` or at `C:\xampp\php\php.exe`.
2. Checks whether MySQL daemon is active on port `3306`; if not, starts `C:\xampp\mysql\bin\mysqld.exe` standalone in the background.
3. Runs `tools\setup_db.php` to verify the `bloodbank` database and import tables/seeds on first run.
4. Starts the PHP built-in web server on `0.0.0.0:8000`.
5. Polls for port `8000` to be fully listening, eliminating any *"localhost refused to connect"* race condition.
6. Automatically opens your default web browser to `http://localhost:8000/`.

---

### Option 2 — macOS / Linux (`start.sh`)

Ensure MySQL / MariaDB is running, then execute:
```bash
bash start.sh
```
The script runs `tools/setup_db.php` to seed the database and serves the app at `http://localhost:8000/`.

---

### Option 3 — Manual PHP Built-in Server + MySQL / XAMPP

1. **Start MySQL / MariaDB** (via XAMPP Control Panel or MySQL service).
2. **Import Database:**
   ```bash
   mysql -u root < database/bloodbank.sql
   ```
   *(Or run `php tools/setup_db.php`)*.
3. **Start the PHP Development Server:**
   ```bash
   php -S 0.0.0.0:8000 -t src
   ```
4. **Open in Browser:**  
   Navigate to `http://localhost:8000/`.

---

### Option 4 — Docker Compose

```bash
docker compose up --build
```
Open `http://localhost:8080/`. The database is automatically provisioned and seeded from `database/bloodbank.sql`.

To stop the containers:
```bash
docker compose down -v
```

---

## Demo Accounts

The database comes pre-seeded with sample hospital and receiver accounts for evaluation:

| Role | Email | Seed Password | Note |
|---|---|---|---|
| **Hospital** | `gandhi@gmail.com` | `gandhi` | Gandhi Hospital, Delhi |
| **Hospital** | `unknown@gmail.com` | `unknown` | Unknown Hospital |
| **Receiver** | `test@gmail.com` | `test` | Lucknow (Blood Group: A+) |
| **Receiver** | `xyz@gmail.com` | `xyz` | Punjab (Blood Group: AB+) |

> **Automatic Password Upgrade:** Plain-text demo credentials are automatically upgraded to secure bcrypt hashes in the database upon the user's first successful login.

---

## Automated Test Suite & Verification

The repository includes a comprehensive automated end-to-end smoke test suite in `tests/smoke_test.php`. Run it with:

```bash
php tests/smoke_test.php
```

### Test Results

| Test Suite | Test Case | Status | Details |
|---|---|:---:|---|
| **Routing** | Root URL redirects to login.php | **PASS** | Status: 302, Location: login.php |
| **Pages** | Pages load with 200 OK and no PHP warnings | **PASS** | Checked `login.php`, `register.php`, `abs.php`, `info.php` |
| **Auth** | Seeded hospital `gandhi@gmail.com` logs in and rehashes password | **PASS** | Plain: `gandhi` -> Hash: `$2y$10$...` |
| **Auth** | Seeded receiver `test@gmail.com` logs in and rehashes password | **PASS** | Plain: `test` -> Hash: `$2y$10$...` |
| **Hospital** | Complete lifecycle & profile updates | **PASS** | Registration, login, stats, add/duplicate/delete stock, edit profile (w/ and w/o pass change), logout |
| **Receiver** | Complete lifecycle & requests | **PASS** | Registration, login, search/filter, request, duplicate reject, cancel, edit profile, logout |
| **Workflow** | Hospital accepts & rejects requests; Receiver sees status | **PASS** | Accept flow verified; Reject flow verified; Receiver sees updated status in real time |
| **Security** | Authorization boundary tampering blocked | **PASS** | Receiver URL tampering on `accept.php`, `reject.php`, `delete.php`, and cross-user `cancel.php` strictly rejected |
| **Security** | SQL injection payload `' OR 1=1 --` fails login | **PASS** | Both hospital and receiver login endpoints reject injection payloads |
| **Security** | `<script>` in `?msg=` is rendered safely escaped | **PASS** | Output escaped with `htmlspecialchars` preventing XSS |

---

## UI Screenshots Gallery

### Desktop View (1280px)

| Available Blood Samples & Search | Tabbed Login Portal |
|---|---|
| ![Available Blood Samples](docs/screenshots/available_samples.png) | ![Login](docs/screenshots/login.png) |

| Tabbed Registration Portal | Landing & Blood Compatibility Guide |
|---|---|
| ![Register](docs/screenshots/register.png) | ![Home](docs/screenshots/home.png) |

### Mobile View (375px)

| Mobile Login | Mobile Registration |
|---|---|
| ![Mobile Login](docs/screenshots/login_mobile.png) | ![Mobile Register](docs/screenshots/register_mobile.png) |

| Mobile Samples Search | Mobile Compatibility Matrix |
|---|---|
| ![Mobile Samples](docs/screenshots/available_samples_mobile.png) | ![Mobile Blood Guide](docs/screenshots/blood_guide_mobile.png) |

---

## Academic Documents

Academic submission materials for Visvesvaraya Technological University (VTU) are located in [`docs/`](docs/):
- **Project Report:** [`docs/report/Blood_Bank_Report.pdf`](docs/report/Blood_Bank_Report.pdf)
- **Synopsis:** [`docs/synopsis/project_synopsis.md`](docs/synopsis/project_synopsis.md)
- **Seminar PPT Notes:** [`docs/ppt/presentation_notes.md`](docs/ppt/presentation_notes.md)

---

## License & Attribution
Based on the academic project *"A Web-based Blood Bank System for Managing Records of Donors and Recipients"*, Department of Computer Science & Engineering, S.J.M. Institute of Technology, Chitradurga.
