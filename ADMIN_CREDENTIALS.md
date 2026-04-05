# 🔐 ADMIN ACCESS CREDENTIALS

## Admin Login Details

### Email & Password
```
Email:    admin@fspoltd.rw
Password: admin123
```

---

## How to Login

1. **Visit**: https://fspo-ecommerce.onrender.com/login.php

2. **Enter Credentials**:
   - Email: `admin@fspoltd.rw`
   - Password: `admin123`

3. **Click**: "Sign In →"

4. **You'll be redirected** to: https://fspo-ecommerce.onrender.com/admin/dashboard.php

---

## Admin Panel Features

Once logged in, you can access:

### 📊 Dashboard
- View statistics
- See orders summary
- Check product inventory
- View customer metrics

### 📦 Products
- ✅ Add new products
- ✅ Edit existing products
- ✅ Delete products
- ✅ Set prices
- ✅ Manage stock
- ✅ Upload product images
- ✅ Mark as featured

### 🏷️ Categories
- ✅ Create categories
- ✅ Edit categories
- ✅ Delete categories
- ✅ Organize products

### 📋 Orders
- ✅ View all orders
- ✅ Update order status
- ✅ Process shipping
- ✅ Track deliveries

### 👥 Users
- ✅ View registered users
- ✅ Manage user roles
- ✅ Deactivate users if needed
- ✅ View user details

### 💬 Messages
- ✅ View contact form submissions
- ✅ Mark as read/unread
- ✅ Reply to messages
- ✅ Delete messages

### ⚙️ Settings
- ✅ Update store information
- ✅ Configure shipping
- ✅ Manage payment methods
- ✅ Site settings

---

## Password Information

### Current Password Details
- **Email**: admin@fspoltd.rw
- **Plain Password**: admin123
- **Password Hash**: $2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lm
- **Hash Algorithm**: bcrypt (PHP password_hash)

### Security Notes
- Passwords are hashed with bcrypt
- Plain passwords are never stored in database
- Only the hash is stored for security

---

## Changing Admin Password

To change the admin password:

1. Login to admin panel
2. Go to **Settings** or **Profile**
3. Look for "Change Password" option
4. Enter old password
5. Enter new password twice
6. Click "Save"

*Note: This feature depends on whether it's implemented in your admin settings page.*

---

## If You Forgot the Password

### Option 1: Direct Database Update (Emergency)
You can reset the password directly via PostgreSQL:

```sql
-- Login to PostgreSQL on Render
-- Run this query to reset to default:

UPDATE users 
SET password = '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lm'
WHERE email = 'admin@fspoltd.rw';

-- Now login with: admin123
```

### Option 2: Create New Admin User
You can add another admin in the database:

```sql
INSERT INTO users (name, email, phone, password, role)
VALUES (
    'Admin 2',
    'admin2@fspoltd.rw',
    '+250 785 723 677',
    '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lm',
    'admin'
);

-- Login with: admin2@fspoltd.rw / admin123
```

---

## Security Recommendations

🔒 **Important**: For production use, you should:

1. **Change the default password** immediately
   - Login with admin123
   - Change to a strong password
   - Store it securely

2. **Use strong passwords**
   - At least 12 characters
   - Mix of uppercase, lowercase, numbers, symbols
   - Avoid dictionary words

3. **Limit access**
   - Only share credentials with trusted admin staff
   - Don't write passwords in public places
   - Use password manager to store securely

4. **Monitor admin actions**
   - Keep audit logs
   - Review admin activity regularly
   - Set alerts for suspicious logins

5. **Two-Factor Authentication** (if implemented)
   - Enable if available
   - Adds extra security layer

---

## Admin URL Paths

| Function | URL |
|----------|-----|
| Admin Dashboard | `/admin/dashboard.php` |
| Products Management | `/admin/products.php` |
| Categories | `/admin/categories.php` |
| Orders | `/admin/orders.php` |
| Users | `/admin/users.php` |
| Messages | `/admin/messages.php` |
| Settings | `/admin/settings.php` |

---

## Testing the Admin Login

✅ Test Steps:
1. Visit https://fspo-ecommerce.onrender.com/login.php
2. Enter: admin@fspoltd.rw
3. Enter: admin123
4. Should redirect to /admin/dashboard.php
5. Dashboard should display properly
6. Should be able to see menu options

---

## Sample Admin Actions

Once logged in, try these:

**Add Product**:
1. Go to Products
2. Click "Add New Product"
3. Fill in details
4. Set price in RWF
5. Upload image
6. Click "Save"

**Manage Orders**:
1. Go to Orders
2. Click an order
3. Update status (pending → processing → shipped → delivered)
4. Add tracking number
5. Save

**View Messages**:
1. Go to Messages
2. See customer inquiries
3. Mark as read
4. Reply if needed

---

## Support

If you need to:
- **Reset password**: Use database update query above
- **Create more admins**: Use INSERT statement above
- **Check logs**: Look in `/logs/php-error.log`
- **Database access**: Use Render PostgreSQL credentials

---

## Next Steps

1. ✅ Login to admin panel
2. ✅ Verify everything works
3. ✅ Change default password to secure one
4. ✅ Add more admin users if needed
5. ✅ Start managing your store!

Your admin account is ready to manage the store! 🎉
