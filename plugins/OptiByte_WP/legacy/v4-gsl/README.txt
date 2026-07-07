OptiByte PRO v4 - Modular Image Optimization System

Included modules:
- OptiByteConfig.php       → Centralized configuration (paths, token, settings)
- OptiByteQueue.php        → Queue handling class
- OptiByteLog.php          → Success/failure logging class
- OptiByteScanner.php      → Queue scanner logic
- OptiByteUIHelper.php     → HTML formatting for status/size/date
- OptiByteDashboard.php    → Full admin dashboard controller
- optibyte-optimizer.php   → Cron/CLI optimizer script
- index.php                → Web dashboard UI interface

Paths assume deployment at:
- /var/www/shared/lib/ (for all classes)
- /var/www/api.[retired-host]/public/cron/
- /var/www/optibyte.[retired-host]/public/ui/

Visit: https://optibyte.[retired-host]/ui/?token=your_secure_token_here
