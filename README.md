# Hotel Stripes Kuala Lumpur — Enterprise Web & Admin System 🏨🛎️

[![Framework](https://img.shields.io/badge/Framework-Laravel%2010+-red.svg)](https://laravel.com/)
[![Language](https://img.shields.io/badge/Language-PHP%208.2+-blue.svg)](https://www.php.net/)
[![Design Pattern](https://img.shields.io/badge/Pattern-Template%20Method-yellowgreen.svg)]()
[![API](https://img.shields.io/badge/Architecture-RESTful%20JSON%20Services-green.svg)]()

A centralized hotel reservation, inventory, and administrative management platform engineered for Hotel Stripes Kuala Lumpur. 

The system transitions the hotel from third-party booking dependency to a sovereign web architecture, providing real-time room availability tracking, multi-room bookings, seasonal promo code discount systems, and an administrative reporting dashboard.

---

## 🏗️ Architectural Pattern: Template Method Pattern

The Administrative Subsystem relies on the **Template Method Pattern** (`CrudTemplate`) to establish an extensible, standardized lifecycle for recurring data persistence operations across Room Types, Physical Rooms, and Promotional Discount Codes.

```
                  ┌──────────────────────────────┐
                  │       CrudTemplate (Base)    │
                  ├──────────────────────────────┤
                  │ + process(data): Model       │
                  │ + processUpdate(data, id)    │
                  │ # validate(data): void       │
                  │ # save(data): Model          │
                  │ # validateUpdate(data, id)   │
                  │ # saveUpdate(data, id): Model│
                  └──────────────┬───────────────┘
                                 ▲
        ┌────────────────────────┼────────────────────────┐
        │                        │                        │
┌───────┴──────────┐   ┌─────────┴──────────┐   ┌─────────┴──────────┐
│     CrudRoom     │   │   CrudRoomType     │   │   CrudPromoCode    │
└──────────────────┘   └────────────────────┘   └────────────────────┘
```

* **Eliminates Code Duplication:** Concentrates generic execution flow in the base class while allowing subclasses to define entity-specific validation constraints and persistence steps.
* **Separation of Concerns:** Controllers remain thin by delegating complex business rules away from HTTP handling.

---

## 🛡️ Enterprise Security Implementations

* **Role-Based Access Control (RBAC):** Tiered route protection using dedicated middleware layers (`AdminMiddleware`, `SuperAdminMiddleware`) to prevent Insecure Direct Object References (IDOR).
* **File Upload Hardening:** Protects against malicious image execution via MIME type content verification (`image/jpeg`, `image/png`), extension whitelisting, and automated file pruning upon record updates via Laravel `Storage`.

---

## 🌐 Inter-Module Web Services (RESTful APIs)

The Admin Module exposes and consumes high-reliability RESTful JSON endpoints to coordinate data with Guest Booking and Payment subsystems:

| Endpoint | Method | Source $\rightarrow$ Target | Purpose |
| :--- | :--- | :--- | :--- |
| `/api/roomTypesToBook` | `POST` | Admin $\rightarrow$ Booking | Resolves room availability by date range, pax count, and active booking locks. |
| `/api/room/availableRoom` | `POST` | Admin $\rightarrow$ Booking | Fetches the next available physical room ID for cart checkout. |
| `/api/promo/{code}` | `GET` | Admin $\rightarrow$ Payment | Validates date ranges and discount parameters for promo code applications. |
| `/api/bookings` | `GET` | Booking $\rightarrow$ Admin | Ingests finalized booking records for administrative sales reporting. |

---

## 🚀 Setup & Installation

### Prerequisites
* PHP >= 8.2 with OpenSSL, PDO, Mbstring extensions
* Composer
* MySQL 8.0+

### Installation Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/Iz040622/Hotel-Booking-System.git
   cd Hotel-Booking-System
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install && npm run build
   ```
3. Configure your environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Configure database credentials in `.env` and run migrations:
   ```bash
   php artisan migrate --seed
   ```
5. Serve the application:
   ```bash
   php artisan serve
   ```

---

## 👨‍💻 Module Author
* **Isaac Lim Tian Le** — *Lead Developer: Admin Dashboard, Room & Promo Inventory Subsystems, Template Method Architecture, and RESTful APIs.*