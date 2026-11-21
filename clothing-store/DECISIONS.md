# Decisions Log

## 2026-11-21 - Phase 0 Bootstrap

### 1. Laravel Version
**Decision:** Use Laravel 12 with PHP 8.2 (system has 8.2.12, docs specify 8.3)
**Reasoning:** Laravel 12 supports PHP 8.2+. The docs specify PHP 8.3 but the environment has 8.2.12. Package versions were adjusted to work with PHP 8.2.

### 2. Pest Version
**Decision:** Use Pest 3.8.2 with pest-plugin-laravel 3.2
**Reasoning:** Pest v4+ requires PHP 8.3+. Pest 3.x works with PHP 8.2 and Laravel 12.

### 3. Filament Version
**Decision:** Use Filament v5.11 (installed as filament/filament ^5.11) instead of documented v4
**Reasoning:** Filament v4 requires PHP 8.3+ and Livewire ^3.5. Livewire v3.5 requires Laravel ^10|^11 (not ^12). Since we use Laravel 12, Filament v4 is incompatible. Filament v5 works with Livewire 4.x and PHP 8.2+.
**Docs updated:** docs/03-TECH-STACK.md, docs/07-ADMIN-PANEL.md

### 4. Livewire Version
**Decision:** Use Livewire v4.4.9 instead of documented v3
**Reasoning:** Livewire v3.x requires Laravel ^10|^11. Livewire v4.x supports Laravel 12. Compatible with Filament v5 and PHP 8.2.
**Docs updated:** docs/03-TECH-STACK.md

### 5. Spatie Package Versions
**Decision:** Use compatible versions for PHP 8.2:
- spatie/laravel-medialibrary ^11.23
- spatie/laravel-permission ^6.25
- spatie/laravel-activitylog ^4.12
- spatie/laravel-sluggable ^3.8
**Reasoning:** Latest versions require PHP 8.3+ or 8.4+.

### 6. Font Strategy
**Decision:** Self-host Hind Siliguri (Bangla) and Inter (Latin) fonts via @font-face in CSS
**Reasoning:** Better performance, no external dependencies, works offline. Font files need to be added to public/fonts/.

### 7. Layout Structure
**Decision:** Use Blade layout with $slot fallback for Laravel 12 compatibility
**Reasoning:** Laravel 12 uses anonymous components with $slot. Added fallback to prevent errors.

### 8. Settings Table
**Decision:** Key-value settings table with typed values (string, int, bool, json) cached via SettingsService
**Reasoning:** Matches docs specification for admin-editable settings.

### 9. Currency Handling
**Decision:** Money stored as unsigned integer (BDT paisa/taka), formatted via Money helper
**Reasoning:** Avoids floating-point precision issues.

### 10. Phone Normalization
**Decision:** PhoneNormalizer handles +880, 880, 01, Bengali digits formats
**Reasoning:** Bangladesh phone numbers come in various formats.

### 11. Testing Strategy
**Decision:** Pest for feature/unit tests, PHPUnit for browser tests (if needed)
**Reasoning:** Pest provides cleaner syntax, works with Laravel 12.

### 12. CI/CD
**Decision:** GitHub Actions with Pint + Pest (configured at .github/workflows/ci.yml)
**Reasoning:** Standard Laravel CI pipeline.

### 13. Admin User Seeding
**Decision:** AdminUserSeeder reads ADMIN_EMAIL/ADMIN_PASSWORD from .env; refuses to seed with default/empty password in production
**Reasoning:** Security - no hardcoded credentials. Owner must provide real credentials via .env.

### 14. Locale & Timezone
**Decision:** APP_LOCALE=bn, APP_FALLBACK_LOCALE=en, APP_TIMEZONE=Asia/Dhaka in .env.example
**Reasoning:** Bangladesh market requires Bangla as primary language.

### 15. Phase 0 Test Coverage
**Decision:** 46 tests (106 assertions) covering: PhoneNormalizer (17 cases), Money (7 cases), Enums (7 cases), SettingsService (11 cases), Feature tests (6 cases)
**Reasoning:** Comprehensive unit and feature test coverage for core utilities before Phase 1.

---

*Decisions will be updated as the project progresses through phases.*