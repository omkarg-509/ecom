# Simple E-Commerce Website

A beginner-level final year CS project: a simple e-commerce website built with core PHP, HTML, CSS, JavaScript, and MySQLi.

## Features

- **Homepage** with product listing from database
- **User registration & login** with PHP sessions
- **Product detail page** with full product information
- **Shopping cart** functionality (session-based)
- **Checkout process** that stores orders in database (no payment gateway)
- **Basic admin panel** for product management
- **Responsive design** with clean CSS
- **Interactive JavaScript** features

## Technologies Used

- **Backend**: Core PHP (no frameworks), MySQLi
- **Frontend**: HTML5, CSS3, JavaScript
- **Database**: MySQL
- **Session Management**: PHP Sessions
- **Architecture**: MVC-inspired structure

## Project Structure

```
ecom/
├── config/
│   └── database.php          # Database connection configuration
├── css/
│   └── style.css            # Main stylesheet
├── js/
│   └── main.js              # JavaScript functionality
├── includes/
│   ├── header.php           # Common header template
│   ├── footer.php           # Common footer template
│   └── functions.php        # Utility functions
├── images/                  # Product images directory
├── database/
│   └── ecommerce.sql        # Database schema and sample data
├── admin/
│   └── index.php            # Basic admin dashboard
├── index.php                # Homepage with products
├── register.php             # User registration
├── login.php                # User login
├── logout.php               # User logout
├── product.php              # Product detail page
├── cart.php                 # Shopping cart
└── checkout.php             # Checkout process
```

## Installation & Setup

### 1. Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Web browser

### 2. Database Setup
1. Create a new MySQL database named `ecommerce`
2. Import the database schema:
   ```sql
   mysql -u your_username -p ecommerce < database/ecommerce.sql
   ```

### 3. Configuration
1. Update database credentials in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'ecommerce');
   ```

### 4. Web Server Setup
1. Copy the project files to your web server directory (e.g., `htdocs`, `www`, or `public_html`)
2. Ensure the web server has read/write permissions for the project directory
3. Make sure PHP sessions are enabled

### 5. Admin Access
- Create a user with username "admin" to access the admin panel
- Admin panel is available at `/admin/index.php`

## Database Schema

### Users Table
- `id` - Primary key
- `username` - Unique username
- `email` - User email address
- `password` - Hashed password
- `created_at` - Registration timestamp

### Products Table
- `id` - Primary key
- `name` - Product name
- `description` - Product description
- `price` - Product price (decimal)
- `image` - Image filename
- `stock` - Available quantity
- `created_at` - Creation timestamp

### Orders Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `total_amount` - Order total amount
- `status` - Order status (pending, completed, etc.)
- `created_at` - Order timestamp

### Order Items Table
- `id` - Primary key
- `order_id` - Foreign key to orders table
- `product_id` - Foreign key to products table
- `quantity` - Item quantity
- `price` - Item price at time of order

## Usage

### For Customers:
1. **Browse Products**: Visit the homepage to see available products
2. **Register/Login**: Create an account or login to existing account
3. **View Product Details**: Click on any product to see full details
4. **Add to Cart**: Add products to your shopping cart
5. **Checkout**: Complete your order (simulation only, no payment processed)

### For Admins:
1. **Login**: Use admin credentials to access admin panel
2. **Manage Products**: Add, edit, or delete products
3. **View Orders**: Monitor customer orders
4. **Dashboard**: View site statistics and quick actions

## Security Features

- **Password Hashing**: All passwords are hashed using PHP's `password_hash()`
- **Input Sanitization**: All user inputs are sanitized to prevent XSS
- **Prepared Statements**: Database queries use prepared statements to prevent SQL injection
- **Session Management**: Secure PHP session handling
- **CSRF Protection**: Basic protection against cross-site request forgery

## Learning Objectives

This project is designed for beginners to learn:
- **PHP Fundamentals**: Variables, functions, classes, sessions
- **Database Integration**: MySQLi usage, CRUD operations
- **Web Security**: Input validation, password hashing, SQL injection prevention
- **Frontend Technologies**: HTML5, CSS3, JavaScript, responsive design
- **Project Structure**: Organizing code, separation of concerns
- **Session Management**: Shopping cart, user authentication
- **Full-Stack Development**: Complete web application development

## Potential Enhancements

For advanced learning, consider adding:
- Payment gateway integration
- Email notifications
- Advanced admin features
- Product categories and search
- User profile management
- Order history and tracking
- Image upload functionality
- Advanced security features
- API development
- Modern frontend framework integration

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

This is an educational project. Feel free to fork, modify, and use for learning purposes.

## Support

This project is meant for educational purposes and basic learning of web development concepts using core PHP and related technologies.
