# 🚨 SERVICE RECOVERY GUIDE - Render "Service Temporarily Unavailable"

## Problem
You're seeing: **"Service Temporarily Unavailable - We're experiencing technical difficulties"**

This happens when:
- ❌ Web service is DOWN or CRASHING
- ❌ Database connection lost
- ❌ PHP errors preventing service startup
- ❌ Memory exhausted on free tier
- ❌ Recent deployment failed

---

## Quick Fix (5 Minutes)

### Step 1: Check Render Dashboard
1. Go to: https://dashboard.render.com
2. Look at your **fspo-ecommerce** web service
3. Check the status:
   - 🟢 "Live" = Service is running
   - 🟡 "Deploying" = Wait a few minutes
   - 🔴 "Failed" = Restart needed

### Step 2: Restart the Service
1. Click your web service **fspo-ecommerce**
2. Scroll to bottom → **Settings** section
3. Look for **"Danger Zone"** (red section)
4. Click **"Restart"** button
5. Wait 1-2 minutes for restart

### Step 3: Test the Site
1. Go to: https://fspo-ecommerce.onrender.com
2. Page should load normally
3. Try login/register - should work now

**If not working, continue to Step 4...**

### Step 4: Check Service Logs
1. Click your web service
2. Go to **"Logs"** tab
3. Scroll to the bottom
4. Look for error messages
5. Common errors:
   - `Connection refused` = Database unreachable
   - `Fatal error` = PHP code error
   - `Out of memory` = Render needs restart
   - `Port already in use` = Service conflict

---

## Detailed Troubleshooting

### Issue 1: Database Connection Error

**Symptoms:**
- Login page loads
- But clicking "Sign In" gives error
- Or registration doesn't work

**Root Cause:**
- PostgreSQL database is unreachable
- Network connection lost
- Database credentials are wrong

**Fix:**
1. Check PostgreSQL on Render:
   - Dashboard → PostgreSQL service
   - Status should be "Active" (green)
   - If not, click "Restart"

2. Verify connection string:
   - File: `includes/config.php`
   - Check if credentials match Render PostgreSQL settings

3. Test connection:
   - Create test file: `test-connection.php`
   ```php
   <?php
   require_once 'includes/config.php';
   try {
       $db = getDB();
       echo "✅ Database connected!";
   } catch (Exception $e) {
       echo "❌ Error: " . $e->getMessage();
   }
   ?>
   ```
   - Visit: https://fspo-ecommerce.onrender.com/test-connection.php

### Issue 2: PHP/Code Errors

**Symptoms:**
- Service keeps failing
- Deployment shows error message
- Page blank or error message

**Root Cause:**
- PHP code syntax error
- Missing file or function
- Incompatible dependency

**Fix:**
1. Check logs for specific error
2. Look at recent commits/changes
3. If recent push caused it:
   ```bash
   git log --oneline -5  # See recent changes
   git revert HEAD       # Undo last commit if needed
   git push origin main
   ```

### Issue 3: Memory/Resource Issues

**Symptoms:**
- Service crashes randomly
- Works for a while, then fails
- Gets slower over time

**Root Cause:**
- Render free tier (512MB) is full
- Memory leak in code
- Too many concurrent connections

**Fix:**
1. **Short term:** Restart service
   - Clears memory
   - Temporary fix only

2. **Long term:** Upgrade Render plan
   - Free tier: 512MB RAM
   - Pro tier: 2GB+ RAM
   - Usually solves issues

### Issue 4: DNS/Network Issues

**Symptoms:**
- "Connection timeout"
- "Unable to reach server"
- Works intermittently

**Root Cause:**
- Temporary network hiccup
- Render infrastructure issue
- ISP/local network problem

**Fix:**
1. Wait 5-10 minutes (usually auto-recovers)
2. Try from different network
3. Try clearing DNS cache:
   ```bash
   # On Windows:
   ipconfig /flushdns
   
   # On Mac:
   sudo dscacheutil -flushcache
   ```

### Issue 5: Recent Deployment Failed

**Symptoms:**
- "Service Unavailable" after recent push
- Logs show build error
- Previous version worked fine

**Root Cause:**
- New code has error
- Dependency installation failed
- Environment variable missing

**Fix:**
1. **Option A - Revert last commit:**
   ```bash
   git revert HEAD  # Undo last change
   git push origin main
   # Render auto-deploys, should work again
   ```

2. **Option B - Fix the error:**
   - Check logs for specific error
   - Fix the code locally
   - Commit and push fix
   ```bash
   git add .
   git commit -m "Fix deployment error"
   git push origin main
   ```

3. **Option C - Manual redeploy:**
   - Dashboard → Web Service
   - Deploys tab → "Clear build cache and deploy"
   - Wait for deployment to complete

---

## Verification Checklist

After applying any fix, verify:

- [ ] Dashboard status is "Live" (green)
- [ ] PostgreSQL status is "Active" (green)
- [ ] Website loads: https://fspo-ecommerce.onrender.com
- [ ] Login page accessible
- [ ] Can enter admin credentials
- [ ] Admin login works OR shows "Invalid password" (not connection error)
- [ ] Registration page loads
- [ ] Homepage displays products
- [ ] No error messages in Render logs

---

## Emergency Recovery Steps

If service is completely broken:

### Step 1: Clear Build Cache and Redeploy
1. Dashboard → Web Service → Deploys
2. Click "Clear build cache and deploy"
3. Wait 5-10 minutes for full rebuild
4. Check logs for errors

### Step 2: Rebuild from Main Branch
```bash
# On your computer:
cd /home/elly/Downloads/fspo
git pull origin main  # Get latest
git push origin main  # Force update
```
Then Render will auto-deploy.

### Step 3: Check Environment Variables
1. Dashboard → Web Service → Environment
2. Verify all variables are set:
   - `DB_HOST` = postgresql host
   - `DB_USER` = database user
   - `DB_PASSWORD` = database password
   - `DB_NAME` = fspo_db_snv4
   - `DB_DRIVER` = pgsql
   - `DB_PORT` = 5432

### Step 4: Restart Everything
1. Restart PostgreSQL database first
2. Wait 1 minute
3. Then restart Web Service
4. Wait 2 minutes
5. Test site

---

## Monitoring & Prevention

To avoid future issues:

1. **Monitor Render logs regularly**
   - Check logs after each deployment
   - Watch for warnings

2. **Test locally before pushing**
   ```bash
   php -S localhost:8000
   # Test login, register, etc.
   ```

3. **Use staging environment**
   - Create separate "staging" service
   - Test changes there first
   - Then deploy to production

4. **Set up alerts**
   - Use Render monitoring
   - Get notified of crashes
   - Respond quickly

---

## Support Resources

### Render Help
- **Status Page**: https://status.render.com
- **Docs**: https://render.com/docs
- **Support**: https://support.render.com

### Your Resources
- **GitHub**: https://github.com/maxwell-cod/fspo-ecommerce
- **Dashboard**: https://dashboard.render.com
- **Logs Location**: Render web service logs

---

## Still Not Working?

If you've tried all steps above:

1. **Collect information:**
   - Full error from logs
   - What you were doing when it happened
   - Recent changes made
   - Browser console errors (F12)

2. **Try basic recovery:**
   ```bash
   # Full restart sequence
   git pull origin main
   git push origin main
   # Then manually redeploy on Render
   ```

3. **Contact Render Support**
   - Go to https://support.render.com
   - Include service logs
   - Include error messages
   - Include timestamp of issue

---

## Temporary Workaround

If you need access while debugging:

**Use local development:**
```bash
cd /home/elly/Downloads/fspo
php -S localhost:8000
```

Then visit: http://localhost:8000
- Can test login/register locally
- Can debug issues
- Can use admin panel locally

This doesn't fix production, but lets you continue working.

---

## Common Commands Reference

```bash
# Check Git status
git status

# See recent commits
git log --oneline -10

# Undo last commit
git revert HEAD
git push origin main

# Force update to latest
git pull origin main
git push origin main

# Test PHP locally
php -S localhost:8000

# Check logs (if SSH access available)
tail -f /logs/php-error.log
```

---

## Next Steps

1. ✅ Check Render dashboard status
2. ✅ If not "Live", restart service
3. ✅ Wait 2 minutes
4. ✅ Test: https://fspo-ecommerce.onrender.com
5. ✅ If not working, check logs
6. ✅ Apply appropriate fix from above
7. ✅ Verify with checklist
8. ✅ Test login/register/admin

**Your site should be back online within 15 minutes!** 🚀
