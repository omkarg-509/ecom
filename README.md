# E-commerce Website

A simple e-commerce website built with core PHP, HTML, CSS, JavaScript, and MySQLi. This project is designed as a beginner-level final year CS project.

## Features

- **Homepage**: Product listing with data from MySQL database
- **User Management**: Registration and login system with PHP sessions
- **Product Details**: Individual product pages with detailed information
- **Shopping Cart**: Session-based cart management
- **Checkout**: Simple order processing (no payment integration)
- **Responsive Design**: Mobile-friendly interface

## Technologies Used

- **Backend**: Core PHP (no frameworks), MySQLi
- **Frontend**: HTML5, CSS3, JavaScript
- **Database**: MySQL
- **Session Management**: PHP Sessions
- **Security**: Password hashing, SQL prepared statements

## Setup Instructions

### 1. Database Setup
1. Create a MySQL database named `ecommerce`
2. Import the database schema from `database/ecommerce.sql`
3. Update database credentials in `config/database.php` if needed

### 2. Web Server Setup
1. Place the project files in your web server directory (e.g., `htdocs` for XAMPP)
2. Ensure PHP and MySQL are running
3. Access the website through your web browser

### 3. Configuration
- Database settings: Edit `config/database.php`
- Session settings: Modify `config/session.php` if needed

## File Structure

```
/
├── index.php           # Homepage with product listing
├── login.php          # User login
├── register.php       # User registration  
├── product.php        # Product detail page
├── cart.php           # Shopping cart
├── checkout.php       # Checkout process
├── logout.php         # Logout functionality
├── config/
│   ├── database.php   # Database connection
│   └── session.php    # Session management
├── includes/
│   ├── header.php     # Common header
│   └── footer.php     # Common footer
├── css/
│   └── style.css      # Main stylesheet
├── js/
│   └── script.js      # JavaScript functionality
├── images/            # Product images
└── database/
    └── ecommerce.sql  # Database schema
```

## Database Schema

### Tables
- **users**: User accounts and authentication
- **products**: Product catalog
- **orders**: Order information
- **order_items**: Individual items in each order

## Key Features Implementation

### Security Features
- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- Input validation and sanitization

### User Experience
- Responsive design for all devices
- AJAX cart operations
- Form validation
- User-friendly error messages
- Clean, intuitive interface

## Default Sample Products

The system comes with 6 sample products:
- Laptop Computer ($899.99)
- Smartphone ($599.99)  
- Wireless Headphones ($149.99)
- Coffee Maker ($79.99)
- Running Shoes ($120.00)
- Backpack ($49.99)

## Usage

1. **Browse Products**: Visit the homepage to see available products
2. **Register**: Create a new user account
3. **Login**: Sign in to your account
4. **Shop**: Add products to cart, view details
5. **Checkout**: Complete your order with shipping information

## Note

This is a demonstration e-commerce website. No actual payments are processed, and no real transactions take place. It's designed for educational purposes and as a foundation for learning web development concepts.

## License

This project is open source and available under the [MIT License](LICENSE).
