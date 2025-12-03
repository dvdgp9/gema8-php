# Gema∞ - Language Learning App

A lightweight, fast PHP application for language learning with AI-powered translations and tips.

## Features

- **🌐 Translator**: Bidirectional translation between English and 30+ languages
- **💡 Daily Tips**: AI-generated language tips tailored to your progress level
- **🗣️ Whispers**: Situational phrase generator for real-world conversations
- **❓ Language Doubts**: Ask questions about any language and get expert answers
- **📜 Echoes**: Translation history with repetition tracking
- **💳 Credit System**: Fair usage with credit-based access
- **🔐 Auth**: Secure authentication with password reset

## Tech Stack

- **Backend**: PHP 7.4+ (no framework)
- **Database**: MySQL 5.7+ / MariaDB 10.2+
- **Frontend**: Tailwind CSS (CDN), Vanilla JavaScript
- **AI**: Google Gemini API
- **Icons**: Lucide Icons

## Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Apache with mod_rewrite (or nginx)
- cURL extension enabled
- PDO MySQL extension

## Installation

### 1. Clone or Download

```bash
git clone https://github.com/yourusername/gema8.git
cd gema8
```

### 2. Database Setup

Create a MySQL database and import the schema:

```bash
mysql -u root -p
CREATE DATABASE gema8 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gema8;
SOURCE config/schema.sql;
```

### 3. Configuration

Copy the config file and update with your values:

```bash
cp config/config.example.php config/config.php
```

Edit `config/config.php`:

```php
// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'gema8');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');

// API Key
define('GEMINI_API_KEY', 'your_gemini_api_key');

// Base URL (no trailing slash)
define('BASE_URL', 'https://yourdomain.com/gema8');
```

### 4. Set Permissions (if needed)

```bash
chmod 755 public/
```

### 5. Apache Configuration

Ensure `mod_rewrite` is enabled and `AllowOverride All` is set for your directory.

For virtual host:

```apache
<VirtualHost *:80>
    ServerName gema8.yourdomain.com
    DocumentRoot /path/to/gema8/public
    
    <Directory /path/to/gema8/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## cPanel Deployment

1. **Upload files** to your subdomain folder (e.g., `public_html/gema8/`)
2. **Create MySQL database** via cPanel > MySQL Databases
3. **Import schema** via phpMyAdmin
4. **Update config.php** with database credentials and your URL
5. **Set environment** to `production`:
   ```php
   define('ENV', 'production');
   ```

## File Structure

```
gema8/
├── config/
│   ├── config.php          # Main configuration
│   ├── config.example.php  # Example config (commit this)
│   ├── database.php        # PDO connection class
│   └── schema.sql          # Database schema
├── controllers/
│   ├── Controller.php      # Base controller
│   ├── AuthController.php  # Authentication
│   ├── DashboardController.php
│   ├── HistoryController.php
│   ├── WhisperController.php
│   ├── AccountController.php
│   └── ApiController.php   # API endpoints
├── models/
│   ├── User.php
│   ├── Profile.php
│   ├── Translation.php
│   ├── Whisper.php
│   └── Tip.php
├── views/
│   ├── layouts/main.php    # Main layout
│   ├── partials/           # Reusable components
│   ├── auth/               # Login/register views
│   ├── dashboard/          # Main dashboard
│   ├── history/            # Translation history
│   ├── whispers/           # Whispers list
│   ├── account/            # Account settings
│   └── errors/             # Error pages
├── includes/
│   ├── helpers.php         # Helper functions
│   ├── security.php        # CSRF, validation
│   ├── session.php         # Session management
│   ├── auth.php            # Auth helpers
│   └── gemini.php          # Gemini API client
├── public/
│   ├── index.php           # Entry point
│   ├── .htaccess           # URL rewriting
│   ├── css/                # Custom styles
│   ├── js/                 # Custom scripts
│   └── assets/             # Images, etc.
└── README.md
```

## API Endpoints

All API endpoints require authentication and CSRF token.

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/translate` | Translate text |
| POST | `/api/ask-question` | Ask language question |
| POST | `/api/generate-whisper` | Generate situational phrases |
| POST | `/api/generate-tip` | Generate daily tip |
| POST | `/api/delete-translation` | Delete translation |
| POST | `/api/delete-whisper` | Delete whisper |

## Security Features

- Password hashing with bcrypt
- CSRF protection on all forms
- Prepared statements (no SQL injection)
- XSS prevention via output escaping
- Rate limiting on auth endpoints
- Secure session configuration

## Getting a Gemini API Key

1. Go to [Google AI Studio](https://aistudio.google.com/)
2. Click "Get API Key"
3. Create a new key or use existing
4. Add to your `config.php`

## License

MIT License - feel free to use and modify.

## Credits

Built with ❤️ for language learners worldwide.
