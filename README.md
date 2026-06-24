# 🤖 PanelBot - Telegram BGMI Key Generator Bot

A powerful Telegram bot for generating and managing BGMI (PUBG Mobile) license keys with full admin panel functionality.

## ✨ Features

### 🔑 Key Management
- Generate license keys with custom durations (2 hours to 60 days)
- Create custom keys with specific names
- Reset and delete keys
- Track key usage and status
- Rate limiting to prevent abuse

### 👥 User Management
- Add/remove authorized users
- Owner-only admin commands
- User access control
- Activity logging

### 📊 Statistics & Monitoring
- Real-time bot statistics
- Key generation tracking
- User activity monitoring
- Mod status control

### 🛡️ Security Features
- Database-driven authentication
- Rate limiting
- Input validation
- SQL injection protection
- Owner verification system

## 🚀 Quick Setup

### Prerequisites
- Web server with PHP 7.4+ and MySQL
- Telegram Bot Token (get from [@BotFather](https://t.me/BotFather))
- Your Telegram User ID (get from [@userinfobot](https://t.me/userinfobot))

### Installation Steps

1. **Clone/Download Files**
   ```bash
   # Upload these files to your web server:
   # - bot.php
   # - auth.php  
   # - setup.php
   ```

2. **Run Setup**
   ```bash
   php setup.php
   ```
   Follow the prompts to configure:
   - Bot Token
   - Owner ID  
   - Database credentials
   - Webhook URL

3. **Set Webhook**
   The setup script will automatically set your webhook, or you can do it manually:
   ```
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://yourdomain.com/bot.php
   ```

4. **Test Bot**
   Send `/start` to your bot to verify it's working!

## 🎮 Usage

### For Regular Users
- `/start` - Start the bot and see main menu
- `🔑 Generate Key` - Generate a license key
- `📞 Contact Support` - Get support information

### For Owner (Admin Commands)
- `/adduser username fullname` - Add new user
- `/removeuser username` - Remove user  
- `/listusers` - List all users
- `/stats` - Show bot statistics
- `/createkey KEYNAME HOURS` - Create custom key
- `/deletekey KEYNAME` - Delete specific key
- `/showkeys` - Show all generated keys
- `/resetkey KEYNAME` - Reset specific key
- `/resetallkeys` - Reset all keys
- `/modstatus` - Toggle mod status
- `/modinfo` - Show mod information
- `/settoken NEWTOKEN` - Update bot token

## 📁 File Structure

```
panelbot/
├── bot.php          # Main bot logic
├── auth.php         # Authentication & database config
├── setup.php        # Initial setup script
└── README.md        # This file
```

## 🗄️ Database Structure

The bot automatically creates these tables:

- `users` - Authorized bot users
- `keys_code` - Generated license keys
- `onoff` - Mod status control
- `modname` - Mod name configuration
- `_ftext` - Feature text content
- `activity_log` - User activity tracking
- `rate_limits` - Rate limiting data

## ⚙️ Configuration

### Bot Settings (auth.php)
```php
$botToken = 'YOUR_BOT_TOKEN';
$ownerID = 'YOUR_TELEGRAM_ID';
```

### Database Settings
```php
$db_host = 'localhost';
$db_username = 'root';
$db_password = 'your_password';
$db_name = 'panelbot_db';
```

### Rate Limiting
```php
$rate_limit = [
    'key_generation' => [
        'max_requests' => 5,
        'time_window' => 3600 // 1 hour
    ]
];
```

## 🔒 Security Features

- **Owner Verification**: Only the configured owner can access admin commands
- **User Authorization**: Regular users must be added by the owner
- **Rate Limiting**: Prevents spam and abuse
- **Input Validation**: All user inputs are sanitized
- **SQL Protection**: Prepared statements prevent SQL injection

## 🛠️ Customization

### Adding New Key Durations
Edit the `$🎚️` array in bot.php:
```php
$🎚️ = [
    '2 Hours' => 2,
    '5 Hours' => 5,
    // Add more durations here
];
```

### Modifying Messages
Update message texts in the respective functions in bot.php.

### Database Schema Changes
Modify the table creation queries in auth.php for custom fields.

## 🐛 Troubleshooting

### Common Issues

1. **"Database connection failed"**
   - Check database credentials in auth.php
   - Ensure MySQL server is running
   - Verify database exists

2. **"Webhook not set"**  
   - Check your webhook URL is accessible
   - Verify SSL certificate (HTTPS required)
   - Confirm bot token is correct

3. **"Access denied"**
   - Make sure you've added yourself as a user
   - Verify your Telegram ID is correct
   - Check owner ID configuration

4. **"Bot not responding"**
   - Check webhook is set correctly
   - Verify files are uploaded to correct directory
   - Check PHP error logs

### Debug Mode
Enable PHP error reporting by adding to bot.php:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📞 Support

For technical support:
- Check the troubleshooting section
- Review PHP error logs
- Verify database connection
- Test webhook URL manually

## 📄 License

This project is open source. Feel free to modify and distribute according to your needs.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests or report issues.

---

**⚠️ Important Security Notes:**
- Keep your bot token secure and never share it
- Regularly backup your database
- Monitor bot activity for unusual behavior
- Update PHP and database software regularly

**🎉 Enjoy using PanelBot!**