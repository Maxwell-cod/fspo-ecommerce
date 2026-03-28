╔════════════════════════════════════════════════════════════════════════════╗
║                                                                            ║
║  🌐 RENDER DEPLOYMENT GUIDE - COMPLETE WALKTHROUGH                        ║
║                                                                            ║
║  Deploy FSPO E-Commerce Platform to Render Cloud                          ║
║  Repository: https://github.com/Maxwell-cod/fspo-ecommerce                ║
║                                                                            ║
╚════════════════════════════════════════════════════════════════════════════╝


⏱️ ESTIMATED TIME: 15-20 minutes


═══════════════════════════════════════════════════════════════════════════════
STEP 1: CREATE RENDER ACCOUNT
═══════════════════════════════════════════════════════════════════════════════

1.1 Go to Render Website
    URL: https://render.com
    
1.2 Click "Get Started" (top right)

1.3 Sign Up Options:
    ☐ Option A: Sign up with GitHub (RECOMMENDED - easiest)
      - Click "Continue with GitHub"
      - Authorize Render to access your GitHub
      - Complete setup
    
    ☐ Option B: Sign up with email
      - Enter email: muhirwamaxwell3@gmail.com
      - Set password
      - Verify email
      - Complete setup

✅ RESULT: You have a Render account


═══════════════════════════════════════════════════════════════════════════════
STEP 2: CONNECT GITHUB REPOSITORY
═══════════════════════════════════════════════════════════════════════════════

2.1 In Render Dashboard
    ☐ Look for "New +" button (usually top right)
    ☐ Click "New +"
    ☐ Select "Web Service"

2.2 Connect Repository
    ☐ You'll see "Build and deploy from a Git repository"
    ☐ Click "Connect GitHub"
    
    If GitHub not connected:
    ☐ Click "Connect GitHub" button
    ☐ Authorize Render to access GitHub
    ☐ Select which repositories to allow (or "All repositories")
    ☐ Authorize

2.3 Select Repository
    ☐ Search for "fspo-ecommerce"
    ☐ Click on "Maxwell-cod/fspo-ecommerce"
    ☐ Click "Connect"

✅ RESULT: Repository connected to Render


═══════════════════════════════════════════════════════════════════════════════
STEP 3: CONFIGURE WEB SERVICE
═══════════════════════════════════════════════════════════════════════════════

3.1 Basic Settings
    ☐ Name: fspo-ecommerce
    ☐ Environment: Docker (automatically selected since we have Dockerfile)
    ☐ Region: Choose closest to your location
      - New York (US East)
      - Frankfurt (Europe)
      - Singapore (Asia)

3.2 Git Settings
    ☐ Repository: Maxwell-cod/fspo-ecommerce
    ☐ Branch: main
    ☐ Auto-Deploy: Yes (checked) - auto-deploy on git push

3.3 Build & Start Commands
    ☐ Build Command: (leave empty - Dockerfile handles this)
    ☐ Start Command: (leave empty - Dockerfile handles this)

3.4 Plan Type
    ☐ Select "Free" (for testing/portfolio)
    ☐ Or "Paid" if you want production
    
    Free tier includes:
    - 750 hours/month CPU
    - 0.5 GB RAM
    - Perfect for demo/portfolio

3.5 Environment Variables
    ☐ Skip for now - we'll add them after adding database

✅ RESULT: Web service configured


═══════════════════════════════════════════════════════════════════════════════
STEP 4: ADD DATABASE
═══════════════════════════════════════════════════════════════════════════════

4.1 Scroll Down to Database Section
    ☐ Look for "Add a Database" button

4.2 Create New Database
    ☐ Click "Create Database"
    ☐ Select "PostgreSQL" (recommended for Render)
    ☐ Database name: fspo_db
    ☐ PostgreSQL version: Latest (default)

    Render will provide:
    - Database Host
    - Database User
    - Database Password
    - Database Name

4.3 Database Connection Details
    Render auto-generates:
    ☐ DB_HOST: Something like "dpg-xxxxx.onrender.com"
    ☐ DB_USER: Auto-generated username
    ☐ DB_PASSWORD: Auto-generated password
    ☐ DB_NAME: fspo_db (or auto-generated)
    
    → You'll need these for environment variables!

✅ RESULT: Database created


═══════════════════════════════════════════════════════════════════════════════
STEP 5: ADD ENVIRONMENT VARIABLES
═══════════════════════════════════════════════════════════════════════════════

5.1 In Environment Section, Add Variables
    Click "Add Environment Variable" for each:

    ☐ Variable 1:
      Name: DB_HOST
      Value: (copy from database connection info)
      Example: dpg-xxxxx.onrender.com

    ☐ Variable 2:
      Name: DB_USER
      Value: (copy from database connection info)
      Example: fspo_user

    ☐ Variable 3:
      Name: DB_PASSWORD
      Value: (copy from database connection info)
      Example: (long generated password)

    ☐ Variable 4:
      Name: DB_NAME
      Value: fspo_db

    ☐ Variable 5:
      Name: SITE_URL
      Value: https://fspo-ecommerce.onrender.com
      (You'll get this URL after deployment)

    ☐ Variable 6:
      Name: APP_ENV
      Value: production

    ☐ Variable 7:
      Name: APP_DEBUG
      Value: false

5.2 Save Variables
    ☐ Each variable is saved as you add it

✅ RESULT: Environment variables configured


═══════════════════════════════════════════════════════════════════════════════
STEP 6: REVIEW & DEPLOY
═══════════════════════════════════════════════════════════════════════════════

6.1 Review All Settings
    ☐ Name: fspo-ecommerce
    ☐ Region: Selected
    ☐ Environment: Docker
    ☐ Repository: Maxwell-cod/fspo-ecommerce
    ☐ Branch: main
    ☐ Auto-Deploy: Enabled
    ☐ Database: PostgreSQL created
    ☐ Environment Variables: All added

6.2 Click "Create Web Service"
    ☐ Render starts building Docker image
    ☐ This takes 5-10 minutes first time

6.3 Monitor Deployment
    ☐ Render shows build progress
    ☐ Watch the logs for errors
    ☐ Look for "Build succeeded"
    ☐ Then "Deployment in progress"

✅ RESULT: Deployment started


═══════════════════════════════════════════════════════════════════════════════
STEP 7: WAIT FOR DEPLOYMENT
═══════════════════════════════════════════════════════════════════════════════

Timeline:
├─ 0-2 min: Build starts
├─ 2-8 min: Docker image builds
├─ 8-10 min: Container starts
├─ 10-15 min: Application initializes
└─ 15+ min: Live and ready!

Watch for Status:
☐ "Building" - Docker image being built
☐ "Deploying" - Container starting up
☐ "Live" - Application is LIVE! 🎉

Look for URL:
☐ Render generates public URL
☐ Format: https://fspo-ecommerce.onrender.com
☐ Copy this URL!

⚠️ Note: Free tier services spin down after 15 minutes of inactivity
☐ First request will take 30 seconds (waking up service)
☐ After that, normal speed

✅ RESULT: Application is live!


═══════════════════════════════════════════════════════════════════════════════
STEP 8: VERIFY DEPLOYMENT
═══════════════════════════════════════════════════════════════════════════════

8.1 Test Homepage
    ☐ Visit: https://fspo-ecommerce.onrender.com
    ☐ Should see FSPO homepage
    ☐ May take 30 seconds first time (service waking up)

8.2 Test Products
    ☐ Navigate to Shop/Products
    ☐ Should see products from database
    ☐ Proves database connection works

8.3 Test Admin Panel
    ☐ Visit: https://fspo-ecommerce.onrender.com/admin/
    ☐ Try login with credentials:
      Email: admin@fspo.com (or your admin email)
      Password: (your admin password)

8.4 Check Error Logs
    ☐ In Render dashboard
    ☐ View "Logs" tab
    ☐ Look for any PHP errors
    ☐ Should be clean or minor warnings only

8.5 Test Database Operations
    ☐ Add product to cart
    ☐ Try checkout (test mode)
    ☐ Check if data persists
    ☐ Place test order

✅ RESULT: All systems verified


═══════════════════════════════════════════════════════════════════════════════
STEP 9: AUTO-DEPLOYMENT SETUP
═══════════════════════════════════════════════════════════════════════════════

From now on, Auto-Deployment is ACTIVE!

When you make code changes:

1. Make changes locally
2. Commit to git:
   ☐ git add .
   ☐ git commit -m "Feature: description"

3. Push to GitHub:
   ☐ git push origin main

4. Render automatically:
   ☐ Detects new commit
   ☐ Rebuilds Docker image
   ☐ Deploys new version
   ☐ Takes 3-5 minutes
   ☐ No manual action needed!

View deployment status:
☐ Go to Render dashboard
☐ Click service name
☐ See build/deployment progress
☐ Check logs for any issues

✅ RESULT: Continuous deployment enabled


═══════════════════════════════════════════════════════════════════════════════
STEP 10: CUSTOM DOMAIN (OPTIONAL)
═══════════════════════════════════════════════════════════════════════════════

Want custom domain? (e.g., fspo-ecommerce.com)

10.1 In Render Dashboard
    ☐ Go to Settings
    ☐ Find "Custom Domain"
    ☐ Enter your domain

10.2 Update DNS Records
    ☐ Go to domain registrar
    ☐ Add CNAME record pointing to Render
    ☐ Wait for DNS propagation (5-48 hours)

10.3 SSL Certificate
    ☐ Render auto-provides free SSL
    ☐ HTTPS automatically enabled
    ☐ Green lock icon shows in browser

✅ RESULT: Custom domain configured


═══════════════════════════════════════════════════════════════════════════════
TROUBLESHOOTING
═══════════════════════════════════════════════════════════════════════════════

Issue: Build fails
  → Check Dockerfile syntax
  → View build logs in Render
  → Check .dockerignore for issues
  → Verify all required files are in repo

Issue: Application shows 500 error
  → Check application logs in Render
  → Verify environment variables are set
  → Check database credentials
  → Verify database connection string

Issue: Database connection fails
  → Verify DB_HOST, DB_USER, DB_PASSWORD
  → Check database exists
  → Verify PostgreSQL vs MySQL difference
  → Check network access (should be automatic)

Issue: Images not uploading
  → Check /uploads directory permissions
  → Verify upload directory exists
  → Check docker volume configuration
  → May need to use managed storage (Render Disk)

Issue: Application very slow
  → Free tier has limited resources
  → Service may be spinning down/up
  → Consider upgrading to paid plan
  → Check database performance

Issue: Changes not deploying automatically
  → Verify auto-deploy is enabled
  → Check GitHub webhook is configured
  → Verify branch is correct (main)
  → Check deployment logs in Render

Solution Steps:
1. Check Render logs first
2. Check GitHub webhook status
3. Verify environment variables
4. Check database connection
5. Contact Render support if needed


═══════════════════════════════════════════════════════════════════════════════
ENVIRONMENT VARIABLES REFERENCE
═══════════════════════════════════════════════════════════════════════════════

Required for Render:

DB_HOST
  - PostgreSQL hostname from Render
  - Example: dpg-xxxxx.onrender.com

DB_USER
  - Database username from Render
  - Example: fspo_user

DB_PASSWORD
  - Database password from Render
  - Example: (secure password)

DB_NAME
  - Database name
  - Example: fspo_db

SITE_URL
  - Your Render application URL
  - Example: https://fspo-ecommerce.onrender.com

APP_ENV
  - Set to "production"

APP_DEBUG
  - Set to "false"


═══════════════════════════════════════════════════════════════════════════════
FILE STRUCTURE REMINDER
═══════════════════════════════════════════════════════════════════════════════

Your project structure is ready:

fspo-ecommerce/
├── Dockerfile              ← Docker configuration
├── docker-compose.yml      ← Local testing config
├── .dockerignore           ← Files to exclude from Docker
├── .gitignore              ← Files to exclude from Git
├── .env.example            ← Environment template
├── README.md               ← Project documentation
├── index.php               ← Homepage
├── admin/
│   ├── dashboard.php
│   ├── products.php        ← Product management
│   └── ...
├── includes/
│   ├── config.php          ← Database config
│   └── error-handler.php
├── uploads/                ← Product images
├── logs/                   ← Application logs
└── [other files]


═══════════════════════════════════════════════════════════════════════════════
QUICK REFERENCE - DEPLOYMENT CHECKLIST
═══════════════════════════════════════════════════════════════════════════════

☐ Render account created
☐ GitHub repository connected
☐ Web service configured
☐ Docker environment selected
☐ PostgreSQL database created
☐ Environment variables added:
  ☐ DB_HOST
  ☐ DB_USER
  ☐ DB_PASSWORD
  ☐ DB_NAME
  ☐ SITE_URL
  ☐ APP_ENV
  ☐ APP_DEBUG
☐ Deployment started
☐ Build completed successfully
☐ Application is live
☐ Homepage loads
☐ Products display
☐ Admin panel accessible
☐ Database operations working
☐ Auto-deployment enabled
☐ Logs show no critical errors


═══════════════════════════════════════════════════════════════════════════════
USEFUL LINKS
═══════════════════════════════════════════════════════════════════════════════

Render Dashboard: https://dashboard.render.com
Render Docs: https://docs.render.com
GitHub Repository: https://github.com/Maxwell-cod/fspo-ecommerce
Your Application: https://fspo-ecommerce.onrender.com (after deployment)


═══════════════════════════════════════════════════════════════════════════════
SUPPORT & NEXT STEPS
═══════════════════════════════════════════════════════════════════════════════

After successful deployment:

1. Monitor Application
   - Check logs regularly
   - Monitor performance
   - Track error rate

2. Make Updates
   - Edit code locally
   - Push to GitHub
   - Render auto-deploys

3. Scale If Needed
   - Monitor resource usage
   - Upgrade plan if necessary
   - Consider paid tier for production

4. Add Features
   - Product recommendations
   - Email notifications
   - Payment integration
   - Social media integration

5. Marketing
   - Register with Google Search Console
   - Add to product directories
   - Social media promotion
   - Email marketing


═══════════════════════════════════════════════════════════════════════════════

                    🚀 YOU'RE READY TO DEPLOY TO RENDER!

Follow the steps above to get your FSPO e-commerce platform live on the cloud.

Total time: 15-20 minutes
Result: Production-ready application at https://fspo-ecommerce.onrender.com

Good luck! 🎉

═══════════════════════════════════════════════════════════════════════════════
