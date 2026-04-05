# Render Environment Variables Setup Guide

## Problem Identified
All links in the application are redirecting to `http://localhost:8000` instead of your Render production URL. This is because the `SITE_URL` environment variable is not set on the Render dashboard.

## Root Cause
In `includes/config.php` (line 13):
```php
define('SITE_URL',  getenv('SITE_URL') ?: 'http://localhost:8000');
```

This means:
- ✅ If `SITE_URL` environment variable is set → Use that value
- ❌ If `SITE_URL` is NOT set → Default to `http://localhost:8000` (CURRENT ISSUE)

## Solution: Set Environment Variables on Render

### Step 1: Go to Render Dashboard
1. Visit https://dashboard.render.com
2. Click on your web service: **fspo-ecommerce**
3. Go to the **Settings** tab
4. Scroll to **Environment** section

### Step 2: Add/Update Environment Variables
Click **Add Environment Variable** and set these values:

#### Required Variables:

| Variable | Value | Purpose |
|----------|-------|---------|
| `SITE_URL` | `https://fspo-ecommerce.onrender.com` | Base URL for all application links |
| `DB_DRIVER` | `pgsql` | Database driver (PostgreSQL) |
| `DB_HOST` | `dpg-d791braa214c73aids1g-a.oregon-postgres.render.com` | PostgreSQL host |
| `DB_PORT` | `5432` | PostgreSQL port |
| `DB_NAME` | `fspo_db_snv4` | Database name |
| `DB_USER` | `fspo_db_snv4_user` | Database user |
| `DB_PASSWORD` | `[YOUR_PASSWORD_HERE]` | Database password from Render |

### Step 3: Deploy Changes
1. After adding all variables, click **Save Changes**
2. Render will automatically redeploy your web service
3. Wait 2-3 minutes for deployment to complete

### Step 4: Verify Fix
Visit https://fspo-ecommerce.onrender.com and:
- ✅ Check that CSS is loading (page has styling)
- ✅ Click a link and verify it goes to `https://fspo-ecommerce.onrender.com/...` (NOT localhost)
- ✅ Navigate through pages
- ✅ Test login/add to cart/admin panel

## How Environment Variables Work

1. **On Render** (Production):
   - Environment variables are set in the Render dashboard
   - PHP reads them with `getenv('VARIABLE_NAME')`
   - Application uses production URLs

2. **On Local Machine** (Development):
   - Create `.env` file with local values OR
   - Variables aren't set, so defaults are used (`localhost:8000`)
   - Application uses local development URLs

## Current Application Configuration

Your `config.php` supports:
- ✅ PostgreSQL (via `DB_DRIVER = 'pgsql'`)
- ✅ TCP connection to remote database
- ✅ Environment variable reading
- ✅ Fallback to defaults for local development

## After Setting Environment Variables

All these links will automatically work:
```php
// In includes/header.php
<a href="<?= SITE_URL ?>/index.php">Home</a>
<link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">

// In includes/footer.php  
<form action="<?= SITE_URL ?>/newsletter.php">

// In login.php
header('Location: ' . SITE_URL . '/client/dashboard.php');

// And all other redirects throughout the application
```

## Troubleshooting

**Issue: CSS still not loading after setting SITE_URL**
- Solution: Wait 5 minutes for Render to fully rebuild and cache to clear
- Check browser DevTools (F12) → Network tab → look for 404 errors
- If CSS shows 404, check that `/css/style.css` file exists in repository

**Issue: Environment variables not showing in logs**
- Solution: Check Render logs to verify deployment succeeded
- Wait for "Build succeeded" message before testing

**Issue: Application still showing localhost**
- Solution: Verify `SITE_URL` was saved correctly on Render dashboard
- Check that you clicked "Save Changes" and waited for redeploy
- Clear browser cache (Ctrl+Shift+Delete) and reload

## Reference

**Your Application URLs:**
- 🌐 Live Site: https://fspo-ecommerce.onrender.com
- 📊 Database: dpg-d791braa214c73aids1g-a.oregon-postgres.render.com:5432
- 🔌 Repository: https://github.com/Maxwell-cod/fspo-ecommerce
- 📋 Render Dashboard: https://dashboard.render.com

**File References:**
- Configuration: `includes/config.php` (lines 1-20)
- Header: `includes/header.php` (uses SITE_URL)
- Footer: `includes/footer.php` (uses SITE_URL)
- Database: All configs use environment variables
