# System Identity & Access Architecture Context (RBAC & ABAC)

This document serves as the persistent context for the system's identity, relational architecture, and naming conventions for Role-Based Access Control (RBAC) and Attribute-Based Access Control (ABAC).

## 1. Core Structural Relations
The entity relationships follow a highly structured organizational path:
* **User 1-to-1 with Staff:** Linked through an `Agreement` of type `Staff Contract`.
  * **Backend Schema:** `party_a = OrgCorp`, `party_b = User`, `scope = Staff`
* **Staff 1-to-1 with JobPosition:** Linked through an `Agreement` of type `Job Contract`.
  * **Backend Schema:** `party_a = OrgUnit`, `party_b = Staff`, `scope = JobPosition`
* **JobPosition Many-to-Many with JobRole:** Forms the foundational static structural layer.
* **PersonAttributes Polymorphic to (User | Staff | JobPosition):** The foundation for dynamic, attribute-driven rule matching.

## 2. Access Control Types
Roles within the system are explicitly built from two distinct paradigms:
* **RBAC (Role-Based Access Control):** Driven directly by structural mappings via `JobRole`.
* **ABAC (Attribute-Based Access Control):** Driven dynamically via polymorphic `PersonAttributes`.

## 3. Naming Conventions & Encoding
Both naming conventions use specific UTF-8 geometric diamond symbols to ensure system-wide uniqueness, visual scannability, and safe processing across Laravel Filament and UTF-8 (`utf8mb4`) databases.

### ABAC Convention (Hollow Diamond)
* **Symbol:** White Diamond `◇` (U+25C7)
* **Format Structure:** `<tablename of morphed>◇<key>◇<value>`
* **Examples:**
  * `users◇partnership◇<org_corps.nick>` (e.g., `users◇partnership◇ACME`)
  * `staff◇gender◇<m or f>` (e.g., `staff◇gender◇m`)

### RBAC Convention (Solid Diamond)
* **Symbol:** Black Diamond `◆` (U+25C6)
* **Format Structure:** `job_roles◆<tablename>◆<value>`
* **Examples:**
  * `job_roles◆<org_units.nick>◆head` (e.g., `job_roles◆FINANCE◆head`)
  * `job_roles◆<org_teams.nick>◆handler` (e.g., `job_roles◆SUPPORT_A◆handler`)
  * `job_roles◆global◆PRODUCTION_OPERATOR`

---

## 4. Implementation Implementations (Laravel Reference)

### Model Accessor (`Role.php`)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Role extends Model
{
    protected function dynamicName(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (\$this->type === 'ABAC') {
                    return "{\$this->target_table}◇{this->key◇this->value}";
                }
                return "job_roles◆{\(this->target_table}◆{\)this->value}";
            }
        );
    }
}
```

### Parsing Middleware / Policy Helpers
```php
// Parsing an ABAC identity string
[table, key, \(value] = explode('◇',\)roleString);

// Parsing an RBAC identity string
[prefix, table, \(value] = explode('◆',\)roleString);
```
