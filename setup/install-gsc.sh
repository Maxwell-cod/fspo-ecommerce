#!/bin/bash

# FSPO Ltd - Google Search Console Integration Installer
# This script sets up the Google Search Console integration system
# Usage: bash setup/install-gsc.sh

echo "🔍 FSPO Ltd - Google Search Console Integration Setup"
echo "=================================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get project root
PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"
cd "$PROJECT_ROOT"

echo -e "${BLUE}Project Root: ${PROJECT_ROOT}${NC}"
echo ""

# Check if .well-known directory exists
if [ ! -d ".well-known" ]; then
    echo -e "${YELLOW}Creating .well-known directory...${NC}"
    mkdir -p .well-known
    chmod 755 .well-known
    echo -e "${GREEN}✅ .well-known directory created${NC}"
else
    echo -e "${GREEN}✅ .well-known directory exists${NC}"
fi

echo ""

# Check if setup directory exists
if [ ! -d "setup" ]; then
    echo -e "${YELLOW}Creating setup directory...${NC}"
    mkdir -p setup
    chmod 755 setup
    echo -e "${GREEN}✅ setup directory created${NC}"
else
    echo -e "${GREEN}✅ setup directory exists${NC}"
fi

echo ""

# Check required files
echo -e "${BLUE}Checking required files...${NC}"

files_to_check=(
    "setup/google-search-console.php"
    "setup/submit-to-gsc-api.php"
    "setup/GSC_SETUP_GUIDE.md"
    "setup/GOOGLE_SEARCH_CONSOLE_README.md"
    ".well-known/submit.php"
    ".well-known/check.php"
    "admin/seo-dashboard.php"
)

for file in "${files_to_check[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✅ $file${NC}"
    else
        echo -e "${RED}❌ $file (missing)${NC}"
    fi
done

echo ""

# Create IndexNow key file (optional)
echo -e "${BLUE}IndexNow Key File Setup${NC}"
echo "========================"
echo ""
echo "Your IndexNow key will be generated based on your domain."
echo "This is done automatically when needed."
echo ""
echo -e "${YELLOW}To create it now, you need to:${NC}"
echo "1. Access: http://localhost:8000/setup/submit-to-gsc-api.php"
echo "2. Click 'Check .well-known File'"
echo "3. Follow the instructions"
echo ""

# Create database table for submissions (if MySQL available)
echo -e "${BLUE}Database Setup${NC}"
echo "==============="
echo ""

# Check if we can connect to database
if command -v mysql &> /dev/null; then
    echo -e "${YELLOW}Creating indexnow_log table...${NC}"
    
    # Try to create table
    mysql -h localhost -u fspo_user -pfspo_password fspo_db << EOF 2>/dev/null
CREATE TABLE IF NOT EXISTS indexnow_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    urls TEXT,
    status INT,
    response TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Database table created${NC}"
    else
        echo -e "${YELLOW}⚠️  Database not available, table will be created on first use${NC}"
    fi
else
    echo -e "${YELLOW}MySQL not found in PATH${NC}"
    echo "Table will be created automatically on first submission."
fi

echo ""

# Set permissions
echo -e "${BLUE}Setting Permissions${NC}"
echo "===================="
echo ""

chmod 755 setup 2>/dev/null && echo -e "${GREEN}✅ setup/ (755)${NC}"
chmod 755 .well-known 2>/dev/null && echo -e "${GREEN}✅ .well-known/ (755)${NC}"
chmod 644 setup/*.php 2>/dev/null && echo -e "${GREEN}✅ setup/*.php (644)${NC}"
chmod 644 .well-known/*.php 2>/dev/null && echo -e "${GREEN}✅ .well-known/*.php (644)${NC}"
chmod 644 admin/seo-dashboard.php 2>/dev/null && echo -e "${GREEN}✅ admin/seo-dashboard.php (644)${NC}"

echo ""

# Check PHP version
echo -e "${BLUE}System Check${NC}"
echo "============"
echo ""

if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v 2>/dev/null | head -n 1)
    echo -e "${GREEN}✅ PHP: ${PHP_VERSION}${NC}"
else
    echo -e "${RED}❌ PHP not found${NC}"
fi

# Check curl
if php -m 2>/dev/null | grep -q curl; then
    echo -e "${GREEN}✅ cURL module enabled${NC}"
else
    echo -e "${YELLOW}⚠️  cURL module not found (needed for IndexNow)${NC}"
fi

# Check OpenSSL
if php -m 2>/dev/null | grep -q openssl; then
    echo -e "${GREEN}✅ OpenSSL module enabled${NC}"
else
    echo -e "${YELLOW}⚠️  OpenSSL module not found${NC}"
fi

echo ""

# Summary
echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}✅ Installation Complete!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo -e "${BLUE}Next Steps:${NC}"
echo "1. Visit: http://localhost:8000/setup/google-search-console.php"
echo "2. Follow the step-by-step guide"
echo "3. Verify your domain in Google Search Console"
echo "4. Submit your sitemap"
echo ""
echo -e "${BLUE}Quick Links:${NC}"
echo "• Setup Guide: http://localhost:8000/setup/google-search-console.php"
echo "• API Tool: http://localhost:8000/setup/submit-to-gsc-api.php"
echo "• Admin Dashboard: http://localhost:8000/admin/seo-dashboard.php"
echo "• Markdown Guide: setup/GSC_SETUP_GUIDE.md"
echo "• README: setup/GOOGLE_SEARCH_CONSOLE_README.md"
echo ""
echo -e "${BLUE}Your Sitemap:${NC}"
echo "• URL: http://localhost:8000/sitemap.xml"
echo ""
echo -e "${YELLOW}For documentation, see:${NC}"
echo "• setup/GSC_SETUP_GUIDE.md"
echo "• setup/GOOGLE_SEARCH_CONSOLE_README.md"
echo ""
echo "Happy optimizing! 🚀"
