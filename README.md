# Lions Design E-commerce Website

A complete e-commerce website for Lions Design, featuring premium gifts, awards, and custom printing solutions.

## Features

### Customer Features
- **Browse Products**: View all products without registration
- **User Registration & Login**: Secure user authentication system
- **Shopping Cart**: Add, update, and remove items
- **Checkout Process**: Complete order placement with MTN Mobile Money integration
- **Order History**: View past orders and their status
- **Product Categories**: Browse by Crystal Awards, Printing Services, and Gift Items

### Admin Features
- **Product Management**: Add, edit, and delete products
- **Order Management**: View and update order status
- **User Management**: View and manage customer accounts
- **Category Management**: Organize products by categories
- **Dashboard**: Overview of sales, orders, and users

### Technical Features
- **Responsive Design**: Mobile-friendly Bootstrap layout
- **Secure Authentication**: Password hashing and session management
- **Database Integration**: MySQL with PDO for secure queries
- **File Upload**: Product image management
- **AJAX Integration**: Dynamic cart updates and search
- **MTN Mobile Money**: Payment integration (placeholder)

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Server**: XAMPP (Apache + MySQL + PHP)
- **Payment**: MTN Mobile Money API (placeholder)

## Installation & Setup

### Prerequisites
- XAMPP installed and running
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Step 1: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `liondesign_db`
3. Import the database schema from `database/schema.sql`

### Step 2: File Setup
1. Place all files in `xampp/htdocs/liondesign/`
2. Ensure proper file permissions (readable by web server)

### Step 3: Configuration
1. Open `config/database.php`
2. Verify database connection settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'liondesign_db');
   ```

### Step 4: Test Installation
1. Start XAMPP (Apache and MySQL)
2. Navigate to `http://localhost/liondesign/`
3. You should see the homepage with featured products

## Default Admin Account

- **Email**: admin@liondesign.com
- **Password**: admin123

## File Structure

```
liondesign/
├── admin/                 # Admin panel files
│   ├── index.php         # Admin dashboard
│   ├── products.php      # Product management
│   ├── orders.php        # Order management
│   └── users.php         # User management
├── ajax/                  # AJAX handlers
│   ├── cart_actions.php  # Cart operations
│   └── get_cart_count.php # Cart count
├── assets/               # Static assets
│   ├── css/
│   │   └── style.css     # Custom styles
│   ├── js/
│   │   └── main.js       # Main JavaScript
│   └── images/           # Product images
├── config/
│   └── database.php      # Database configuration
├── database/
│   └── schema.sql        # Database schema
├── includes/
│   ├── functions.php     # Helper functions
│   ├── header.php        # Site header
│   └── footer.php        # Site footer
├── index.php             # Homepage
├── shop.php              # Product catalog
├── cart.php              # Shopping cart
├── checkout.php          # Checkout process
├── login.php             # User login
├── register.php          # User registration
├── logout.php            # User logout
└── README.md             # This file
```

## Product Categories

1. **Crystal Awards**
   - Premium crystal awards and recognition items
   - Executive trophies and achievement awards

2. **Printing Services**
   - Custom banners and promotional materials
   - T-shirts and branded merchandise
   - Large format printing

3. **Gift Items**
   - Coffee mugs and drinkware
   - Corporate umbrellas
   - Key holders and accessories
   - Gift sets and packages

## Payment Integration

The system includes a placeholder for MTN Mobile Money integration. To implement real payment processing:

1. Replace the `processMTNPayment()` function in `includes/functions.php`
2. Add your MTN Mobile Money API credentials
3. Implement proper payment validation and confirmation

## Customization

### Colors
The website uses a custom color scheme defined in `assets/css/style.css`:
- Primary: Black (#000000)
- Secondary: White (#ffffff)
- Success: Green (#28a745)
- Warning: Yellow (#ffc107)
- Danger: Red (#dc3545)

### Adding Products
1. Login to admin panel
2. Navigate to Products section
3. Click "Add Product"
4. Fill in product details and upload image
5. Save product

### Modifying Categories
1. Access the database directly or create admin interface
2. Categories are stored in the `categories` table
3. Update category names, descriptions, and slugs

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with PDO prepared statements
- XSS protection with `htmlspecialchars()`
- Session-based authentication
- Admin access control

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify XAMPP is running
   - Check database credentials in `config/database.php`
   - Ensure database `liondesign_db` exists

2. **Images Not Loading**
   - Check file permissions on `assets/images/` directory
   - Verify image paths in database
   - Ensure proper file upload settings in PHP

3. **Session Issues**
   - Check PHP session configuration
   - Verify session storage directory permissions
   - Clear browser cookies if needed

4. **Admin Access Denied**
   - Verify admin user exists in database
   - Check `is_admin` field is set to 1
   - Ensure proper session handling

### Performance Optimization

1. **Database Indexing**
   - Add indexes on frequently queried columns
   - Optimize product search queries

2. **Image Optimization**
   - Compress product images
   - Use appropriate image formats (JPEG for photos, PNG for graphics)

3. **Caching**
   - Implement browser caching for static assets
   - Consider database query caching for product listings

## Support

For technical support or customization requests, please contact the development team.

## License

This project is proprietary software developed for Lions Design. All rights reserved.

## Version History

- **v1.0.0** - Initial release with basic e-commerce functionality
- Complete product management system
- User authentication and registration
- Shopping cart and checkout process
- Admin panel with dashboard
- MTN Mobile Money integration placeholder 