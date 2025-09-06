# TradeHub - Sustainable Second-Hand Marketplace

TradeHub is a comprehensive second-hand marketplace platform built with PHP, MySQL, HTML, CSS, and JavaScript. It promotes sustainable consumption by enabling users to buy and sell pre-owned items while contributing to a circular economy.

## 🌟 Features

### Core Features
- **User Authentication**: Secure registration, login, email verification, and password reset
- **Product Management**: Create, read, update, and delete product listings
- **Advanced Search**: Filter by category, price range, condition, and location
- **Shopping Cart**: Add items to cart with quantity management
- **Favorites System**: Save products for later viewing
- **User Dashboard**: Comprehensive profile and listing management
- **Responsive Design**: Mobile-first approach with desktop optimization

### Advanced Features
- **Multi-image Support**: Upload and manage multiple product images
- **Condition-based Pricing**: New, Like New, Good, Fair, Poor conditions
- **Negotiable Pricing**: Option for price negotiations
- **Location-based Search**: Find products near you
- **Real-time Notifications**: Toast notifications for user actions
- **Security Features**: Password hashing, SQL injection prevention, XSS protection
- **Indian Rupee Support**: Native currency formatting and display

## 🚀 Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Server**: XAMPP (Apache, MySQL, PHP)
- **Icons**: Font Awesome 6.0
- **Fonts**: Inter (Google Fonts)

## 📁 Project Structure

```
TradeHub/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── main.js            # Main JavaScript file
├── config/
│   └── database.php           # Database configuration
├── includes/
│   ├── auth.php               # Authentication system
│   └── products.php           # Product management
├── api/
│   ├── products.php           # Product API endpoints
│   ├── cart.php               # Cart API endpoints
│   └── favorites.php          # Favorites API endpoints
├── database/
│   └── tradehub.sql           # Database schema
├── index.php                  # Homepage
├── login.php                  # Login page
├── register.php               # Registration page
├── product.php                # Product detail page
├── cart.php                   # Shopping cart page
├── add-product.php            # Add new product page
├── logout.php                 # Logout functionality
└── README.md                  # This file
```

## 🛠️ Installation & Setup

### Prerequisites
- XAMPP (Apache, MySQL, PHP)
- Web browser (Chrome, Firefox, Safari, Edge)

### Installation Steps

1. **Clone/Download the Project**
   ```bash
   # Extract the project to your XAMPP htdocs folder
   # Usually located at: C:\xampp\htdocs\TradeHub
   ```

2. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

3. **Database Setup**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `tradehub`
   - Import the SQL file: `database/tradehub.sql`

4. **Configure Database Connection**
   - Edit `config/database.php` if needed
   - Default settings work with XAMPP:
     - Host: localhost
     - Database: tradehub
     - Username: root
     - Password: (empty)

5. **Access the Application**
   - Open your browser
   - Navigate to: `http://localhost/TradeHub`

## 🎯 Usage Guide

### For Buyers
1. **Register/Login**: Create an account or sign in
2. **Browse Products**: Use search and filters to find items
3. **View Details**: Click on products to see full details
4. **Add to Cart**: Add items to your shopping cart
5. **Checkout**: Proceed to checkout (feature coming soon)

### For Sellers
1. **Create Account**: Register as a new user
2. **Add Products**: Use "Add Product" to list items
3. **Manage Listings**: View and edit your products
4. **Respond to Messages**: Communicate with potential buyers

## 🔧 Configuration

### Database Configuration
Edit `config/database.php` to match your database settings:

```php
private $host = 'localhost';
private $db_name = 'tradehub';
private $username = 'root';
private $password = '';
```

### Email Configuration (Optional)
To enable email verification, configure SMTP settings in the authentication system.

## 🎨 Customization

### Styling
- Main styles are in `assets/css/style.css`
- Uses CSS custom properties for easy theming
- Responsive design with mobile-first approach

### Features
- Add new product categories in the database
- Modify validation rules in PHP files
- Extend API endpoints for additional functionality

## 🔒 Security Features

- **Password Hashing**: Uses PHP's `password_hash()` function
- **SQL Injection Prevention**: Prepared statements with PDO
- **XSS Protection**: HTML escaping for user inputs
- **CSRF Protection**: Session-based token validation
- **Input Validation**: Server-side validation for all inputs

## 📱 Mobile Responsiveness

- Mobile-first CSS design
- Responsive grid layouts
- Touch-friendly interface elements
- Optimized for various screen sizes

## 🚀 Future Enhancements

### Planned Features
- **Real-time Messaging**: Chat between buyers and sellers
- **Payment Integration**: Secure payment processing
- **Advanced Search**: AI-powered recommendations
- **Analytics Dashboard**: Seller and admin analytics
- **Mobile App**: Native mobile applications
- **Push Notifications**: Real-time updates
- **Review System**: Product and seller ratings
- **Bidding System**: Auction-style selling

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure MySQL is running in XAMPP
   - Check database credentials in `config/database.php`
   - Verify database `tradehub` exists

2. **Page Not Loading**
   - Check if Apache is running
   - Verify file permissions
   - Check for PHP syntax errors

3. **Images Not Uploading**
   - Check file upload permissions
   - Verify image file formats
   - Ensure upload directory exists

### Debug Mode
Enable error reporting by adding to the top of PHP files:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📊 Database Schema

### Key Tables
- **users**: User accounts and profiles
- **products**: Product listings
- **categories**: Product categories
- **cart**: Shopping cart items
- **favorites**: User favorites
- **orders**: Order management
- **messages**: User communications
- **notifications**: System notifications

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available under the MIT License.

## 📞 Support

For support and questions:
- Email: support@tradehub.com
- Phone: +91 98765 43210
- Address: Mumbai, India

## 🙏 Acknowledgments

- Font Awesome for icons
- Google Fonts for typography
- XAMPP for local development environment
- PHP and MySQL communities for excellent documentation

---

**TradeHub** - Empowering Sustainable Consumption through Second-Hand Trading

*Built with ❤️ for a greener future*
