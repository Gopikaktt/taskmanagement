# 🚀 Task Management System (Web & RESTful API)

A robust, enterprise-ready **Task Management System** built with **Laravel 12 (Latest Stable Version)**, featuring a modern web interface, comprehensive RESTful API endpoints, distinct **Multi-Guard Authentication** (Administrators & Staff Members), **Asynchronous Queued Background Notifications**, and strict **Account Eligibility & Verification Security Checks**.

---

## 📑 Table of Contents
1. [Core Features & Principles](#-core-features--principles)
2. [Default Login Credentials](#-default-login-credentials)
3. [Technology Stack & Architectural Principles](#-technology-stack--architectural-principles)
4. [Step-by-Step Installation Guide](#-step-by-step-installation-guide)
5. [Running Queues & Background Workers](#-running-queues--background-workers)
6. [Automated Testing Suite](#-automated-testing-suite)
7. [RESTful API Endpoints Reference](#-restful-api-endpoints-reference)
8. [Postman Collection Import](#-postman-collection-import)

---

## 🌟 Core Features & Principles

### 1. Multi-Guard Authentication (Web & API)
- **Web Sessions**: Built using distinct Laravel session guards:
  - `auth:admin` – Protects `/admin/*` routes using the `admins` provider and `Admin` model.
  - `auth:staff` – Protects `/staff/*` routes using the `staff` provider and `Staff` model.
- **RESTful API**: Token-based authentication via **Laravel Sanctum**. Tokens are polymorphic and scoped to user types (`Admin` or `Staff`) with role abilities (`role:admin`, `role:staff`).
- Separate login portals:
  - **Admin Login:** `/admin/login`
  - **Staff Login:** `/staff/login`
  - **Landing Hub:** `/`

### 2. Task Assignment & Automated Background Notifications
- When an Administrator creates or re-assigns a task to a staff member, the system dispatches an asynchronous queued job: `SendTaskAssignedNotification`.
- Runs via Laravel's **Queue Worker** without blocking the HTTP request/response cycle.
- Generates a styled responsive HTML email notification (`TaskAssignedMail`) with direct action links to the staff portal.

### 3. Account Status Verification & Security Checks
- **Middleware:** `EnsureStaffIsActiveAndVerified`
- Enforces security checks before staff members can view or update task details:
  - **Account Active Check (`is_active`):** Inactive staff are prevented from logging in and accessing tasks.
  - **Email Verified Check (`email_verified_at`):** Unverified staff cannot access task operations.
  - Administrators can toggle active and verification status in 1 click from the Staff Management console.

### 4. Admin Management Capabilities
- **Project Tracking:** Full CRUD for projects with progress bar calculation (`getProgressAttribute`), task counters, and deadline alerts.
- **Staff Management:** View personnel, filter by department/status, toggle active status, and view workload distribution.
- **Task Scheduling:** Schedule tasks, set priorities (`urgent`, `high`, `medium`, `low`), assign staff, and track completion timestamps.

### 5. Staff Portal Capabilities
- Filter assigned tasks by status (`pending`, `in_progress`, `completed`, `cancelled`) and priority.
- Instant 1-click status updater that automatically tracks `completed_at` timestamps.
- Strict ownership check: Staff can only access tasks assigned specifically to them.

---

## 🔑 Default Login Credentials

All accounts are automatically generated via database seeders:

| Role | Email | Password | Status / Notes |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin@example.com` | `password123` | Full Administrative Privileges |
| **Administrator (Alex)** | `alex.admin@example.com` | `password123` | Secondary Admin Account |
| **Active Staff (John Doe)** | `staff@example.com` | `password123` | Software Engineering (Active & Verified) |
| **Active Staff (Sarah Smith)** | `sarah.smith@example.com` | `password123` | UI/UX Design (Active & Verified) |
| **Active Staff (David Lee)** | `david.lee@example.com` | `password123` | QA & Testing (Active & Verified) |
| **Inactive Staff (Test)** | `inactive.staff@example.com` | `password123` | *Blocked by `EnsureStaffIsActiveAndVerified`* |
| **Unverified Staff (Test)** | `unverified.staff@example.com` | `password123` | *Blocked by verification check* |

---

## 🛠 Technology Stack & Architectural Principles

- **Framework:** Laravel 12.x
- **Language:** PHP 8.2+
- **Database:** MySQL
- **Authentication:** Laravel Multi-Auth Session Guards + Laravel Sanctum API Tokens
- **Asynchronous Queue:** Database Queue Driver (`QUEUE_CONNECTION=database`)
- **UI & Styling:** Custom Vanilla CSS Design System with a modern, high-contrast light SaaS theme, responsive KPI cards, interactive auto-dismissing toasts, and typography (`Plus Jakarta Sans`).
- **Icons:** FontAwesome 6
- **Architecture Patterns:**
  - **Eloquent Relationships:** `hasMany`, `belongsTo`, and polymorphic tokenables
  - **Form Requests:** Dedicated validation classes for Admin/Staff login, projects, staff, and tasks
  - **API Resources:** `JsonResource` transformers for clean API contract responses
  - **Queued Jobs:** `SendTaskAssignedNotification` implementing `ShouldQueue`
  - **Mailables:** `TaskAssignedMail` with Blade template

---

## 💻 Step-by-Step Installation Guide

### Prerequisites
- PHP >= 8.2 with PDO, OpenSSL, Mbstring, Tokenizer, XML, and Ctype extensions
- Composer
- MySQL / MariaDB (e.g. XAMPP)

### Setup Steps

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd task-management-system
   ```

2. **Install Composer dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Database in `.env`:**
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task-management
   DB_USERNAME=root
   DB_PASSWORD=

   QUEUE_CONNECTION=database
   MAIL_MAILER=log
   ```

5. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Start the Development Server:**
   ```bash
   php artisan serve
   ```
   Access the web interface at: **[http://localhost:8000](http://localhost:8000)**

---

## 📬 Running Queues & Background Workers

The system queues email notifications in the database when a task is assigned to a staff member.

To process background notification jobs:
```bash
php artisan queue:work
```
*(Or use `php artisan queue:listen --tries=1` during development).*

Sent notification emails are logged in `storage/logs/laravel.log` (when `MAIL_MAILER=log`).

---

## 🧪 Automated Testing Suite

Comprehensive feature and unit tests are included covering multi-auth guards, Sanctum API tokens, account eligibility middleware, project CRUD, and queued notification dispatching:

Run the tests using PHPUnit / Artisan:
```bash
php artisan test
```

### Test Coverage Highlights:
- `AdminAuthTest`: Admin session login, invalid credentials check, API Sanctum token issue & revocation.
- `StaffAuthAndSecurityTest`: Active/verified login, inactive account rejection (403), unverified email rejection, mid-session deactivation enforcement.
- `TaskAssignmentAndNotificationTest`: Task assignment job queued (`Queue::fake()`), staff status update, staff task ownership isolation.
- `ProjectManagementTest`: Project creation, staff permission restriction checks.

---

## 📡 RESTful API Endpoints Reference

All API routes are prefixed with `/api/v1` and return JSON.

### 1. Authentication
| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/admin/login` | Admin login & Sanctum token generation | Public |
| `POST` | `/api/v1/staff/login` | Staff login & Sanctum token generation | Public |
| `GET` | `/api/v1/me` | Retrieve authenticated user profile | Bearer Token |
| `POST` | `/api/v1/logout` | Revoke current access token | Bearer Token |

### 2. Administrator Endpoints (Requires `admin` token)
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/v1/admin/stats` | Executive KPI analytics & summary numbers |
| `GET` | `/api/v1/admin/projects` | List projects (with search & status filter) |
| `POST` | `/api/v1/admin/projects` | Create new project |
| `GET` | `/api/v1/admin/projects/{id}` | Get project details & associated tasks |
| `PUT` | `/api/v1/admin/projects/{id}` | Update project |
| `DELETE` | `/api/v1/admin/projects/{id}` | Delete project |
| `GET` | `/api/v1/admin/staff` | List staff members (with search & filters) |
| `POST` | `/api/v1/admin/staff` | Create new staff member |
| `GET` | `/api/v1/admin/staff/{id}` | Get staff details & assigned tasks |
| `PUT` | `/api/v1/admin/staff/{id}` | Update staff profile |
| `DELETE` | `/api/v1/admin/staff/{id}` | Delete staff member |
| `PATCH` | `/api/v1/admin/staff/{id}/toggle-status` | Toggle active/deactivated status |
| `PATCH` | `/api/v1/admin/staff/{id}/toggle-verification` | Toggle email verified status |
| `GET` | `/api/v1/admin/tasks` | List all tasks (with filters) |
| `POST` | `/api/v1/admin/tasks` | Create task & **dispatch queued notification** |
| `GET` | `/api/v1/admin/tasks/{id}` | Get task details |
| `PUT` | `/api/v1/admin/tasks/{id}` | Update task / reassign staff |
| `DELETE` | `/api/v1/admin/tasks/{id}` | Delete task |

### 3. Staff Member Endpoints (Requires `staff` token & Active/Verified status)
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/v1/staff/stats` | Staff assigned tasks summary counts |
| `GET` | `/api/v1/staff/tasks` | List tasks assigned to authenticated staff |
| `GET` | `/api/v1/staff/tasks/{id}` | View assigned task details (ownership verified) |
| `PATCH` | `/api/v1/staff/tasks/{id}/status` | Update task status (`in_progress`, `completed`, etc.) |

---

## 📦 Postman Collection Import

A complete Postman Collection file is provided in the repository root:
**[`postman_collection.json`](postman_collection.json)**

### How to use:
1. Open Postman &rarr; Click **Import** &rarr; Select `postman_collection.json`.
2. The collection includes pre-configured variables (`base_url`, `admin_token`, `staff_token`).
3. Running `1. Admin Login` or `2. Staff Login` automatically saves the Bearer token into collection variables for seamless execution of all endpoints!

---

## 🏆 Summary Checklist of Requirements Met
- [x] Web dashboard along with RESTful APIs
- [x] Separate logins for Admin and Staff with distinct session guards
- [x] Multi-guard Sanctum token authentication for API
- [x] Admins can manage projects and staff, and create/assign tasks
- [x] Staff can view and update the status of their assigned tasks
- [x] Automatic background notification (queued email job) when task is assigned
- [x] Account check to ensure only active and verified staff can access tasks
- [x] Middlewares (`EnsureStaffIsActiveAndVerified`, `EnsureAdmin`, `EnsureStaff`)
- [x] Validations (Dedicated Form Requests)
- [x] Database Migrations with foreign keys and cascade rules
- [x] Seeders & Factories with default Admin and Staff accounts
- [x] Eloquent ORM & Relationships
- [x] Postman Collection export included (`postman_collection.json`)
- [x] 100% passing automated test suite (22 tests, 89 assertions)
