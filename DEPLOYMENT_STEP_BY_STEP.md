╔════════════════════════════════════════════════════════════════════════════╗
║                                                                            ║
║  🚀 GITHUB TO RENDER DEPLOYMENT - STEP BY STEP                            ║
║                                                                            ║
║  Account: Maxwell-cod                                                      ║
║  Email: muhirwamaxwell3@gmail.com                                          ║
║  Target: Render Cloud Platform                                             ║
║                                                                            ║
╚════════════════════════════════════════════════════════════════════════════╝


✅ PHASE 1: GITHUB SETUP (30 minutes)
════════════════════════════════════════════════════════════════════════════

STEP 1.1: Login to GitHub
  URL: https://github.com/login
  Username: Maxwell-cod
  Password: Billionaire@23
  ☐ Login successful
  ☐ Two-factor authentication (if enabled)

STEP 1.2: Create Repository
  ☐ Click "+" in top right
  ☐ Select "New repository"
  ☐ Repository name: fspo-ecommerce
  ☐ Description: FSPO Ltd E-Commerce Platform
  ☐ Visibility: Public (recommended for portfolio)
  ☐ Click "Create repository"
  
  RESULT: https://github.com/Maxwell-cod/fspo-ecommerce

STEP 1.3: Note Repository URL
  Copy this URL: https://github.com/Maxwell-cod/fspo-ecommerce.git
  You'll need it in Step 2.5


✅ PHASE 2: LOCAL GIT SETUP (20 minutes)
════════════════════════════════════════════════════════════════════════════

STEP 2.1: Open Terminal
  cd /home/elly/Downloads/fspo

STEP 2.2: Initialize Git
  ☐ git init

STEP 2.3: Configure Git User
  ☐ git config user.name "Maxwell Muhirwa"
  ☐ git config user.email "muhirwamaxwell3@gmail.com"

STEP 2.4: Add All Files
  ☐ git add .
  
  This stages:
  - All project files
  - Dockerfile
  - docker-compose.yml
  - .gitignore
  - .dockerignore
  - README.md
  - Everything except ignored files

STEP 2.5: Create Initial Commit
  ☐ git commit -m "Initial commit: FSPO e-commerce platform v1.0"

STEP 2.6: Rename Branch (if needed)
  ☐ git branch -M main

STEP 2.7: Add Remote Repository
  ☐ git remote add origin https://github.com/Maxwell-cod/fspo-ecommerce.git

STEP 2.8: Verify Remote
  ☐ git remote -v
  
  Should show:
  origin  https://github.com/Maxwell-cod/fspo-ecommerce.git (fetch)
  origin  https://github.com/Maxwell-cod/fspo-ecommerce.git (push)


✅ PHASE 3: PUSH TO GITHUB (15 minutes)
════════════════════════════════════════════════════════════════════════════

STEP 3.1: Push Code to GitHub
  ☐ git push -u origin main
  
  First time will ask for authentication:
  - Use GitHub Personal Access Token (recommended)
  - Or username + password

STEP 3.2: Verify on GitHub
  ☐ Go to https://github.com/Maxwell-cod/fspo-ecommerce
  ☐ Should see all files
  ☐ Should see README.md content
  ☐ Should see Dockerfile
  ☐ Should see docker-compose.yml

STEP 3.3: Get Personal Access Token (if needed)
  ☐ GitHub Settings → Developer settings → Personal access tokens
  ☐ Generate new token
  ☐ Scopes: repo (full control of private repositories)
  ☐ Copy token (you won't see it again)
  ☐ Use token as password for git push


✅ PHASE 4: DOCKER TEST (20 minutes)
════════════════════════════════════════════════════════════════════════════

STEP 4.1: Install Docker (if not installed)
  ☐ Visit https://www.docker.com/products/docker-desktop
  ☐ Download for your OS
  ☐ Install and restart

STEP 4.2: Build Docker Image
  ☐ cd /home/elly/Downloads/fspo
  ☐ docker build -t fspo-ecommerce:latest .
  
  Wait for build to complete (5-10 minutes)

STEP 4.3: Test Docker Compose Locally
  ☐ docker-compose up -d
  
  This starts:
  - Web service on port 80
  - MySQL on port 3306
  - PhpMyAdmin on port 8081

STEP 4.4: Verify Services Running
  ☐ docker-compose ps
  
  Should show 3 services running:
  - fspo-web
  - fspo-db
  - fspo-phpmyadmin

STEP 4.5: Test Application
  ☐ Visit http://localhost
  ☐ Should see FSPO homepage
  ☐ Try admin login
  ☐ Verify products display

STEP 4.6: Test PhpMyAdmin
  ☐ Visit http://localhost:8081
  ☐ Login: fspo_user / fspo_password
  ☐ Check databases and tables

STEP 4.7: Stop Services
  ☐ docker-compose down


✅ PHASE 5: RENDER SETUP (30 minutes)
════════════════════════════════════════════════════════════════════════════

STEP 5.1: Sign Up for Render
  ☐ Visit https://render.com
  ☐ Click "Get Started"
  ☐ Choose "GitHub" for sign-up (recommended)
  ☐ Authorize Render to access GitHub
  ☐ Complete signup

STEP 5.2: Create New Web Service
  ☐ Click "New +" button
  ☐ Select "Web Service"
  ☐ Click "Build and deploy from a Git repository"

STEP 5.3: Connect GitHub Repository
  ☐ Click "Connect GitHub" (if not connected)
  ☐ Search for: fspo-ecommerce
  ☐ Select: Maxwell-cod/fspo-ecommerce
  ☐ Click "Connect"

STEP 5.4: Configure Service
  ☐ Name: fspo-ecommerce
  ☐ Region: Choose closest to you (e.g., New York, Frankfurt)
  ☐ Branch: main
  ☐ Build Command: (leave empty - uses Dockerfile)
  ☐ Start Command: (leave empty - uses Dockerfile)


✅ PHASE 6: ENVIRONMENT VARIABLES (15 minutes)
════════════════════════════════════════════════════════════════════════════

STEP 6.1: Add Database Service
  ☐ Scroll down to "Add a Database"
  ☐ Click "Create Database"
  ☐ Choose "PostgreSQL" (Render's PostgreSQL)
  ☐ This creates managed database
  ☐ Render auto-provides connection details

STEP 6.2: Add Environment Variables
  In "Environment" section, click "Add Environment Variable"
  
  Add these variables:
  
  ☐ DB_HOST = (Render will provide PostgreSQL host)
  ☐ DB_USER = (Render-generated username)
  ☐ DB_PASSWORD = (Render-generated password)
  ☐ DB_NAME = fspo_db
  ☐ SITE_URL = https://fspo-ecommerce.onrender.com
  ☐ APP_ENV = production
  ☐ APP_DEBUG = false
  
  (Render will show PostgreSQL connection details when you add database)

STEP 6.3: Billing Information
  ☐ Add payment method (free tier still works)
  ☐ Or stay on free tier with limitations


✅ PHASE 7: DEPLOYMENT (10-15 minutes)
════════════════════════════════════════════════════════════════════════════

STEP 7.1: Deploy Service
  ☐ Review all settings
  ☐ Click "Create Web Service"
  ☐ Render starts building Docker image
  ☐ Watch build logs for errors

STEP 7.2: Monitor Deployment
  ☐ Render shows build progress
  ☐ Build takes 5-10 minutes
  ☐ Logs show Docker build steps
  ☐ Watch for "Build succeeded"

STEP 7.3: Wait for Service to Start
  ☐ After build, Render deploys container
  ☐ Service starts on Render servers
  ☐ Watch for "Live" status
  ☐ Takes 2-3 minutes total

STEP 7.4: Get Public URL
  ☐ Once live, Render shows public URL
  ☐ Something like: https://fspo-ecommerce.onrender.com
  ☐ Copy this URL


✅ PHASE 8: VERIFICATION (15 minutes)
════════════════════════════════════════════════════════════════════════════

STEP 8.1: Test Application
  ☐ Visit your Render URL
  ☐ Should see FSPO homepage
  ☐ Navigate to Products
  ☐ Try admin login
  ☐ Test shopping cart

STEP 8.2: Check Logs
  ☐ In Render dashboard, view logs
  ☐ Look for errors
  ☐ Verify database connections

STEP 8.3: Test Database Connection
  ☐ Try to view products (tests DB)
  ☐ Try to place test order
  ☐ Add product to cart
  ☐ Verify data persists

STEP 8.4: Test Image Uploads
  ☐ Go to admin/products
  ☐ Add new product with image
  ☐ Upload image from computer
  ☐ Verify image displays

STEP 8.5: Test Delete Feature
  ☐ Go to admin/products
  ☐ Click delete on test product
  ☐ Confirm deletion
  ☐ Verify product removed

STEP 8.6: Verify SSL/HTTPS
  ☐ Check for lock icon in browser
  ☐ URL should be https://
  ☐ Render provides free SSL


✅ PHASE 9: AUTO-DEPLOYMENT (Ongoing)
════════════════════════════════════════════════════════════════════════════

From now on:

1. You make code changes locally
2. Commit and push to GitHub:
   ☐ git add .
   ☐ git commit -m "Feature: description"
   ☐ git push origin main

3. Render automatically:
   ☐ Detects changes
   ☐ Rebuilds Docker image
   ☐ Deploys new version
   ☐ No manual action needed!

4. Check deployment status:
   ☐ Go to Render dashboard
   ☐ See build progress
   ☐ New version live in 3-5 minutes


✅ TROUBLESHOOTING
════════════════════════════════════════════════════════════════════════════

Issue: Docker build fails
  → Check .dockerignore (shouldn't exclude important files)
  → Check Dockerfile syntax
  → View build logs in Render dashboard

Issue: Database connection error
  → Verify DB_HOST, DB_USER, DB_PASSWORD in environment
  → PostgreSQL vs MySQL difference?
  → Check database exists in Render

Issue: Application shows 500 error
  → Check error logs in Render
  → Verify all PHP extensions installed in Dockerfile
  → Check PHP config values

Issue: Images not uploading
  → Check /uploads directory permissions
  → Verify upload directory exists
  → Check Docker volume configuration

Issue: Changes not deploying
  → Verify GitHub repository connected
  → Check for git push errors
  → Monitor Render dashboard for webhooks


📊 EXPECTED TIMELINE
════════════════════════════════════════════════════════════════════════════

Phase 1 (GitHub): 30 min
Phase 2 (Git Setup): 20 min
Phase 3 (Push): 15 min
Phase 4 (Docker Test): 20 min
Phase 5 (Render Setup): 30 min
Phase 6 (Environment): 15 min
Phase 7 (Deploy): 15 min
Phase 8 (Verify): 15 min

TOTAL: ~2.5 hours for first deployment

After that: Push to GitHub → Auto-deploy in 3-5 minutes!


🎯 FINAL CHECKLIST
════════════════════════════════════════════════════════════════════════════

✅ Code ready (all files)
✅ Dockerfile created
✅ docker-compose.yml created
✅ .dockerignore created
✅ .gitignore created
✅ .env.example created
✅ README.md created

✅ GitHub account (Maxwell-cod)
✅ Repository created (fspo-ecommerce)
✅ Git configured locally
✅ Code pushed to GitHub

✅ Docker tested locally
✅ Services verified running
✅ Application accessible

✅ Render account created
✅ GitHub connected to Render
✅ Database service added
✅ Environment variables configured

✅ Deployment started
✅ Build successful
✅ Application live
✅ Public URL obtained

✅ Application tested
✅ All features working
✅ Admin panel accessible
✅ Database operational

✅ Ready for production!


════════════════════════════════════════════════════════════════════════════

                    YOU'RE READY TO DEPLOY! 🚀

Follow the steps above to deploy your FSPO e-commerce platform
from your local machine to Render cloud platform.

All files are prepared. Just follow the checklist!

════════════════════════════════════════════════════════════════════════════

Questions? Issues? Check the troubleshooting section above!

Good luck with your deployment! 🎉

════════════════════════════════════════════════════════════════════════════
