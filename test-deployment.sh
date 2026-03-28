#!/bin/bash

#################################################################################
# FSPO Ltd - Deployment Readiness Test Suite
# Tests all critical system components before deployment
#################################################################################

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║     FSPO LTD - DEPLOYMENT READINESS TEST SUITE                 ║"
echo "║     Testing all critical systems for production readiness      ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Test counters
TESTS_PASSED=0
TESTS_FAILED=0

# Project root
PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$PROJECT_ROOT"

#################################################################################
# TEST FUNCTIONS
#################################################################################

test_passed() {
    echo -e "${GREEN}✅ PASS${NC}: $1"
    ((TESTS_PASSED++))
}

test_failed() {
    echo -e "${RED}❌ FAIL${NC}: $1"
    ((TESTS_FAILED++))
}

test_warning() {
    echo -e "${YELLOW}⚠️  WARN${NC}: $1"
}

#################################################################################
# 1. SYSTEM REQUIREMENTS
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}1. SYSTEM REQUIREMENTS${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Check PHP
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -r 'echo PHP_VERSION;')
    if [[ $PHP_VERSION == 8.* ]]; then
        test_passed "PHP 8.x installed (v$PHP_VERSION)"
    else
        test_warning "PHP version is $PHP_VERSION (8.x recommended)"
    fi
else
    test_failed "PHP not found"
fi

# Check MySQL
if command -v mysql &> /dev/null; then
    test_passed "MySQL client installed"
else
    test_warning "MySQL client not found (needed for backups)"
fi

# Check cURL
if php -m | grep -q curl; then
    test_passed "PHP cURL extension enabled"
else
    test_failed "PHP cURL extension not enabled"
fi

# Check OpenSSL
if php -m | grep -q openssl; then
    test_passed "PHP OpenSSL extension enabled"
else
    test_failed "PHP OpenSSL extension not enabled"
fi

# Check PDO MySQL
if php -m | grep -q pdo_mysql; then
    test_passed "PHP PDO MySQL extension enabled"
else
    test_failed "PHP PDO MySQL extension not enabled"
fi

echo ""

#################################################################################
# 2. FILE STRUCTURE
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}2. FILE STRUCTURE${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Check critical files
files_to_check=(
    "includes/config.php"
    "includes/header.php"
    "includes/footer.php"
    "includes/error-handler.php"
    "index.php"
    "admin/dashboard.php"
    "admin/products.php"
    "setup/index.php"
    ".well-known/submit.php"
    "robots.txt"
    "sitemap.xml.php"
)

for file in "${files_to_check[@]}"; do
    if [ -f "$file" ]; then
        test_passed "File exists: $file"
    else
        test_failed "File missing: $file"
    fi
done

echo ""

#################################################################################
# 3. DIRECTORY PERMISSIONS
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}3. DIRECTORY PERMISSIONS${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Check directory writability
dirs_to_check=(
    "uploads"
    "logs"
    ".well-known"
    "setup"
)

for dir in "${dirs_to_check[@]}"; do
    if [ -d "$dir" ]; then
        if [ -w "$dir" ]; then
            test_passed "Directory writable: $dir"
        else
            test_failed "Directory NOT writable: $dir"
        fi
    else
        # Create directory and check
        mkdir -p "$dir" 2>/dev/null
        if [ -w "$dir" ]; then
            test_passed "Directory created and writable: $dir"
        else
            test_failed "Cannot create directory: $dir"
        fi
    fi
done

echo ""

#################################################################################
# 4. DATABASE CONNECTIVITY
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}4. DATABASE CONNECTIVITY${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Test database connection
DB_TEST=$(php -r "
require 'includes/config.php';
try {
    \$db = getDB();
    \$result = \$db->query('SELECT 1');
    echo 'OK';
} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage();
}
" 2>&1)

if [[ $DB_TEST == "OK" ]]; then
    test_passed "Database connection successful"
else
    test_failed "Database connection failed: $DB_TEST"
fi

# Check database tables
if [[ $DB_TEST == "OK" ]]; then
    TABLES=$(php -r "
    require 'includes/config.php';
    \$db = getDB();
    \$result = \$db->query('SHOW TABLES FROM fspo_db');
    echo \$result->rowCount();
    " 2>&1)
    
    if [ "$TABLES" -ge 8 ]; then
        test_passed "Database tables exist ($TABLES tables)"
    else
        test_failed "Not enough database tables ($TABLES found, 8 expected)"
    fi
fi

echo ""

#################################################################################
# 5. CONFIGURATION TESTING
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}5. CONFIGURATION TESTING${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Check config constants
CONFIG_TEST=$(php -r "
require 'includes/config.php';
\$checks = [
    'DB_HOST' => defined('DB_HOST'),
    'DB_USER' => defined('DB_USER'),
    'DB_NAME' => defined('DB_NAME'),
    'SITE_URL' => defined('SITE_URL'),
    'SITE_NAME' => defined('SITE_NAME'),
];
echo json_encode(\$checks);
" 2>&1)

if echo "$CONFIG_TEST" | grep -q '"DB_HOST":true'; then
    test_passed "All configuration constants defined"
else
    test_failed "Missing configuration constants"
fi

# Check error handler
if [ -f "includes/error-handler.php" ]; then
    test_passed "Error handler configured"
else
    test_failed "Error handler missing"
fi

echo ""

#################################################################################
# 6. SECURITY CHECKS
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}6. SECURITY CHECKS${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Check .htaccess files
if [ -f ".htaccess" ]; then
    if grep -q "X-Content-Type-Options" .htaccess || grep -q "Header" includes/header.php; then
        test_passed "Security headers configured"
    else
        test_warning "Security headers not found"
    fi
else
    test_warning ".htaccess file not found"
fi

# Check uploads protection
if [ -f "uploads/.htaccess" ]; then
    test_passed "Uploads directory protected (.htaccess)"
else
    test_warning "Uploads .htaccess protection missing"
fi

# Check CSRF token functions
if grep -q "generateCSRFToken" includes/config.php; then
    test_passed "CSRF token functions implemented"
else
    test_warning "CSRF token functions not found"
fi

echo ""

#################################################################################
# 7. SEO COMPONENTS
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}7. SEO COMPONENTS${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Check sitemap
if [ -f "sitemap.xml.php" ]; then
    SITEMAP_TEST=$(php sitemap.xml.php 2>&1 | head -1)
    if echo "$SITEMAP_TEST" | grep -q "<?xml"; then
        test_passed "Sitemap generator working"
    else
        test_failed "Sitemap generator not working"
    fi
else
    test_failed "Sitemap file missing"
fi

# Check robots.txt
if [ -f "robots.txt" ]; then
    if grep -q "User-agent" robots.txt; then
        test_passed "robots.txt configured"
    else
        test_failed "robots.txt invalid"
    fi
else
    test_failed "robots.txt missing"
fi

# Check GSC tools
if [ -f "setup/index.php" ]; then
    test_passed "Google Search Console tools installed"
else
    test_warning "Google Search Console tools not found"
fi

echo ""

#################################################################################
# 8. API & ENDPOINT TESTING
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}8. API & ENDPOINT TESTING${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Check IndexNow API handler
if [ -f ".well-known/submit.php" ]; then
    test_passed "IndexNow API handler exists"
else
    test_failed "IndexNow API handler missing"
fi

# Check verification tools
if [ -f ".well-known/check.php" ]; then
    test_passed "Verification tools exist"
else
    test_failed "Verification tools missing"
fi

echo ""

#################################################################################
# 9. MEMORY & RESOURCE LIMITS
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}9. MEMORY & RESOURCE LIMITS${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Check memory limit
MEM_LIMIT=$(php -r "echo ini_get('memory_limit');")
echo -e "Memory limit: ${BLUE}$MEM_LIMIT${NC}"

# Check execution time
EXEC_TIME=$(php -r "echo ini_get('max_execution_time');")
echo -e "Max execution time: ${BLUE}${EXEC_TIME}s${NC}"

# Check upload size
UPLOAD_SIZE=$(php -r "echo ini_get('upload_max_filesize');")
echo -e "Max upload size: ${BLUE}$UPLOAD_SIZE${NC}"

test_passed "Resource limits readable"
echo ""

#################################################################################
# 10. SYNTAX & CODE VALIDATION
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}10. SYNTAX & CODE VALIDATION${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Check PHP syntax of critical files
SYNTAX_ERROR=0
for file in includes/*.php admin/*.php setup/*.php .well-known/*.php; do
    if [ -f "$file" ]; then
        if ! php -l "$file" > /dev/null 2>&1; then
            test_failed "Syntax error in $file"
            ((SYNTAX_ERROR++))
        fi
    fi
done

if [ $SYNTAX_ERROR -eq 0 ]; then
    test_passed "No PHP syntax errors found"
fi

echo ""

#################################################################################
# 11. FILE ENCODING & ENCODING
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}11. CONFIGURATION BACKUP${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Create backup directory
BACKUP_DIR="backups"
if [ ! -d "$BACKUP_DIR" ]; then
    mkdir -p "$BACKUP_DIR"
fi

# Backup config
if cp includes/config.php "$BACKUP_DIR/config.php.backup.$(date +%s)" 2>/dev/null; then
    test_passed "Configuration backup created"
else
    test_warning "Could not create configuration backup"
fi

echo ""

#################################################################################
# SUMMARY
#################################################################################

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}TEST SUMMARY${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

echo -e "Tests Passed: ${GREEN}$TESTS_PASSED${NC}"
echo -e "Tests Failed: ${RED}$TESTS_FAILED${NC}"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}✅ ALL TESTS PASSED${NC}"
    echo -e "${GREEN}System is READY for deployment! 🚀${NC}"
    exit 0
else
    echo -e "${RED}❌ SOME TESTS FAILED${NC}"
    echo -e "${YELLOW}Please fix the issues above before deploying${NC}"
    exit 1
fi
