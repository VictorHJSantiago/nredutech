<div align="center">
  <br />
  <img src="https://raw.githubusercontent.com/victorhjsantiago/nredutech/main/public/images/nredutech.png" alt="NREduTech Logo" width="150" style="border-radius: 50%;">
  
  <h1 style="border-bottom: none; font-size: 2.5em; margin-bottom: 0;">NREduTech</h1>
  
  <strong style="font-size: 1.2em; color: #555;">
    Academic Management and Educational Resource Scheduling System
  </strong>
  
  <br />
  <br />

  <p style="font-size: 1.1em; max-width: 700px;">
    A robust, centralized solution built on the <strong>Laravel MVC</strong> architecture, designed for the integrated management of schools, classes, educational resources, and scheduling for the <strong>Regional Education Center (NRE)</strong>.
  </p>

  <p>
    <img src="https://img.shields.io/badge/status-under%20development-yellow?style=for-the-badge" alt="Project Status: Under Development">
    <img src="https://img.shields.io/badge/PHP-8.4.11-777BB4?style=for-the-badge&logo=php" alt="PHP Version">
    <img src="https://img.shields.io/badge/Laravel-12.28.1-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel Version">
    <img src="https://img.shields.io/badge/MariaDB-11.8.3-003545?style=for-the-badge&logo=mariadb" alt="Database">
  </p>

  <p>
    <strong>🇺🇸 English</strong>
    ·
    <a href="README.pt-BR.md">🇧🇷 Português</a>
    ·
    <a href="README.es.md">🇪🇸 Español</a>
  </p>
</div>

---

## 📖 About the Project

**NREduTech** is an Academic Management System (AMS) designed to serve as the central administration platform for the Regional Education Center. The application addresses the challenge of efficiently managing the allocation of educational resources, lab scheduling, and the organization of curriculum components across multiple educational institutions.

From an academic standpoint, the project is a practical implementation of the principles of **Object-Oriented Software Development (OOP)** and the **Model-View-Controller (MVC)** architecture. It uses the Laravel framework to ensure rapid, secure, and scalable development, abstracting low-level complexities and allowing full focus on business rules.

The platform is designed with a focus on different user profiles (Administrators, Principals, and Teachers), offering specific dashboards and features for each access level. The system incorporates essential features such as complex report generation, a proactive notification system, and automated backup routines, ensuring data integrity and availability.

## ✨ Key Features

The system is modularized to cover every educational management need:

* **👥 User Management:** Granular access control with three permission levels (Administrator, Principal, Teacher).
* **🏫 School and Municipality Management:** Centralized registration and administration of educational institutions and their locations.
* **👨‍🎓 Class Management:** Organization of classes linked to each school.
* **📂 Subject Management:** (Curriculum Components) Registration and association of taught subjects.
* **📖 Educational Resource Management:** Catalog of all pedagogical and technological resources available for scheduling (e.g., labs, projectors, robotics kits).
* **📅 Smart Scheduling:** Calendar interface (based on *FullCalendar*) for teachers to book resources for their classes, with availability validation.
* **📊 Advanced Reports:** Generation of dynamic reports on resource usage, scheduling by school, and more, with export to **PDF** and **Excel**.
* **🔔 Notification System:** Real-time in-app alerts and email notifications for critical actions (e.g., scheduling confirmations).
* **🗃️ Backup and Restore:** Robust functionality for creating application and database *backups*, with automatic scheduling and restoration.
* **♿ Accessibility:** Native integration with **VLibras** to ensure accessibility for people with disabilities.

---

## 🛠️ Requirements and Business Rules

The system logic was modeled to reflect the hierarchies and processes of a real educational environment.

### Core Business Rules

* 🔑 **User Approval:** Teachers and Principals can self-register, but their accounts are created with `pending` status. An `Administrator` must manually approve the registration before the user can access the system.
* 🚦 **Permission Hierarchy:**
    * **Administrator:** Has full control (CRUD) over all entities: Schools, Municipalities, Users, Classes, Resources, and Subjects. The only profile that can perform system backups and restorations.
    * **Principal:** Has control (CRUD) over entities *only* from their own school (Classes, Teachers, Resources, Subjects). Can view reports related to their school.
    * **Teacher:** The focus is on scheduling. Can schedule resources for their classes/subjects (Offerings) and manage (CRUD) the resources and subjects they themselves registered.
* 🌍 **Resource Ownership:** Resources and Subjects can be "Global" (belonging to the NRE, `school_id = null`) and available to all schools, or belong to a specific school (visible only to users of that school).
* ⏱️ **Scheduling Conflicts:** The system actively prevents the same resource (`recurso_didatico_id`) from being booked by two people for the same time slot (overlap validation of `data_inicio` and `data_fim`).
* 🔗 **Data Integrity:** The system uses foreign key constraints (`FOREIGN KEY`) to ensure referential integrity. A School cannot be deleted if it has linked Classes or Users; a Municipality cannot be deleted if it has linked Schools.

### Business Rules (BR)
<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #0169b4; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">ID</th>
        <th style="padding: 12px 15px; text-align: left;">Affected Actor(s)</th>
        <th style="padding: 12px 15px; text-align: left;">Rule Description</th>
        <th style="padding: 12px 15px; text-align: left;">Justification/Origin</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-001</td>
        <td style="padding: 12px 15px;">User (all)</td>
        <td style="padding: 12px 15px;">When a user updates their email in their profile, the account must be marked as "unverified", requiring re-confirmation.</td>
        <td style="padding: 12px 15px;">Ensure ownership and validity of the new email address.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-002</td>
        <td style="padding: 12px 15px;">User (all)</td>
        <td style="padding: 12px 15px;">To delete their own account, the user must confirm their current password.</td>
        <td style="padding: 12px 15px;">Security measure to prevent accidental or malicious deletion.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-003</td>
        <td style="padding: 12px 15px;">User (new)</td>
        <td style="padding: 12px 15px;">Registration fields (username, email, CPF, RG, etc.) must be unique in the system.</td>
        <td style="padding: 12px 15px;">Ensure the uniqueness of each user in the database.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-004</td>
        <td style="padding: 12px 15px;">Administrator</td>
        <td style="padding: 12px 15px;">Only administrators can view and manage users from all schools.</td>
        <td style="padding: 12px 15px;">Centralization of access control and account management at the NRE.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-005</td>
        <td style="padding: 12px 15px;">Principal, teacher</td>
        <td style="padding: 12px 15px;">Principals and teachers can only view users from their own school.</td>
        <td style="padding: 12px 15px;">Ensure data isolation (privacy) between institutions.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-006</td>
        <td style="padding: 12px 15px;">Principal</td>
        <td style="padding: 12px 15px;">Principals can only create users (e.g., teachers) for their own school.</td>
        <td style="padding: 12px 15px;">Delegation of personnel management at the school level.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-007</td>
        <td style="padding: 12px 15px;">Principal</td>
        <td style="padding: 12px 15px;">Principals cannot create or promote users to the "administrator" level.</td>
        <td style="padding: 12px 15px;">Maintain the permission hierarchy and system security.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-008</td>
        <td style="padding: 12px 15px;">Administrator</td>
        <td style="padding: 12px 15px;">An administrator (or any user) cannot delete their own account.</td>
        <td style="padding: 12px 15px;">Prevent accidental system lockout.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-009</td>
        <td style="padding: 12px 15px;">Administrator, principal</td>
        <td style="padding: 12px 15px;">The system must prevent the deletion of users who have dependencies (created resources or offerings).</td>
        <td style="padding: 12px 15px;">Ensure referential integrity and the history of actions.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-010</td>
        <td style="padding: 12px 15px;">Administrator</td>
        <td style="padding: 12px 15px;">Only Administrators can manage (CRUD) municipalities and schools.</td>
        <td style="padding: 12px 15px;">Centralization of NRE infrastructure management.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-011</td>
        <td style="padding: 12px 15px;">Administrator (when creating a school)</td>
        <td style="padding: 12px 15px;">A School must mandatorily be associated with a municipality.</td>
        <td style="padding: 12px 15px;">NRE structural organization requirement.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-012</td>
        <td style="padding: 12px 15px;">Administrator (when creating a school)</td>
        <td style="padding: 12px 15px;">The education level and type fields of a school must be predefined values (enum).</td>
        <td style="padding: 12px 15px;">Ensure data standardization and consistency for reports.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-013</td>
        <td style="padding: 12px 15px;">Principal, teacher</td>
        <td style="padding: 12px 15px;">Principals and teachers can only manage (view, create, edit) classes from their own school.</td>
        <td style="padding: 12px 15px;">Keep the management scope restricted to the institution itself.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-014</td>
        <td style="padding: 12px 15px;">User (when creating a class)</td>
        <td style="padding: 12px 15px;">The school year must be an integer within a valid range (e.g., 2000-2100).</td>
        <td style="padding: 12px 15px;">Ensure the validity and consistency of school year data.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-015</td>
        <td style="padding: 12px 15px;">User (when deleting a class)</td>
        <td style="padding: 12px 15px;">The system must prevent the deletion of classes that have component offerings.</td>
        <td style="padding: 12px 15px;">Protect the history of subject and teacher allocations.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-016</td>
        <td style="padding: 12px 15px;">Administrator, principal, teacher</td>
        <td style="padding: 12px 15px;">Subjects can be "global" or "specific" (linked to a school).</td>
        <td style="padding: 12px 15px;">Allow curriculum components common to all schools as well as unique components.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-017</td>
        <td style="padding: 12px 15px;">Administrator</td>
        <td style="padding: 12px 15px;">Only administrators can create or edit global subjects.</td>
        <td style="padding: 12px 15px;">Centralized control over the regional core curriculum.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-018</td>
        <td style="padding: 12px 15px;">Principal, teacher</td>
        <td style="padding: 12px 15px;">Principals and teachers view global subjects as well as those specific to their school.</td>
        <td style="padding: 12px 15px;">Provide access to the curriculum relevant to the institution.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-019</td>
        <td style="padding: 12px 15px;">User (when deleting a subject)</td>
        <td style="padding: 12px 15px;">The system must prevent the deletion of subjects that have linked offerings.</td>
        <td style="padding: 12px 15px;">Ensure the integrity of the class history.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-020</td>
        <td style="padding: 12px 15px;">Teacher</td>
        <td style="padding: 12px 15px;">Teachers can only create component offerings for themselves (not for other teachers).</td>
        <td style="padding: 12px 15px;">Ensure that teachers only manage their own assignments.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-021</td>
        <td style="padding: 12px 15px;">User (when deleting an offering)</td>
        <td style="padding: 12px 15px;">The system must prevent the deletion of offerings that have linked appointments.</td>
        <td style="padding: 12px 15px;">Protect the history of resource usage in appointments.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-022</td>
        <td style="padding: 12px 15px;">User (when creating a resource)</td>
        <td style="padding: 12px 15px;">The quantity of a resource must be an integer equal to or greater than 1.</td>
        <td style="padding: 12px 15px;">Ensure that the resource inventory has valid values.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-023</td>
        <td style="padding: 12px 15px;">User (when deleting a resource)</td>
        <td style="padding: 12px 15px;">The system must prevent the deletion of resources that have linked appointments.</td>
        <td style="padding: 12px 15px;">Ensure the integrity of the appointment history.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-024</td>
        <td style="padding: 12px 15px;">User (when creating an appointment)</td>
        <td style="padding: 12px 15px;">The end date/time of an appointment must mandatorily be after the start date/time.</td>
        <td style="padding: 12px 15px;">Ensure temporal logic and the validity of the scheduled period.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-025</td>
        <td style="padding: 12px 15px;">User (when creating an appointment)</td>
        <td style="padding: 12px 15px;">The start date/time must be at least 10 minutes ahead of the moment of creation.</td>
        <td style="padding: 12px 15px;">Avoid retroactive or instantaneous appointments that are impossible to fulfill.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-026</td>
        <td style="padding: 12px 15px;">System</td>
        <td style="padding: 12px 15px;">The system must not allow the same resource to be booked for overlapping (conflicting) time slots.</td>
        <td style="padding: 12px 15px;">Prevention of allocation conflicts (double booking).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-027</td>
        <td style="padding: 12px 15px;">System</td>
        <td style="padding: 12px 15px;">Appointments cannot be created at certain times (e.g., overnight, between 11:00 PM and 6:00 AM).</td>
        <td style="padding: 12px 15px;">Security restriction and compliance with operating hours.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-028</td>
        <td style="padding: 12px 15px;">User (when canceling an appointment)</td>
        <td style="padding: 12px 15px;">An appointment cannot be canceled less than 10 minutes before it starts.</td>
        <td style="padding: 12px 15px;">Avoid last-minute cancellations that disrupt resource allocation.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-029</td>
        <td style="padding: 12px 15px;">System</td>
        <td style="padding: 12px 15px;">Creating and canceling appointments must trigger notifications to everyone involved.</td>
        <td style="padding: 12px 15px;">Keep users informed about changes to the calendar.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-030</td>
        <td style="padding: 12px 15px;">Principal</td>
        <td style="padding: 12px 15px;">Reports generated by principals must contain only data from their own school.</td>
        <td style="padding: 12px 15px;">Ensure data isolation and privacy between institutions.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-031</td>
        <td style="padding: 12px 15px;">Administrator</td>
        <td style="padding: 12px 15px;">Only administrators can access the settings area (backups, etc.).</td>
        <td style="padding: 12px 15px;">Restrict access to critical system features.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-032</td>
        <td style="padding: 12px 15px;">System</td>
        <td style="padding: 12px 15px;">The system must notify the administrator by email when a backup completes successfully.</td>
        <td style="padding: 12px 15px;">Provide confirmation and monitoring of critical tasks.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-033</td>
        <td style="padding: 12px 15px;">Administrator</td>
        <td style="padding: 12px 15px;">The system must prevent the deletion of municipalities that have linked schools.</td>
        <td style="padding: 12px 15px;">Ensure the referential integrity of school locations.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-034</td>
        <td style="padding: 12px 15px;">Administrator</td>
        <td style="padding: 12px 15px;">The system must prevent the deletion of schools that have linked classes or users.</td>
        <td style="padding: 12px 15px;">Protect associated data (classes, users) of the institution.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-035</td>
        <td style="padding: 12px 15px;">System</td>
        <td style="padding: 12px 15px;">The system must prevent the creation of duplicate offerings (same subject, teacher, and class).</td>
        <td style="padding: 12px 15px;">Avoid redundancy and inconsistency in pedagogical data.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-036</td>
        <td style="padding: 12px 15px;">Administrator, principal, teacher</td>
        <td style="padding: 12px 15px;">Editing a subject is permitted only by its creator, the school principal, or an administrator.</td>
        <td style="padding: 12px 15px;">Control over who can change the data of a curriculum component.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-037</td>
        <td style="padding: 12px 15px;">Administrator</td>
        <td style="padding: 12px 15px;">Only administrators can change the school associated with a subject (or make it global).</td>
        <td style="padding: 12px 15px;">Centralized control over the regional curriculum structure.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-038</td>
        <td style="padding: 12px 15px;">System</td>
        <td style="padding: 12px 15px;">New subjects registered by teachers or principals start with "Pending" status.</td>
        <td style="padding: 12px 15px;">Ensure control and standardization of the component catalog.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-039</td>
        <td style="padding: 12px 15px;">User (when creating a resource)</td>
        <td style="padding: 12px 15px;">When registering a resource with a quantity greater than 1, the system must offer the option to create individual items or a single batch.</td>
        <td style="padding: 12px 15px;">Facilitate bulk inventory registration (usability).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-040</td>
        <td style="padding: 12px 15px;">System</td>
        <td style="padding: 12px 15px;">Newly registered users (Public Registration) start with "Pending" status and must be approved.</td>
        <td style="padding: 12px 15px;">Security measure to validate new users before granting access.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-041</td>
        <td style="padding: 12px 15px;">Administrator, principal</td>
        <td style="padding: 12px 15px;">Principals can only delete users (who are not administrators) from their own school.</td>
        <td style="padding: 12px 15px;">Maintain the permission hierarchy and management scope.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-042</td>
        <td style="padding: 12px 15px;">Administrator, principal, teacher</td>
        <td style="padding: 12px 15px;">An appointment can only be canceled by its creator (teacher), the school principal, or an administrator.</td>
        <td style="padding: 12px 15px;">Define responsibility over booking cancellations.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-043</td>
        <td style="padding: 12px 15px;">Administrator, principal</td>
        <td style="padding: 12px 15px;">Access to the reports module is restricted to administrators and principals.</td>
        <td style="padding: 12px 15px;">Protect access to analytical and consolidated data.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">BR-044</td>
        <td style="padding: 12px 15px;">Administrator</td>
        <td style="padding: 12px 15px;">Critical actions (running a backup, downloading a backup, restoring) require the administrator to confirm their current password.</td>
        <td style="padding: 12px 15px;">Security measure (step-up authentication) for sensitive operations.</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">BR-045</td>
        <td style="padding: 12px 15px;">User (when changing password)</td>
        <td style="padding: 12px 15px;">User passwords must be at least 16 characters long.</td>
        <td style="padding: 12px 15px;">Ensure a minimum level of complexity and security for passwords.</td>
      </tr>
    </tbody>
  </table>
</div>

### Functional Requirements (FR)
<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #0169b4; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">ID</th>
        <th style="padding: 12px 15px; text-align: left;">Module</th>
        <th style="padding: 12px 15px; text-align: left;">Requirement Name</th>
        <th style="padding: 12px 15px; text-align: left;">Description</th>
        <th style="padding: 12px 15px; text-align: left;">Priority</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-001</td>
        <td style="padding: 12px 15px;">Authentication</td>
        <td style="padding: 12px 15px;">User registration (public)</td>
        <td style="padding: 12px 15px;">The system must allow users (teachers, principals) to register through a public form.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-002</td>
        <td style="padding: 12px 15px;">Authentication</td>
        <td style="padding: 12px 15px;">User login</td>
        <td style="padding: 12px 15px;">The system must allow registered users to log in with email and password.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-003</td>
        <td style="padding: 12px 15px;">Authentication</td>
        <td style="padding: 12px 15px;">Password recovery</td>
        <td style="padding: 12px 15px;">The system must allow users to recover their passwords through a "Forgot my password" flow.</td>
        <td style="padding: 12px 15px;">High</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-004</td>
        <td style="padding: 12px 15px;">Profile</td>
        <td style="padding: 12px 15px;">Update profile information</td>
        <td style="padding: 12px 15px;">The user must be able to view and update their profile information (name, email, phone).</td>
        <td style="padding: 12px 15px;">Medium</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-005</td>
        <td style="padding: 12px 15px;">Profile</td>
        <td style="padding: 12px 15px;">Update password</td>
        <td style="padding: 12px 15px;">The user must be able to update their password by providing their current password.</td>
        <td style="padding: 12px 15px;">High</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-006</td>
        <td style="padding: 12px 15px;">Profile</td>
        <td style="padding: 12px 15px;">Delete account</td>
        <td style="padding: 12px 15px;">A user's account can be deleted by themselves or by an administrator/principal.</td>
        <td style="padding: 12px 15px;">Medium</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-007</td>
        <td style="padding: 12px 15px;">User management</td>
        <td style="padding: 12px 15px;">User CRUD</td>
        <td style="padding: 12px 15px;">The system must support CRUD operations for users.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-008</td>
        <td style="padding: 12px 15px;">User management</td>
        <td style="padding: 12px 15px;">Filter users</td>
        <td style="padding: 12px 15px;">The system must allow filtering the user list (by name, email, status, type, CPF, RG, education, etc.).</td>
        <td style="padding: 12px 15px;">High</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-009</td>
        <td style="padding: 12px 15px;">School management</td>
        <td style="padding: 12px 15px;">Municipality CRUD</td>
        <td style="padding: 12px 15px;">The system must support CRUD operations for municipalities.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-010</td>
        <td style="padding: 12px 15px;">School management</td>
        <td style="padding: 12px 15px;">School CRUD</td>
        <td style="padding: 12px 15px;">The system must support CRUD operations for schools.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-011</td>
        <td style="padding: 12px 15px;">Class management</td>
        <td style="padding: 12px 15px;">Class CRUD</td>
        <td style="padding: 12px 15px;">The system must support CRUD operations for classes.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-012</td>
        <td style="padding: 12px 15px;">Class management</td>
        <td style="padding: 12px 15px;">View class details (offerings)</td>
        <td style="padding: 12px 15px;">The system must allow viewing the details of a class and its offerings.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-013</td>
        <td style="padding: 12px 15px;">Subjects</td>
        <td style="padding: 12px 15px;">Curriculum component CRUD</td>
        <td style="padding: 12px 15px;">The system must support CRUD operations for curriculum components (subjects).</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-014</td>
        <td style="padding: 12px 15px;">Subjects</td>
        <td style="padding: 12px 15px;">Filter components</td>
        <td style="padding: 12px 15px;">The system must allow filtering components (name/description, workload, status, school).</td>
        <td style="padding: 12px 15px;">High</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-015</td>
        <td style="padding: 12px 15px;">Component offerings</td>
        <td style="padding: 12px 15px;">Offering CRUD</td>
        <td style="padding: 12px 15px;">The system must support CRUD operations for offerings.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-016</td>
        <td style="padding: 12px 15px;">Educational resources</td>
        <td style="padding: 12px 15px;">Educational resource CRUD</td>
        <td style="padding: 12px 15px;">The system must support CRUD operations for educational resources and labs.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-017</td>
        <td style="padding: 12px 15px;">Scheduling</td>
        <td style="padding: 12px 15px;">Manage scheduling</td>
        <td style="padding: 12px 15px;">The system must allow users to create, view, and cancel appointments for educational resources.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-018</td>
        <td style="padding: 12px 15px;">Scheduling</td>
        <td style="padding: 12px 15px;">Scheduling calendar</td>
        <td style="padding: 12px 15px;">The system must display appointments in an interactive calendar interface (FullCalendar).</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-019</td>
        <td style="padding: 12px 15px;">Reports</td>
        <td style="padding: 12px 15px;">View reports</td>
        <td style="padding: 12px 15px;">The system must allow previewing analytical reports with advanced filters and charts.</td>
        <td style="padding: 12px 15px;">High</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-020</td>
        <td style="padding: 12px 15px;">Reports</td>
        <td style="padding: 12px 15px;">Export reports</td>
        <td style="padding: 12px 15px;">The system must allow exporting reports in multiple formats (PDF, XLSX, CSV, ODS, HTML).</td>
        <td style="padding: 12px 15px;">High</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-021</td>
        <td style="padding: 12px 15px;">Settings</td>
        <td style="padding: 12px 15px;">Backup management</td>
        <td style="padding: 12px 15px;">The system must allow backup management (manual creation, download, and deletion).</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-022</td>
        <td style="padding: 12px 15px;">Settings</td>
        <td style="padding: 12px 15px;">Backup restoration</td>
        <td style="padding: 12px 15px;">The system must allow data restoration from a backup file.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-023</td>
        <td style="padding: 12px 15px;">Notifications</td>
        <td style="padding: 12px 15px;">Display notifications</td>
        <td style="padding: 12px 15px;">The system must display notifications to users (via the interface and email) about relevant events.</td>
        <td style="padding: 12px 15px;">High</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-024</td>
        <td style="padding: 12px 15px;">Notifications</td>
        <td style="padding: 12px 15px;">Mark notifications as read</td>
        <td style="padding: 12px 15px;">The system must mark notifications as read (automatically when the list is viewed).</td>
        <td style="padding: 12px 15px;">Medium</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-025</td>
        <td style="padding: 12px 15px;">User management</td>
        <td style="padding: 12px 15px;">User approval</td>
        <td style="padding: 12px 15px;">The system must allow administrators and principals to approve or reject/block new pending registrations.</td>
        <td style="padding: 12px 15px;">High</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-026</td>
        <td style="padding: 12px 15px;">Subjects</td>
        <td style="padding: 12px 15px;">Subject approval</td>
        <td style="padding: 12px 15px;">The system must allow authorized users (administrator, principal) to approve or reject components with "Pending" status.</td>
        <td style="padding: 12px 15px;">High</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-027</td>
        <td style="padding: 12px 15px;">Scheduling</td>
        <td style="padding: 12px 15px;">Check availability</td>
        <td style="padding: 12px 15px;">The system must display resource availability (available and booked) for a specific day.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-028</td>
        <td style="padding: 12px 15px;">Educational resources</td>
        <td style="padding: 12px 15px;">Batch registration</td>
        <td style="padding: 12px 15px;">The system must allow registering multiple individual resources from a single form (via a quantity checkbox).</td>
        <td style="padding: 12px 15px;">Medium</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-029</td>
        <td style="padding: 12px 15px;">Notifications</td>
        <td style="padding: 12px 15px;">Clear notifications</td>
        <td style="padding: 12px 15px;">The system must allow the user to delete notifications individually or clear the entire history.</td>
        <td style="padding: 12px 15px;">Medium</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">FR-030</td>
        <td style="padding: 12px 15px;">Scheduling</td>
        <td style="padding: 12px 15px;">Check daily availability</td>
        <td style="padding: 12px 15px;">The system must display resource availability (available and booked) for a specific selected day.</td>
        <td style="padding: 12px 15px;">Essential</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">FR-031</td>
        <td style="padding: 12px 15px;">Notifications</td>
        <td style="padding: 12px 15px;">Delete notifications</td>
        <td style="padding: 12px 15px;">The system must allow the user to delete notifications (individually or via "Clear All").</td>
        <td style="padding: 12px 15px;">Medium</td>
      </tr>
    </tbody>
  </table>
</div>

### Non-Functional Requirements (NFR)
<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #0169b4; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">ID</th>
        <th style="padding: 12px 15px; text-align: left;">Quality Attribute</th>
        <th style="padding: 12px 15px; text-align: left;">Requirement Description</th>
        <th style="padding: 12px 15px; text-align: left;">Verification Metric</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">NFR-001</td>
        <td style="padding: 12px 15px;">Security (access control)</td>
        <td style="padding: 12px 15px;">The system must have robust role-based access control (administrator, principal, teacher).</td>
        <td style="padding: 12px 15px;">Integration tests validating that each profile can only access allowed routes and data (HTTP 403 status tests).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">NFR-002</td>
        <td style="padding: 12px 15px;">Security (data)</td>
        <td style="padding: 12px 15px;">User passwords must be stored using strong, modern hashing (Argon2id).</td>
        <td style="padding: 12px 15px;">Code review and unit tests verifying that the hash is generated correctly.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">NFR-003</td>
        <td style="padding: 12px 15px;">Security (data)</td>
        <td style="padding: 12px 15px;">Sensitive personal data (such as CPF and RG) must be stored encrypted (e.g., AES-256-CBC).</td>
        <td style="padding: 12px 15px;">Implementation audit and manual database verification to confirm data is not stored in plain text.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">NFR-004</td>
        <td style="padding: 12px 15px;">Security (web)</td>
        <td style="padding: 12px 15px;">The system must be protected against common attacks (CSRF, XSS, SQL Injection).</td>
        <td style="padding: 12px 15px;">Code review (use of Eloquent ORM, Blade, middleware, CSRF) and basic penetration testing.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">NFR-005</td>
        <td style="padding: 12px 15px;">Integrity</td>
        <td style="padding: 12px 15px;">The system must ensure referential integrity, preventing the deletion of "parent" records that have "child" records.</td>
        <td style="padding: 12px 15px;">Integration tests (Feature Tests) that attempt to delete records with dependencies and validate the resulting error.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">NFR-006</td>
        <td style="padding: 12px 15px;">Reliability (backup)</td>
        <td style="padding: 12px 15px;">The system must provide mechanisms for (manual) backup and database restoration.</td>
        <td style="padding: 12px 15px;">Functional tests of the "Backup and Restore" interface. Verification that backup files are created on the server.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">NFR-007</td>
        <td style="padding: 12px 15px;">Maintainability (testability)</td>
        <td style="padding: 12px 15px;">The code must be testable, following unit and integration testing standards (PHPUnit).</td>
        <td style="padding: 12px 15px;">Running the test suite and checking code coverage.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">NFR-008</td>
        <td style="padding: 12px 15px;">Localization</td>
        <td style="padding: 12px 15px;">The system's primary language must be set to Portuguese (Brazil).</td>
        <td style="padding: 12px 15px;">Verification of language files and the user interface.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">NFR-009</td>
        <td style="padding: 12px 15px;">Platform (technology)</td>
        <td style="padding: 12px 15px;">The system must be built with the Laravel framework (PHP), MariaDB, and frontend tools such as Vite.js and Alpine.js.</td>
        <td style="padding: 12px 15px;">Verification of the project's configuration files (e.g., composer.json, package.json).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">NFR-010</td>
        <td style="padding: 12px 15px;">Performance (interface)</td>
        <td style="padding: 12px 15px;">The scheduling module must use AJAX (Axios) to load resource availability without reloading the page.</td>
        <td style="padding: 12px 15px;">Functional test of the calendar (clicking on a day) and verification that a request is made.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">NFR-011</td>
        <td style="padding: 12px 15px;">Usability (data visualization)</td>
        <td style="padding: 12px 15px;">The reports module must use charts (e.g., Chart.js) for easier interpretation.</td>
        <td style="padding: 12px 15px;">Functional test of the reports page and verification of the charts.</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">NFR-012</td>
        <td style="padding: 12px 15px;">Usability (interaction)</td>
        <td style="padding: 12px 15px;">The system must use modals (SweetAlert2) for destructive actions.</td>
        <td style="padding: 12px 15px;">Functional test and verification that the confirmation modal is displayed.</td>
      </tr>
    </tbody>
  </table>
</div>

---

## 💻 Development Environment

The project was developed with a modern set of tools, focused on security and productivity, in a hybrid environment.

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #444; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">Category</th>
        <th style="padding: 12px 15px; text-align: left;">Tool</th>
        <th style="padding: 12px 15px; text-align: left;">Version</th>
        <th style="padding: 12px 15px; text-align: left;">Purpose</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">Operating System</td>
        <td style="padding: 12px 15px;"><strong>Windows 11 + WSL 2 (Ubuntu)</strong></td>
        <td style="padding: 12px 15px;">-</td>
        <td style="padding: 12px 15px;">Hybrid development environment, combining the Windows UI with a native Linux terminal (WSL) for performance.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">Operating System</td>
        <td style="padding: 12px 15px;"><strong>Kali GNU/Linux Rolling</strong></td>
        <td style="padding: 12px 15px;">2025.3</td>
        <td style="padding: 12px 15px;">Used for security testing (pentesting) and validating the robustness of the application.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">Code Editor</td>
        <td style="padding: 12px 15px;"><strong>Visual Studio Code</strong></td>
        <td style="padding: 12px 15px;">1.103.1</td>
        <td style="padding: 12px 15px;">Main editor with extensions for PHP, Laravel, Blade, and Tailwind.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">Version Control</td>
        <td style="padding: 12px 15px;"><strong>Git</strong></td>
        <td style="padding: 12px 15px;">2.50.1</td>
        <td style="padding: 12px 15px;">Source code management and versioning.</td>
      </tr>
    </tbody>
  </table>
</div>

---

## 🚀 Tech Stack and Academic Rationale

The technology selection (the *stack*) for NREduTech was made deliberately, aiming to optimize performance, security, and development productivity.

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #444; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">Technology</th>
        <th style="padding: 12px 15px; text-align: left;">Version</th>
        <th style="padding: 12px 15px; text-align: left;">Why was it chosen? (Advantages over alternatives)</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>PHP</strong></td>
        <td style="padding: 12px 15px;">8.4.11</td>
        <td style="padding: 12px 15px;">
          <strong>Performance and Modernity:</strong> PHP 8.4 offers drastic performance improvements via the <strong>JIT (Just-In-Time)</strong> compiler. Its modern features (strict typing, Enums, Readonly Properties) make it more robust and less error-prone.<br>
          <strong>Advantage vs. Alternatives (Python/Node.js):</strong> PHP's ease of deployment (hosting) is unmatched. Its learning curve is faster than frameworks like Django (Python), and its multi-process model is simpler to manage for traditional web applications than Node.js's event loop.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Laravel</strong></td>
        <td style="padding: 12px 15px;">12.28.1</td>
        <td style="padding: 12px 15px;">
          <strong>"Batteries-Included" Ecosystem:</strong> Chosen for its complete ecosystem. The <strong>Eloquent ORM</strong> is considered more elegant and productive than Doctrine (Symfony) or TypeORM (Node.js). The <strong>Blade</strong> template engine is simple and extensible. Built-in tools such as `artisan` and task scheduling abstract away complexities that more "agnostic" frameworks would require to be implemented manually.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>MariaDB (Server/Client)</strong></td>
        <td style="padding: 12px 15px;">11.8.3 / 15.2</td>
        <td style="padding: 12px 15px;">
          <strong>Open-Source Performance:</strong> A community-maintained fork of MySQL focused on performance and openness. It offers full compatibility with MySQL (and Eloquent), but with performance optimizations (e.g., storage engines such as Aria) and a faster release cycle for new features. It is superior to MySQL in licensing and openness, and often outperforms MySQL on complex queries.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Tailwind CSS</strong></td>
        <td style="padding: 12px 15px;">3.x</td>
        <td style="padding: 12px 15px;">
          <strong>Productivity and Customization:</strong> Superior to component-based frameworks (such as Bootstrap). Instead of providing ready-made components (e.g., `.card`) that need to be overridden, Tailwind provides low-level utility classes. This makes it possible to build 100% custom, responsive designs without "fighting" predefined styles, resulting in a smaller final CSS bundle.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Vite.js</strong></td>
        <td style="padding: 12px 15px;">7.1.10</td>
        <td style="padding: 12px 15px;">
          <strong>Development Speed:</strong> Replaces Webpack/Mix. Its main advantage is near-instant <strong>Hot Module Replacement (HMR)</strong>. It uses ESBuild (written in Go) to pre-bundle dependencies, making builds and dev-server updates orders of magnitude faster than Webpack.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Node.js / NPM</strong></td>
        <td style="padding: 12px 15px;">20.19.2 / 9.2.0</td>
        <td style="padding: 12px 15px;">
          <strong>Frontend Ecosystem:</strong> The JavaScript runtime essential for the frontend build process (Vite, Tailwind). Version 20.x is the LTS (Long-Term Support) release, ensuring stability. NPM is used for frontend package management.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Composer</strong></td>
        <td style="padding: 12px 15px;">2.8.10</td>
        <td style="padding: 12px 15px;">
          <strong>PHP Dependency Manager:</strong> The de-facto standard, essential for managing Laravel's packages and their dependencies (Spatie, Maatwebsite, etc.).
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Maatwebsite/Excel</strong></td>
        <td style="padding: 12px 15px;">3.1</td>
        <td style="padding: 12px 15px;">
          <strong>Report Export:</strong> The Laravel community standard for data export. It abstracts away the complexity of PHPOffice/PhpSpreadsheet, allowing Blade views or Eloquent collections to be exported directly to XLSX, CSV, ODS, or PDF.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Spatie/laravel-backup</strong></td>
        <td style="padding: 12px 15px;">8.x</td>
        <td style="padding: 12px 15px;">
          <strong>Backup Reliability:</strong> A solution superior to manual cron scripts, as it handles the entire backup lifecycle: scheduling, running the DB dump, compression, email notification, and cleanup of old backups.
        </td>
      </tr>
    </tbody>
  </table>
</div>

---

## 🔒 Security and Encryption

Security is a core pillar of NREduTech, implementing modern standards for data protection.

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #444; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">Topic</th>
        <th style="padding: 12px 15px; text-align: left;">Implementation</th>
        <th style="padding: 12px 15px; text-align: left;">Justification (Why is it superior?)</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Password Hashing</strong></td>
        <td style="padding: 12px 15px;"><strong>Argon2id</strong> (via <code>config/hashing.php</code>)</td>
        <td style="padding: 12px 15px;">
          <strong>Resistance to Specialized Hardware:</strong> Argon2id is the winner of the <strong>Password Hashing Competition (2015)</strong> and the standard recommended by OWASP.
          <ul>
            <li><strong>Superior to Bcrypt:</strong> Bcrypt resists brute-force attacks but is vulnerable to specialized hardware (GPUs).</li>
            <li><strong>Superior to scrypt:</strong> scrypt pioneered being "memory-hard" (GPU-resistant), but Argon2id is more robust against a wider range of attacks.</li>
            <li><strong>Superior to Argon2d/2i:</strong> The <strong>Argon2id</strong> variant is hybrid, combining the GPU resistance of Argon2d with the side-channel attack resistance of Argon2i, making it the safest choice.</li>
          </ul>
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Session Encryption</strong></td>
        <td style="padding: 12px 15px;"><strong>AES-256-CBC</strong></td>
        <td style="padding: 12px 15px;">
          <strong>Industry Standard:</strong> Uses strong symmetric encryption to protect session data and "remember me" cookies. This prevents an attacker from reading or forging the contents of a user's session, since they do not have the secret key (<code>APP_KEY</code>) needed to decrypt the data.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Form Protection</strong></td>
        <td style="padding: 12px 15px;"><strong>CSRF Tokens</strong> (via <code>@csrf</code> and Middleware)</td>
        <td style="padding: 12px 15px;">
          <strong>Attack Prevention:</strong> Ensures that requests that modify data (<code>POST</code>, <code>PUT</code>, <code>DELETE</code>) can only originate from within the application itself. This prevents an external malicious site from tricking a logged-in user into performing unwanted actions (e.g., deleting an appointment).
        </td>
      </tr>
    </tbody>
  </table>
</div>

---

## 💡 Architecture Notes and Trivia

* **Decoupled Validation:** The project makes extensive use of *Form Requests* (e.g., `StoreUserRequest`, `StoreAppointmentRequest`). This is a Laravel *best practice* that moves all data validation logic out of the Controllers, making them cleaner, more readable, and easier to test.
* **Efficient Queries:** The Reports feature (`ReportController`) uses *Model Scopes* (e.g., `scopeFiltroRecursos`, `scopeFiltroUsuarios`) defined directly on the Models. This makes database queries dynamic, efficient, and reusable.
* **Production-Ready Seeders:** The project includes *seeders* such as `NreIratiSeeder`, which populate the database with real data (municipalities and schools from the Irati NRE), demonstrating a focus on practical deployment.
* **Development Time:**
    * **Start:** July 31, 2025
    * **Completion (v1.0):** November 26, 2025
    * **Total Hours (approx.):** 250 hours
    * **Total Elapsed Days:** 119 days

---

## 👨‍💻 Author

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9;">
    <tr>
      <td style="padding: 20px; width: 100px; text-align: center;">
        <img src="https://avatars.githubusercontent.com/u/142981329?v=4" width="90" alt="Victor's avatar" style="border-radius: 50%;">
      </td>
      <td style="padding: 20px; color: #333;">
        <strong style="font-size: 1.3em; color: #0169b4;">Victor Henrique Jesus Santiago</strong><br>
        Full Stack Developer<br><br>
        📧 <a href="mailto:victorhenriquedejesussantiago@gmail.com" style="color: #0169b4; text-decoration: none;">victorhenriquedejesussantiago@gmail.com</a><br>
        👔 <a href="https://www.linkedin.com/in/victor-henrique-de-jesus-santiago/" style="color: #0169b4; text-decoration: none;">LinkedIn/victorhjsantiago</a><br>
        🐙 <a href="https://github.com/victorhjsantiago" style="color: #0169b4; text-decoration: none;">GitHub/victorhjsantiago</a>
      </td>
    </tr>
  </table>
</div>
