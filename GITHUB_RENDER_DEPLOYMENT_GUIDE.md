╔════════════════════════════════════════════════════════════════════════════╗
║                                                                            ║
║  🚀 DEPLOYMENT WORKFLOW - GITHUB TO RENDER                                 ║
║                                                                            ║
║  Step 1: GitHub Setup                                                      ║
║  Step 2: Commit Code                                                       ║
║  Step 3: Docker Setup                                                      ║
║  Step 4: Render Deployment                                                 ║
║                                                                            ║
╚════════════════════════════════════════════════════════════════════════════╝


📋 STEP 1: GITHUB SETUP
════════════════════════════════════════════════════════════════════════════

ACCOUNT INFORMATION:
  Username: Maxwell-cod
  Email: muhirwamaxwell3@gmail.com
  Password: Billionaire@23

WHAT TO DO:

1. Go to: https://github.com/login
2. Enter credentials:
   - Username: Maxwell-cod
   - Password: Billionaire@23
   - Verify with 2FA if enabled

3. Create new repository:
   - Click "+" → New repository
   - Name: fspo-ecommerce
   - Description: "FSPO Ltd E-Commerce Platform"
   - Visibility: Public (or Private)
   - Initialize with README: No
   - Click "Create repository"

RESULT:
  Repository URL: https://github.com/Maxwell-cod/fspo-ecommerce


📋 STEP 2: GIT SETUP & CODE COMMIT
════════════════════════════════════════════════════════════════════════════

IN YOUR TERMINAL (in /home/elly/Downloads/fspo):

1. Initialize Git:
   git init

2. Configure Git:
   git config user.name "Maxwell Muhirwa"
   git config user.email "muhirwamaxwell3@gmail.com"

3. Add all files:
   git add .

4. Initial commit:
   git commit -m "Initial commit: FSPO e-commerce platform"

5. Add remote repository:
   git remote add origin https://github.com/Maxwell-cod/fspo-ecommerce.git

6. Push to GitHub:
   git branch -M main
   git push -u origin main

NOTES:
  - You'll be prompted for GitHub credentials
  - Use Personal Access Token instead of password (recommended)
  - Or setup SSH key for authentication


📋 STEP 3: DOCKER SETUP
════════════════════════════════════════════════════════════════════════════

CREATE Dockerfile (root directory):

FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql
RUN apt-get update && apt-get install -y \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80

CMD ["apache2-foreground"]


CREATE .dockerignore (root directory):

.git
.gitignore
.DS_Store
node_modules
logs/*
backups/*
uploads/*
.env.local
.well-known/submit.php.enhanced


CREATE docker-compose.yml (root directory):

version: '3.8'

services:
  web:
    build: .
    ports:
      - "80:80"
    environment:
      - DB_HOST=db
      - DB_USER=fspo_user
      - DB_PASSWORD=fspo_password
      - DB_NAME=fspo_db
    depends_on:
      - db
    volumes:
      - ./uploads:/var/www/html/uploads
      - ./logs:/var/www/html/logs

  db:
    image: mysql:10.11
    environment:
      - MYSQL_ROOT_PASSWORD=root_password
      - MYSQL_USER=fspo_user
      - MYSQL_PASSWORD=fspo_password
      - MYSQL_DATABASE=fspo_db
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"

volumes:
  db_data:


📋 STEP 4: RENDER DEPLOYMENT
════════════════════════════════════════════════════════════════════════════

WHAT IS RENDER?
  - Cloud platform (like Heroku)
  - Free tier available
  - Supports Docker containers
  - Integrated PostgreSQL/MySQL
  - Custom domains
  - Easy GitHub integration


DEPLOYMENT STEPS:

1. Go to: https://render.com

2. Sign up or login:
   - Use GitHub account (recommended)
   - Or create new account

3. Connect GitHub:
   - Click "New +"
   - Select "Web Service"
   - Connect GitHub
   - Select: Maxwell-cod/fspo-ecommerce
   - Authorize Render

4. Configure Service:
   - Name: fspo-ecommerce
   - Region: Choose closest to you
   - Runtime: Docker
   - Build Command: (Leave default)
   - Start Command: (Leave default)

5. Set Environment Variables:
   - DB_HOST: [PostgreSQL hostname]
   - DB_USER: [username]
   - DB_PASSWORD: [password]
   - DB_NAME: fspo_db
   - SITE_URL: https://fspo-ecommerce.onrender.com

6. Add Database:
   - Click "Add PostgreSQL Database"
   - This creates managed database
   - Render provides connection details
   - Automatically add to environment

7. Deploy:
   - Click "Create Web Service"
   - Render builds Docker image
   - Deploys to production
   - Gets public URL


RESULT:
  Your app URL: https://fspo-ecommerce.onrender.com
  Database: Managed PostgreSQL by Render
  Auto-deploys when you push to GitHub


🔧 DATABASE MIGRATION (MySQL → PostgreSQL)
════════════════════════════════════════════════════════════════════════════

WHY POSTGRESQL?
  - Better for Render
  - More reliable
  - Better scaling
  - Free tier suitable

WHAT NEEDS TO CHANGE:

1. Update includes/config.php:
   
   OLD (MySQL):
   $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
   
   NEW (PostgreSQL):
   $dsn = 'pgsql:host=' . DB_HOST . ';dbname=' . DB_NAME;

2. Update SQL syntax:
   - AUTO_INCREMENT → SERIAL
   - TIMESTAMP DEFAULT CURRENT_TIMESTAMP (same)
   - Most queries compatible

3. Export data:
   - Use database.sql
   - Render handles migration
   - Or import via pgAdmin


📊 FILE STRUCTURE FOR DEPLOYMENT
════════════════════════════════════════════════════════════════════════════

Your GitHub repository should have:

fspo-ecommerce/
├── Dockerfile
├── docker-compose.yml
├── .dockerignore
├── .gitignore
├── README.md
├── admin/
│   ├── products.php
│   ├── dashboard.php
│   └── ...
├── includes/
│   ├── config.php
│   ├── error-handler.php
│   ├── header.php
│   └── ...
├── css/
│   └── style.css
├── js/
│   └── main.js
├── uploads/
│   └── (empty - created at runtime)
├── logs/
│   └── (empty - created at runtime)
├── backups/
│   └── (empty - for backups)
├── database.sql
├── index.php
├── product.php
└── ... (all other files)


🔐 ENVIRONMENT VARIABLES FOR RENDER
════════════════════════════════════════════════════════════════════════════

Create .env.example (commit to GitHub):

DB_HOST=localhost
DB_USER=fspo_user
DB_PASSWORD=fspo_password
DB_NAME=fspo_db
SITE_URL=https://fspo-ecommerce.onrender.com
APP_ENV=production
APP_DEBUG=false


In Render Dashboard, add:

DB_HOST={render-postgres-host}
DB_USER={render-user}
DB_PASSWORD={render-password}
DB_NAME=fspo_db
SITE_URL=https://fspo-ecommerce.onrender.com
APP_ENV=production
APP_DEBUG=false


✅ PRE-DEPLOYMENT CHECKLIST
════════════════════════════════════════════════════════════════════════════

Before pushing to GitHub:

Code:
  ☐ All files backed up locally
  ☐ Sensitive data removed (passwords)
  ☐ .gitignore configured
  ☐ No node_modules or vendor/ committed
  ☐ .env.example created (no actual secrets)

Docker:
  ☐ Dockerfile created
  ☐ docker-compose.yml created
  ☐ .dockerignore created
  ☐ Tested locally: docker build .
  ☐ Tested locally: docker-compose up

GitHub:
  ☐ Repository created
  ☐ Git initialized locally
  ☐ Remote configured
  ☐ Ready to push

Render:
  ☐ Account created
  ☐ GitHub connected
  ☐ Ready to deploy


🚀 QUICK START COMMANDS
════════════════════════════════════════════════════════════════════════════

1. Setup Git locally:
   cd /home/elly/Downloads/fspo
   git init
   git config user.name "Maxwell Muhirwa"
   git config user.email "muhirwamaxwell3@gmail.com"

2. Add files:
   git add .

3. Commit:
   git commit -m "Initial commit: FSPO e-commerce platform"

4. Add remote:
   git remote add origin https://github.com/Maxwell-cod/fspo-ecommerce.git

5. Push to GitHub:
   git branch -M main
   git push -u origin main

6. Test Docker locally:
   docker build -t fspo-ecommerce .
   docker-compose up

7. Go to Render:
   https://render.com → New Web Service → GitHub


📱 EXPECTED WORKFLOW
════════════════════════════════════════════════════════════════════════════

1. You push code to GitHub
   ↓
2. Render detects changes (webhook)
   ↓
3. Render builds Docker image
   ↓
4. Render deploys container
   ↓
5. Your app is live!
   ↓
6. You get URL: https://fspo-ecommerce.onrender.com


⚡ AUTO-DEPLOYMENT
════════════════════════════════════════════════════════════════════════════

After first deployment:

- Every push to main branch → Auto-deploy
- Changes live in 2-3 minutes
- No manual action needed
- Render rebuilds and restarts service


🎯 NEXT STEPS
════════════════════════════════════════════════════════════════════════════

Ready to proceed?

Step 1: Create GitHub repository
Step 2: Setup Git locally
Step 3: Create Docker files
Step 4: Push to GitHub
Step 5: Setup on Render
Step 6: Deploy!

Let me know which step you need help with!

════════════════════════════════════════════════════════════════════════════
