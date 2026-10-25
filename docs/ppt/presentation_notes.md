# Presentation Structure: Web-Based Blood Bank System

**Title:** A Web-Based Blood Bank System for Managing Records of Donors and Recipients  
**Presented by:** Somesha K, Ummar Farukh, Vikas, Vikas R Rathod  
**Department of CS&E, S.J.M Institute of Technology, Chitradurga**  
**Academic Year:** 2025–2026  

---

### Slide 1: Title & Presentation Details
- **Project Title:** A Web-Based Blood Bank System for Managing Records of Donors and Recipients
- **Candidate Details:**
  - Somesha K (4SM23CS093)
  - Ummar Farukh (4SM23CS109)
  - Vikas (4SM23CS117)
  - Vikas R Rathod (4SM23CS118)
- **Supervision:** Guide Prof. Ambika S | Coordinator Prof. Pushpalatha M | HOD Dr. Krishnareddy K R

### Slide 2: Introduction & Motivation
- Critical need for rapid blood matching during medical emergencies.
- Shortcomings of manual paper logs and fragmented communication.
- Value of a centralized, secure web-based blood inventory management platform.

### Slide 3: Objectives
- Establish an automated, real-time repository of available blood groups.
- Bridge communication between blood receivers and participating hospitals.
- Implement strict data integrity, role-based access control, and injection prevention.

### Slide 4: System Architecture
- **Client Tier:** Responsive web interface (HTML5, Bootstrap 4, jQuery).
- **Application Tier:** PHP 8 engine processing authentication, authorization, and business logic.
- **Data Tier:** MariaDB/MySQL relational database enforcing foreign key relationships and unique constraints.

### Slide 5: Entity-Relationship & Database Design
- `hospitals`: Manages hospital accounts, credentials, contact details, and locations.
- `receivers`: Manages recipient user accounts, default blood groups, and locations.
- `bloodinfo`: Stores current blood stock units mapped to hospitals.
- `bloodrequest`: Tracks transaction lifecycle (`Pending`, `Accepted`, `Rejected`) between receivers and hospitals.

### Slide 6: Security & Implementation Highlights
- Prepared statements with parameterized queries preventing SQL injection.
- Bcrypt password hashing (`PASSWORD_DEFAULT`) with automated legacy migration.
- XSS prevention through contextual HTML escaping (`htmlspecialchars`).
- Strict session-based access control preventing horizontal and vertical privilege escalation.

### Slide 7: Demonstration & Key Interfaces
- Receiver Registration & Compatibility Overview.
- Available Blood Samples search and live request submission.
- Hospital Dashboard: Inventory management (Add/Delete) and Request actioning (Accept/Reject).
- Real-time request tracking for patients.

### Slide 8: Testing & Results
- Comprehensive black-box and white-box test coverage.
- Validation of end-to-end user workflows, error handling, and authorization boundaries.
- 100% test pass rate across core functional and security test suites.

### Slide 9: Conclusion & Future Scope
- Elimination of manual delays and reduction of stock management overhead.
- Future enhancements: SMS/WhatsApp notification gateway integration, geolocation mapping, and mobile app support.
