# Web-Based Blood Bank System

[![PHP Version](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/Database-MySQL%20|%20MariaDB-00758F?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Web Server](https://img.shields.io/badge/Server-Apache%202.4-D22128?logo=apache&logoColor=white)](https://httpd.apache.org/)
[![Docker](https://img.shields.io/badge/Deployment-Docker%20|%20Render-2496ED?logo=docker&logoColor=white)](https://render.com/)
[![UI Theme](https://img.shields.io/badge/UI-Bootstrap%204%20Modern%20Crimson-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![Tests](https://img.shields.io/badge/Tests-10%2F10%20PASS-16A34A?logo=checkmarx&logoColor=white)](#automated-test-suite--verification)

> **Live Production Demo:** [https://bloodbank.vikasst.shop/](https://bloodbank.vikasst.shop/)  
> **Render Service URL:** [https://web-based-blood-bank.onrender.com/](https://web-based-blood-bank.onrender.com/)  
> *(Root automatically opens the Login portal with zero PHP warnings)*

A secure, responsive PHP & MySQL web application designed to connect **hospitals** and **blood receivers**. Hospitals publish available blood stocks, monitor inventory, and accept or reject patient requests. Receivers search compatible blood groups in real time, view hospital contact information, and track request status.

![Blood Bank System - Home](docs/screenshots/home.png)

---

## Live Deployment Architecture (Render + Cloud MySQL)

The application is containerized with Docker and deployed on **Render** under the custom subdomain **`bloodbank.vikasst.shop`**:

- **Public URL:** [https://bloodbank.vikasst.shop/](https://bloodbank.vikasst.shop/)
- **Render Service Name:** `web-based-blood-bank`
- **Render Default URL:** `https://web-based-blood-bank.onrender.com/`
- **Container Runtime:** `php:8.2-apache` with `mysqli`, `pdo`, `pdo_mysql` extensions
- **Custom DNS Mapping:**
  - **Subdomain:** `bloodbank.vikasst.shop`
  - **Type:** `CNAME`
  - **Target:** `web-based-blood-bank.onrender.com`
  - **SSL:** Automatic free TLS certificate issued and auto-renewed by Render via Cloudflare / Let's Encrypt

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
- **Back-End:** PHP 8.2 (compatible with 7.4 - 8.3)
- **Database:** MySQL 5.7+ / MariaDB 10.4+ / Cloud MySQL (Aiven, Railway, TiDB)
- **Containerization & Hosting:** Docker, Apache 2.4, Render Web Service

---

## Environment Variables

All database credentials and runtime configurations are managed via environment variables. Copy `.env.example` to `.env` for local runs, or configure them in the Render Dashboard under **Environment**:

| Variable | Description | Example / Default | Required in Production |
|---|---|---|:---:|
| `DB_HOST` | Hostname of the MySQL server | `mysql.railway.internal` or `...aivencloud.com` | **Yes** |
| `DB_PORT` | Port of the MySQL server | `3306` | No (defaults to `3306`) |
| `DB_NAME` | Database name | `bloodbank` | **Yes** |
| `DB_USER` | Database username | `avnadmin` or `root` | **Yes** |
| `DB_PASS` | Database user password | `your_secret_password` | **Yes** |
| `APP_ENV` | Application environment | `production` / `development` | No |
| `PORT` | Listening port for Apache (set by Render) | `10000` or `80` | Automatic on Render |

---

## Cloud Database Setup (Aiven / Railway / Cloud MySQL)

The pure database schema without sample personal data is located at [`database/schema.sql`](database/schema.sql).

### Step 1: Provision a Free Cloud MySQL Instance
Create a free database on any cloud MySQL provider:
- **Aiven for MySQL:** [https://aiven.io/](https://aiven.io/) (Free tier available)
- **Railway:** [https://railway.app/](https://railway.app/) (One-click MySQL plugin)
- **TiDB Cloud Serverless:** [https://tidbcloud.com/](https://tidbcloud.com/) (MySQL-compatible free tier)

### Step 2: Import the Schema
Using your local terminal or phpMyAdmin / MySQL Workbench:
```bash
mysql -h <DB_HOST> -P <DB_PORT> -u <DB_USER> -p <DB_NAME> < database/schema.sql
```

*(Optional)* If you wish to seed demo accounts for demonstration, also run:
```bash
mysql -h <DB_HOST> -P <DB_PORT> -u <DB_USER> -p <DB_NAME> < database/bloodbank.sql
```

---

## Deploy to Render Steps

### Method A: Connect Existing GitHub Repository
1. Log into your [Render Dashboard](https://dashboard.render.com/).
2. Click **New +** -> **Web Service**.
3. Connect your repository: `https://github.com/vikast2849/web-based-blood-bank-system`.
4. Configure the service settings:
   - **Name:** `web-based-blood-bank`
   - **Language / Runtime:** `Docker`
   - **Branch:** `main`
   - **Plan:** `Free`
5. In the **Environment Variables** section, add your database credentials:
   - `DB_HOST`: *(Your Cloud DB Host)*
   - `DB_PORT`: `3306` *(or custom port)*
   - `DB_NAME`: `bloodbank`
   - `DB_USER`: *(Your Cloud DB Username)*
   - `DB_PASS`: *(Your Cloud DB Password)*
   - `APP_ENV`: `production`
6. Click **Create Web Service**.
7. Once the build finishes and status turns to **Live**, go to **Settings** -> **Custom Domains**:
   - Add: `bloodbank.vikasst.shop`
   - Render verifies the existing CNAME record (`bloodbank.vikasst.shop -> web-based-blood-bank.onrender.com`) and automatically provisions the SSL certificate.

---

## How to Run Locally

### Option 1 — 1-Click Windows Launcher (`start.bat`) [Recommended]

Simply **double-click** `start.bat` in the root folder.

The launcher automatically:
1. Detects PHP in your system `PATH` or at `C:\xampp\php\php.exe`.
2. Starts the background MySQL daemon if not yet running.
3. Automatically creates and provisions the database via `tools\setup_db.php`.
4. Starts the PHP built-in web server on `0.0.0.0:8000`.
5. Waits for port readiness and opens `http://localhost:8000/` without connection refused errors.

---

### Option 2 — Docker Container

```bash
docker build -t blood-bank-system .
docker run -p 8080:80 --env-file .env blood-bank-system
```
Access at `http://localhost:8080/`.

---

## Automated Test Suite & Verification

The repository includes a comprehensive automated end-to-end smoke test suite in [`tests/smoke_test.php`](tests/smoke_test.php). Run it locally with:

```bash
php tests/smoke_test.php
```

| Test Suite | Test Case | Status | Details |
|---|---|:---:|---|
| **Routing** | Root URL redirects to login.php | **PASS** | Status: 302, Location: login.php |
| **Pages** | Clean page loads with zero PHP warnings | **PASS** | Checked `login.php`, `register.php`, `abs.php`, `info.php` |
| **Auth** | Seeded hospital `gandhi@gmail.com` logs in | **PASS** | Authenticates & updates password hash to bcrypt |
| **Auth** | Seeded receiver `test@gmail.com` logs in | **PASS** | Authenticates & updates password hash to bcrypt |
| **Hospital** | Complete lifecycle & profile updates | **PASS** | Registration, stock add/delete, profile updates, logout |
| **Receiver** | Complete lifecycle & requests | **PASS** | Registration, filter, request, duplicate reject, cancel |
| **Workflow** | Hospital accepts/rejects; Receiver updates | **PASS** | Live status updates reflected immediately |
| **Security** | Authorization boundary tampering blocked | **PASS** | URL tampering on accept/reject/delete blocked |
| **Security** | SQL injection payload `' OR 1=1 --` fails | **PASS** | Rejected on both hospital and receiver endpoints |
| **Security** | `<script>` in `?msg=` rendered escaped | **PASS** | Escaped with `htmlspecialchars` |

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
