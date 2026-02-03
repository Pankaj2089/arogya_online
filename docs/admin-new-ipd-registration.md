# Admin: New IPD Registration

## Overview

New IPD (In-Patient Department) registration at `/admin/new-ipd-registration`. Flow matches the reference screenshot: first **Enter OPD No.** (same as Re-Schedule OPD), then the **IPD Patient Registration** form using the current admin design.

## Flow

1. **First screen** – Enter OPD No.
   - Same layout and validation as `/admin/re-schedule-opd`.
   - Single field “Enter OPD No.” and Submit.
   - Validation: OPD number required (client-side + server-side); “OPD number not found” if invalid.

2. **Second screen** – IPD Registration form (after valid OPD)
   - Pre-filled from OPD: OPD No., Patient Name, Age/Gender, Category, Department (Unit: N/A).
   - Editable: Date, Time, Amount, Fath./Husb Name, Address, Diagnosis.
   - **Bed No.** – Dropdown from `bed_distributions` where `status = 1` and `bed_status = 'available'`.
   - **Admit by** – Dropdown from `users` (doctors: `type = 'Doctor'`, else all active users).
   - Ward, Ward-Type, and Room No. are **not** included.

3. **Footer** – Last IPD No., Patient Name, Category, Date/Time (when available).

## Excluded fields (as per requirement)

- Ward  
- Ward-Type  
- Room No.

## Data sources

- **Admit by**: `users` table – `type = 'Doctor'` and `status = 1`; if none, all `status = 1` users.
- **Bed No.**: `bed_distributions` – `status = true` and `bed_status = 'available'`, ordered by `bed_no`.

## Backend

- **Route**: `GET|POST /admin/new-ipd-registration` → `IpdController@newIpdRegistration`.
- **Tables**: `ipd_registration` (new), `opd_registration`, `users`, `bed_distributions`.
- **Migration**: `2025_02_02_100010_create_ipd_registration_table.php`.
- **Models**: `IpdRegistration`, `BedDistribution` (new); uses existing `OpdRegistration`, `User`.

## Files touched

- `app/Http/Controllers/Admin/IpdController.php` – OPD lookup, form data, save.
- `resources/views/admin/ipd/new-ipd-registration.blade.php` – Enter OPD + IPD form.
- `database/migrations/2025_02_02_100010_create_ipd_registration_table.php` – New table.
- `app/Models/IpdRegistration.php` – New model.
- `app/Models/BedDistribution.php` – New model.

Sidebar link and active state for “New IPD Registration” were already present in `resources/views/element/admin/sidebar.blade.php`.
