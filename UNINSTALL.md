# Uninstallation Guide

## Options

### Complete Removal
```bash
php artisan bithoven:extension:uninstall tickets
```
Removes: Code + Views + Config + Database

### Keep Published Views
```bash
php artisan bithoven:extension:uninstall tickets --keep-views
```
Keeps: Views and Config (for customizations)

### Keep Database
```bash
php artisan bithoven:extension:uninstall tickets --keep-data
```
Keeps: All tickets and data

### Keep Both
```bash
php artisan bithoven:extension:uninstall tickets --keep-views --keep-data
```

## What Gets Removed

| Item | Default | --keep-views | --keep-data |
|------|---------|--------------|-------------|
| vendor/bithoven/tickets/ | Yes | Yes | Yes |
| resources/views/extensions/tickets/ | Yes | No | Yes |
| config/tickets.php | Yes | No | Yes |
| Database tables | Yes | Yes | No |

## Confirmation

The command shows a summary before proceeding:
```
⚠️  Uninstalling extension: tickets
Are you sure you want to continue? (yes/no)
```
