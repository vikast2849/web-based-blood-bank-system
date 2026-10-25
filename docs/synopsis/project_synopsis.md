# Project Synopsis

**Project Title:** A Web-Based Blood Bank System for Managing Records of Donors and Recipients  
**Institution:** S.J.M. Institute of Technology, Chitradurga  
**Department:** Computer Science & Engineering  
**Affiliation:** Visvesvaraya Technological University (VTU), Belagavi (2025-2026)  

### Team Members
- **Somesha K** (USN: 4SM23CS093)
- **Ummar Farukh** (USN: 4SM23CS109)
- **Vikas** (USN: 4SM23CS117)
- **Vikas R Rathod** (USN: 4SM23CS118)

### Project Mentors
- **Guide:** Prof. Ambika S (B.E., M.Tech., Asst. Prof., Dept. of CS&E)
- **Coordinator:** Prof. Pushpalatha M (B.E., M.Tech., Asst. Prof., Dept. of CS&E)
- **HOD:** Dr. Krishnareddy K R (M.Tech., Ph.D., Professor & HOD, Dept. of CS&E)

---

## 1. Abstract
The Web-Based Blood Bank System is an online platform developed to efficiently manage and store records of blood donors, hospitals, and recipients. Built using PHP for server-side logic and MySQL/MariaDB as the relational database, the primary goal of this application is to streamline blood inventory tracking and the request-fulfillment workflow. 

The system enables hospitals to advertise available blood stocks and manage patient requests, while recipients can search for compatible blood groups in real time and submit requests directly to hospitals. By digitizing record-keeping, the application eliminates manual paperwork, minimizes errors, improves emergency response time, and ensures transparency.

## 2. Objectives
- **Centralized Record Management:** Maintain accurate real-time records of hospitals, available blood units, and patient requests.
- **Fast Information Retrieval:** Provide instantaneous search and filtering of available blood groups across multiple healthcare providers.
- **Inventory Tracking:** Allow hospitals to easily add, monitor, and remove blood stocks.
- **Request Coordination:** Facilitate a two-way request lifecycle (Pending, Accepted, Rejected) between hospitals and receivers.
- **Security & Reliability:** Implement robust authentication, secure password hashing (bcrypt), prepared statements to eliminate SQL injection, and authorization barriers to protect sensitive patient records.

## 3. Technology Stack
- **Front-End:** HTML5, CSS3, JavaScript, jQuery, Bootstrap 4
- **Back-End:** PHP 7.4 / 8.x (Object-Oriented & Procedural MySQLi with Prepared Statements)
- **Database:** MySQL / MariaDB (InnoDB, Foreign Key Constraints, Unique Indices)
- **Deployment:** Docker & Docker Compose / Apache (XAMPP/WAMP) / PHP Built-in Server

## 4. Key Functional Modules
1. **Hospital Module:**
   - Registration & Secure Authentication
   - Blood Stock Inventory Management (Add blood groups, prevent duplicate stock entries, delete units)
   - Incoming Request Processing (Accept or Reject pending recipient requests)
   - Profile Management
2. **Receiver Module:**
   - Registration & Authentication
   - Real-Time Blood Stock Search & Filter by Blood Group
   - Sample Request Submission with duplicate prevention
   - Sent Request Tracking & Cancellation
   - Receiver Profile & Compatibility Information
3. **Security Architecture:**
   - Parameterized queries (`mysqli::prepare` and `bind_param`) across all database operations
   - Bcrypt password hashing (`PASSWORD_DEFAULT`) with seamless upgrade for legacy credentials
   - Output sanitization (`htmlspecialchars`) on all rendered user inputs and flash messages
   - Strict role-based session authorization preventing parameter tampering or cross-user modifications
