# Changelog

All notable changes to the Bithoven Tickets extension will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.3] - 2025-11-27

### Removed - Code Sanitation

**REFACTOR:** Eliminated ~190 lines of dead code from ServiceProvider

#### Background
- Code audit revealed ServiceProvider contained hook-based permission management that never executed
- ExtensionManager doesn't implement static hook registration (`registerInstallHook()`, `registerUninstallHook()` don't exist)
- Permissions were never being assigned to roles during installation
- Permissions were not being cleaned up during uninstallation

#### Changes Made

**Removed from TicketsServiceProvider.php (~190 lines):**
- ❌ `registerExtensionHooks()` method
- ❌ `installPermissions()` method - Never executed
- ❌ `uninstallPermissions()` method - Never executed

**Created:**
- ✅ `TicketsPermissionsSeeder.php` - Proper permission installation with role-based assignment
  - Creates 8 permissions with alias/description
  - Assigns to 5 roles: super-admin (8), master-developer (8), administrator (8), support (4), user (2)
  - Role strategy designed for helpdesk/support system
- ✅ `TicketsUninstallSeeder.php` - Proper permission cleanup during uninstallation
  - Deletes role_has_permissions entries
  - Deletes permissions matching `extensions:tickets:%`
  - Clears permission cache

**Updated extension.json:**
```json
"seeders": {
  "core": [
    "TicketsPermissionsSeeder",  // NEW - First in list
    "CategorySeeder",
    "AutomationRulesSeeder",
    "TemplatesResponsesSeeder"
  ],
  "demo": ["TicketsDemoSeeder"],
  "uninstall": ["TicketsUninstallSeeder"]  // NEW
}
```

**Updated CPANEL Core:**
- `ExtensionSeederManager`: Added `runUninstall()` method
- `ExtensionUninstaller`: Now executes uninstall seeders before migration rollback

#### Role-Based Permission Strategy

| Role | Permissions | Purpose |
|------|-------------|---------|
| super-admin | ALL 8 | Full system management |
| master-developer | ALL 8 | Full system management |
| administrator | ALL 8 | Full system management |
| support | 4 (view, create, edit, assign) | Agent work - handle tickets |
| user | 2 (view, create) | Submit support requests |

#### Impact
- ✅ Permissions now properly installed during extension installation
- ✅ Permissions now properly cleaned up during uninstallation
- ✅ Role assignments working correctly (was broken before)
- ✅ Consistent seeder-based approach across all extensions
- ✅ No breaking changes - fixes existing bug

### Fixed
- **CRITICAL:** Permissions now assign to roles during installation (was completely broken)
- **CRITICAL:** Permissions now cleanup on uninstall (was leaving orphaned data)

---

## [1.1.0] - 2025-11-13

### Added
- ✅ **DataTables Migration:** All tables migrated to Yajra DataTables v10.x
  - Tickets, Templates, Responses, Automation Rules, Automation Logs
  - Server-side AJAX processing for performance
  - Metronic CSS styling with #009ef7 primary color
  - Advanced search, filtering, and sorting
- ✅ **Ticket Templates:** Pre-configured templates for common issues
- ✅ **Canned Responses:** Quick reply templates for agents
- ✅ **Automation Rules:** Automatic ticket routing and processing
- ✅ **Automation Logs:** Track automation execution and results
- ✅ **Email Notifications:** Complete async notification system
  - 5 notification types (created, assigned, comment, status, priority)
  - User-specific preferences via `/settings/notifications`
  - Queue support for background sending
  - Customizable email templates
- ✅ **Notification Preferences:** User settings for email notifications
- ✅ **Enhanced UI:** Sidebar menu link for notification settings
- ✅ **Active State Detection:** JavaScript support for showcase menu items

### Changed
- 🔄 **Pagination → DataTables:** Removed Laravel Pagination in favor of DataTables
- 🔄 **UI Consistency:** All tables now use uniform Metronic styling
- 🔄 **Performance:** Server-side processing for large datasets

### Technical
- DataTable classes in `src/DataTables/`
- Mail classes in `src/Mail/`
- Email views in `resources/views/emails/`
- Migration scripts in `scripts/migrate-to-datatables.sh`

## [1.0.0] - 2025-10-31

### Added
- ✅ Complete ticket management system
- ✅ Ticket categories with colors and icons
- ✅ Comments system with internal notes
- ✅ File attachments support
- ✅ Ticket assignment to agents
- ✅ Status tracking (Open, In Progress, Pending, Resolved, Closed)
- ✅ Priority levels (Low, Medium, High, Urgent)
- ✅ Service Provider with Laravel auto-discovery
- ✅ Policy-based authorization
- ✅ Form request validation
- ✅ Event system (TicketCreated, TicketAssigned, TicketResolved)
- ✅ Notification service (database, mail, slack channels)
- ✅ Assignment service with auto-assignment
- ✅ CLI command to close stale tickets
- ✅ RESTful API endpoints
- ✅ Metronic-styled Blade views
- ✅ Statistics dashboard
- ✅ Advanced filtering and search
- ✅ Soft deletes
- ✅ Timestamps tracking (created, updated, resolved, closed, first response)
- ✅ SLA configuration support
- ✅ Extensive configuration options
- ✅ Database seeder with default categories
- ✅ English translations
- ✅ Comprehensive documentation

### Features
- **Ticket Management:** Full CRUD with filtering, search, and pagination
- **Categorization:** Custom categories with colors and icons
- **Assignment:** Manual and auto-assignment to agents
- **Comments:** Public and internal comments, mark as solution
- **Attachments:** Upload multiple files with size/type restrictions
- **Notifications:** Multi-channel notification system
- **Statistics:** Real-time ticket statistics and metrics
- **API:** RESTful API for third-party integrations
- **Permissions:** Role-based access control with Spatie Permission
- **Events:** Event-driven architecture for extensibility
- **CLI:** Artisan commands for automation
- **UI:** Modern Metronic-styled interface

### Technical
- Laravel 11 compatible
- PHP 8.1+ required
- Bithoven Core 1.4.0+ required
- Auto-discovery service provider
- PSR-4 autoloading
- Comprehensive test coverage ready
- Database connection configurable (main or separate)
- Extensible via events and service injection

### Documentation
- Complete README with installation instructions
- Configuration guide
- API documentation
- Development guide
- MIT License

---

## [Unreleased]

### Planned for v1.2.0
- Bulk actions (assign, close, delete)
- Export tickets to CSV/PDF
- SLA breach alerts
- Customer satisfaction ratings
- Ticket merge functionality
- Advanced analytics dashboard
- Custom fields support

### Planned for v1.3.0
- Knowledge base integration
- Slack webhook integration
- Multi-language support (ES, FR, DE)
- Time tracking per ticket
- Agent performance metrics

### Planned for v2.0.0
- Multi-tenant support
- Live chat integration
- AI-powered ticket routing
- Customer portal
- Mobile app support

---

[1.1.0]: https://github.com/bithoven/tickets/releases/tag/v1.1.0
[1.0.0]: https://github.com/bithoven/tickets/releases/tag/v1.0.0
