# 🏪 FSPO Ltd - E-Commerce Platform

Complete e-commerce solution built with PHP and MySQL. Features product management, shopping cart, order processing, and admin dashboard.

## ✨ Features

- **🛍️ Product Management**
  - Add, edit, delete products
  - Image uploads with validation
  - Category management
  - Stock tracking
  - Featured products

- **🛒 Shopping Cart**
  - Add/remove items
  - Update quantities
  - Real-time totals
  - Cart persistence

- **❤️ Wishlist**
  - Save products for later
  - One-click add to cart
  - Shareable wishlists

- **📦 Orders**
  - Order placement
  - Order history
  - Order status tracking
  - Invoice generation

- **👥 Admin Dashboard**
  - Dashboard with analytics
  - Product management
  - Category management
  - User management
  - Order management
  - Settings

- **🔐 Security**
  - Bcrypt password hashing
  - CSRF token protection
  - SQL injection prevention
  - File upload validation
  - Session management

- **📱 Responsive Design**
  - Mobile-friendly interface
  - Bootstrap CSS framework
  - Touch-optimized buttons
  - Responsive tables

- **🔍 SEO**
  - Dynamic sitemap
  - Meta tags
  - Structured data (JSON-LD)
  - robots.txt
  - Google Search Console integration

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose
- Or PHP 8.2+, MySQL 10.11+

### Local Development

```bash
# Clone repository
git clone https://github.com/Maxwell-cod/fspo-ecommerce.git
cd fspo-ecommerce

# Using Docker (Recommended)
docker-compose up -d

# Visit application
http://localhost
```

### Manual Setup

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Setup database
mysql -u root -p fspo_db < database.sql

# Start server
php -S localhost:8000

# Visit application
http://localhost:8000
```

## 📋 Database

The project uses MySQL 10.11+ with the following tables:

- **users** - User accounts (admin, client)
- **categories** - Product categories
- **products** - Product listings
- **cart** - Shopping cart items
- **wishlist** - User wishlists
- **orders** - Order records
- **order_items** - Items in orders
- **contact_messages** - Contact form submissions

### Database Initialization

```bash
# Using Docker Compose
docker exec fspo-db mysql -u fspo_user -pfspo_password fspo_db < database.sql

# Or manually
mysql -u fspo_user -p fspo_db < database.sql
```

## 🔐 Default Credentials

**Admin:**
- Email: admin@fspoltd.rw
- Password: (see deployment guide)

**Client:**
- Email: client@gmail.com
- Password: (see deployment guide)

**Note:** Change these immediately in production!

## 🗂️ Project Structure

```
fspo-ecommerce/
├── admin/                 # Admin panel
│   ├── dashboard.php     # Admin dashboard
│   ├── products.php      # Product management
│   ├── categories.php    # Category management
│   ├── orders.php        # Order management
│   └── ...
├── client/               # Client portal
│   ├── dashboard.php     # Client dashboard
│   ├── orders.php        # Order history
│   └── ...
├── includes/             # Core files
│   ├── config.php        # Configuration
│   ├── header.php        # Header template
│   ├── footer.php        # Footer template
│   └── error-handler.php # Error handling
├── css/                  # Stylesheets
├── js/                   # JavaScript
├── uploads/              # Uploaded files
├── logs/                 # Log files
├── database.sql          # Database schema
├── Dockerfile            # Docker configuration
├── docker-compose.yml    # Docker Compose
└── README.md            # This file
```

## 📚 Documentation

- **[GITHUB_RENDER_DEPLOYMENT_GUIDE.md](GITHUB_RENDER_DEPLOYMENT_GUIDE.md)** - Deploy to Render
- **[DEPLOYMENT_COMPLETE.md](DEPLOYMENT_COMPLETE.md)** - Local deployment details
- **[PRODUCT_DELETION_FEATURE.md](PRODUCT_DELETION_FEATURE.md)** - Product deletion system
- **[DEPLOYMENT_CONFIG.md](DEPLOYMENT_CONFIG.md)** - Configuration guide

## 🧪 Testing

### Admin Login
```
Email: admin@fspoltd.rw
```

### Add Product
1. Login as admin
2. Go to Products section
3. Click "Add Product"
4. Fill in details and upload image
5. Save

### Delete Product
1. Go to Products
2. Click red "🗑️ Delete" button
3. Confirm deletion
4. Product removed completely

## 🚀 Deployment

### Deploy to Render

1. Push to GitHub:
   ```bash
   git push origin main
   ```

2. Go to https://render.com
3. Create new Web Service
4. Connect your GitHub repository
5. Configure environment variables
6. Deploy!

See [GITHUB_RENDER_DEPLOYMENT_GUIDE.md](GITHUB_RENDER_DEPLOYMENT_GUIDE.md) for detailed steps.

## 🔧 Configuration

Copy `.env.example` to `.env` and configure:

```bash
# Database
DB_HOST=localhost
DB_USER=fspo_user
DB_PASSWORD=fspo_password
DB_NAME=fspo_db

# Site
SITE_URL=http://localhost:8000

# Environment
APP_ENV=development
APP_DEBUG=true
```

## 🛡️ Security

- SQL injection prevention via prepared statements
- CSRF token protection on forms
- Bcrypt password hashing
- File upload validation
- Session-based authentication
- HTTP security headers
- XSS protection

## 📊 Features in Detail

### Product Deletion
Products can be permanently deleted with:
- One-click deletion
- Confirmation dialog
- Automatic image cleanup
- Cart/wishlist cleanup
- Order preservation

### Error Handling
- Custom error handler
- Exception catching
- Fatal error recovery
- Comprehensive logging

### SEO
- Dynamic XML sitemap
- Meta tags
- Structured data (JSON-LD)
- Google Search Console integration

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📝 License

This project is private/proprietary. All rights reserved.

## 📧 Support

For issues or questions:
- Email: info@fspoltd.rw
- GitHub Issues: [Project Issues](https://github.com/Maxwell-cod/fspo-ecommerce/issues)

## 🎯 Roadmap

- [ ] Payment gateway integration
- [ ] Email notifications
- [ ] Advanced analytics
- [ ] Multi-language support
- [ ] API endpoints
- [ ] Mobile app

## 🙏 Credits

**FSPO Ltd** - E-Commerce Platform
- Developer: Maxwell Muhirwa
- Email: muhirwamaxwell3@gmail.com
- GitHub: [Maxwell-cod](https://github.com/Maxwell-cod)

---

**Status:** ✅ Production Ready  
**Last Updated:** March 28, 2026  
**Version:** 1.0.1
