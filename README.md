# Web-Based Blood Bank System

A secure, responsive PHP + MySQL web application designed to connect **hospitals** and **blood receivers**. Hospitals publish available blood stocks and manage patient requests; receivers search compatible blood groups in real time and submit requests directly to hospitals.

![Blood Bank System - Home](docs/screenshots/home.png)

---

## Key Features

### Hospital Module
- **Registration & Authentication:** Register hospital accounts with validated credentials and contact details.
- **Stock Management:** Add available blood groups (`A+`, `A-`, `B+`, `B-`, `AB+`, `AB-`, `O+`, `O-`) to the blood bank inventory; duplicate entries are strictly rejected.
- **Inventory Control:** Remove expired or transferred blood samples with owner-isolated deletion.
- **Request Management:** View patient requests and either **Accept** or **Reject** them.
- **Profile Updates:** Manage hospital address, contact telephone, and password updates.

### Receiver Module
- **Registration & Authentication:** Register recipient accounts with designated blood group and contact information.
- **Search & Filter:** Instantaneous blood availability lookups filtered by blood group.
- **Sample Request Workflow:** Request blood units from specific hospitals; duplicate requests are rejected.
- **Request Tracking & Cancellation:** Live status tracking (`Pending`, `Accepted`, `Rejected`) and one-click request cancellation.
- **Profile Management:** Update personal contact info and preferences.

### Security & Engineering Standards
- **SQL Injection Prevention:** Prepared statements with parameterized queries (`mysqli::prepare`) across all database operations.
- **Password Security:** Modern bcrypt hashing (`PASSWORD_DEFAULT`) with automated on-the-fly upgrades for legacy demo credentials.
- **Cross-Site Scripting (XSS) Prevention:** Output escaping (`htmlspecialchars`) across all forms, dynamic tables, and URL flash parameters.
- **Authorization & Ownership Checks:** Strict session role boundaries prevent URL parameter tampering across hospitals and receivers.
- **PHP 7.4 to 8.x Compatibility:** Verified with zero runtime warnings or deprecation notices under PHP 8+.

---

## Tech Stack
- **Front-End:** HTML5, CSS3, Bootstrap 4, JavaScript, jQuery, Font Awesome
- **Back-End:** PHP 7.4 / 8.x
- **Database:** MySQL 5.7+ / MariaDB 10.4+
- **Containerization:** Docker & Docker Compose

---

## Project Structure
```
web-based-blood-bank-system/
├── src/                    # Application source code
│   ├── css/                # Custom CSS styling
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
│   ├── login.php / register.php / logout.php
│   └── index.php           # Landing page with compatibility charts
├── database/
│   └── bloodbank.sql       # Database schema, indices & seed data
├── docs/                   # Academic documentation & screenshots
│   ├── report/             # Official VTU Mini-Project Report (PDF)
│   ├── synopsis/           # Project synopsis & abstract
│   ├── ppt/                # Seminar presentation notes & slide outlines
│   └── screenshots/        # High-resolution screenshots of the UI
├── Dockerfile              # PHP 8.2 Apache container configuration
├── docker-compose.yml      # Multi-container orchestration (PHP + MariaDB)
└── README.md
```

---

## How to Run

### Option 1 — PHP Built-in Server + MySQL / XAMPP (Fastest)

1. **Start MySQL / MariaDB** (e.g. via XAMPP Control Panel or MySQL service).
2. **Import Database:**
   ```bash
   mysql -u root -p < database/bloodbank.sql
   ```
   *(Or import `database/bloodbank.sql` via phpMyAdmin at `http://localhost/phpmyadmin`)*.
3. **Start the PHP Development Server:**
   ```bash
   php -S 127.0.0.1:8000 -t src
   ```
4. **Open in Browser:**  
   Navigate to `http://localhost:8000/`.

---

### Option 2 — XAMPP Apache VirtualHost / Subfolder

1. Copy the contents of `src/` to `C:\xampp\htdocs\bloodbank\`.
2. Import `database/bloodbank.sql` in phpMyAdmin.
3. Access `http://localhost/bloodbank/`.

---

### Option 3 — Docker Compose

```bash
docker compose up --build
```
Open `http://localhost:8080/`. The database is automatically provisioned and seeded from `database/bloodbank.sql`.

To shut down and wipe persistent volumes:
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

> **Note on Password Migration:** Demo credentials stored in plain-text within the seed SQL are automatically upgraded to strong bcrypt hashes in the database upon their first successful login.

---

## Test & Verification Results

All core workflows and security requirements have been validated end-to-end:

| Test Item | Status | Details |
|---|---|---|
| **Clean Page Loads** | **PASS** | `index.php`, `login.php`, `register.php`, `abs.php` load with zero PHP warnings or notices |
| **Hospital Workflow** | **PASS** | Registration, login, stock addition, duplicate rejection, deletion, profile editing, logout |
| **Receiver Workflow** | **PASS** | Registration, login, blood group search, sample request, duplicate request rejection, cancellation, profile editing |
| **Request Lifecycle** | **PASS** | Hospital accepts/rejects requests; Receiver sent-request dashboard reflects updated status in real time |
| **Credential Rehash** | **PASS** | Seeded plain-text passwords (`gandhi`, `test`) authenticate cleanly and are immediately re-hashed to bcrypt |
| **Authorization Boundaries** | **PASS** | Receivers cannot accept, reject, or delete records via URL parameter manipulation; cross-tenant cancellation blocked |
| **SQL Injection & XSS** | **PASS** | `' OR 1=1 --` injection attempts fail; `<script>` tags in URL parameters (`?msg=`, `?error=`) are strictly escaped |

---

## UI Screenshots

| Available Blood Samples | Login Page |
|---|---|
| ![Available Blood Samples](docs/screenshots/available_samples.png) | ![Login](docs/screenshots/login.png) |

| Registration Page | Landing & Compatibility Info |
|---|---|
| ![Register](docs/screenshots/register.png) | ![Home](docs/screenshots/home.png) |

---

## Academic Documents

Academic submission materials for Visvesvaraya Technological University (VTU) are located in [`docs/`](docs/):
- **Project Report:** [`docs/report/Blood_Bank_Report.pdf`](docs/report/Blood_Bank_Report.pdf)
- **Synopsis:** [`docs/synopsis/project_synopsis.md`](docs/synopsis/project_synopsis.md)
- **Seminar PPT Notes:** [`docs/ppt/presentation_notes.md`](docs/ppt/presentation_notes.md)

---

## License & Attribution
Based on the academic project *"A Web-based Blood Bank System for Managing Records of Donors and Recipients"*, Department of Computer Science & Engineering, S.J.M. Institute of Technology, Chitradurga.
