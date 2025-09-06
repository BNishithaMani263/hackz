# XAMPP Setup Guide for TradeHub

This guide will help you set up TradeHub on your local XAMPP environment.

## Prerequisites

- Windows 10/11, macOS, or Linux
- XAMPP installed (Download from https://www.apachefriends.org/)
- Web browser (Chrome, Firefox, Safari, or Edge)

## Step-by-Step Installation

### 1. Install XAMPP

1. Download XAMPP from the official website
2. Run the installer as administrator
3. Select the following components:
   - Apache
   - MySQL
   - PHP
   - phpMyAdmin
4. Complete the installation

### 2. Start XAMPP Services

1. Open XAMPP Control Panel
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Both services should show "Running" status

### 3. Setup TradeHub Project

1. Copy the TradeHub folder to your XAMPP htdocs directory:
   - Windows: `C:\xampp\htdocs\TradeHub`
   - macOS: `/Applications/XAMPP/htdocs/TradeHub`
   - Linux: `/opt/lampp/htdocs/TradeHub`

2. Ensure the folder structure looks like this:
   ```
   htdocs/TradeHub/
   ├── assets/
   ├── config/
   ├── includes/
   ├── api/
   ├── database/
   ├── index.php
   └── other PHP files...
   ```

### 4. Create Database

1. Open your web browser
2. Navigate to `http://localhost/phpmyadmin`
3. Click "New" to create a new database
4. Name it `tradehub`
5. Select "utf8_general_ci" as collation
6. Click "Create"

### 5. Import Database Schema

1. In phpMyAdmin, select the `tradehub` database
2. Click the "Import" tab
3. Click "Choose File" and select `database/tradehub.sql`
4. Click "Go" to import the database

### 6. Configure Database Connection (if needed)

1. Open `config/database.php`
2. Verify the database settings:
   ```php
   private $host = 'localhost';
   private $db_name = 'tradehub';
   private $username = 'root';
   private $password = '';
   ```
3. Save the file

### 7. Access TradeHub

1. Open your web browser
2. Navigate to `http://localhost/TradeHub`
3. You should see the TradeHub homepage

## Testing the Installation

### 1. Test Database Connection
- Try registering a new user account
- If successful, the database connection is working

### 2. Test File Permissions
- Try uploading a product image
- If it fails, check folder permissions

### 3. Test All Features
- User registration and login
- Product listing creation
- Search and filtering
- Cart functionality
- Favorites system

## Troubleshooting

### Common Issues

**Apache won't start:**
- Check if port 80 is in use
- Run XAMPP as administrator
- Change Apache port in XAMPP settings

**MySQL won't start:**
- Check if port 3306 is in use
- Run XAMPP as administrator
- Check MySQL logs for errors

**Database connection error:**
- Verify database name is `tradehub`
- Check username and password in config
- Ensure MySQL is running

**Page shows errors:**
- Check PHP error logs
- Enable error reporting in PHP files
- Verify file permissions

**Images not uploading:**
- Check upload directory permissions
- Verify PHP upload settings
- Check file size limits

### Error Logs

**Apache Error Log:**
- Windows: `C:\xampp\apache\logs\error.log`
- macOS: `/Applications/XAMPP/logs/error_log`
- Linux: `/opt/lampp/logs/error_log`

**PHP Error Log:**
- Windows: `C:\xampp\php\logs\php_error_log`
- macOS: `/Applications/XAMPP/logs/php_error_log`
- Linux: `/opt/lampp/logs/php_error_log`

## Development Tips

### 1. Enable Error Reporting
Add this to the top of PHP files for debugging:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### 2. Check PHP Settings
- Upload max filesize: 10M or higher
- Post max size: 10M or higher
- Memory limit: 128M or higher

### 3. Database Management
- Use phpMyAdmin for database management
- Regular backups recommended
- Monitor database size

### 4. File Permissions
- Ensure web server can read all files
- Upload directories need write permissions
- Log files need write permissions

## Security Considerations

### For Development Only
- This setup is for local development only
- Do not use in production without proper security measures
- Change default passwords
- Enable HTTPS in production
- Use environment variables for sensitive data

### Production Deployment
- Use a proper web server (Apache/Nginx)
- Configure SSL certificates
- Set up proper database security
- Implement proper user authentication
- Regular security updates

## Support

If you encounter issues:

1. Check the error logs
2. Verify all steps were followed
3. Check XAMPP documentation
4. Search online for specific error messages
5. Contact support if needed

## Next Steps

Once TradeHub is running:

1. Create your first user account
2. Add some sample products
3. Test all features
4. Customize the design
5. Add additional features

Happy coding! 🚀
