# 🚀 دليل النشر - نظام الخدمات اللوجستية

## 📋 جدول المحتويات

1. [المتطلبات](#المتطلبات)
2. [إعداد الخادم](#إعداد-الخادم)
3. [نشر المشروع](#نشر-المشروع)
4. [إعداد قاعدة البيانات](#إعداد-قاعدة-البيانات)
5. [إعداد البيئة](#إعداد-البيئة)
6. [إعداد الخادم](#إعداد-الخادم-1)
7. [إعداد SSL](#إعداد-ssl)
8. [إعداد Cron Jobs](#إعداد-cron-jobs)
9. [النسخ الاحتياطي](#النسخ-الاحتياطي)
10. [المراقبة](#المراقبة)

---

## 🔧 المتطلبات

### متطلبات النظام
- **OS**: Ubuntu 20.04+ أو CentOS 8+
- **PHP**: 8.1 أو أحدث
- **Web Server**: Nginx أو Apache
- **Database**: MySQL 8.0+ أو PostgreSQL 13+
- **Redis**: 6.0+ (اختياري للتخزين المؤقت)

### متطلبات PHP
```bash
php >= 8.1
ext-ctype
ext-json
ext-mbstring
ext-openssl
ext-pdo
ext-tokenizer
ext-xml
ext-fileinfo
ext-curl
ext-gd
ext-zip
```

---

## 🖥️ إعداد الخادم

### 1. تحديث النظام
```bash
sudo apt update && sudo apt upgrade -y
```

### 2. تثبيت PHP
```bash
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-curl php8.1-gd php8.1-mbstring php8.1-zip php8.1-bcmath
```

### 3. تثبيت Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 4. تثبيت Node.js
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

---

## 📦 نشر المشروع

### 1. استنساخ المشروع
```bash
cd /var/www
sudo git clone https://github.com/your-username/laravel-service-system.git
sudo chown -R www-data:www-data laravel-service-system
cd laravel-service-system
```

### 2. تثبيت التبعيات
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 3. إعداد الصلاحيات
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 🗄️ إعداد قاعدة البيانات

### 1. إنشاء قاعدة البيانات
```sql
CREATE DATABASE laravel_service_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON laravel_service_system.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. تشغيل الهجرات
```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## ⚙️ إعداد البيئة

### 1. نسخ ملف البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 2. تعديل ملف .env
```env
APP_NAME="Laravel Service System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_service_system
DB_USERNAME=laravel_user
DB_PASSWORD=strong_password_here

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. تحسين الأداء
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🌐 إعداد الخادم

### 1. إعداد Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/laravel-service-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 2. إعداد PHP-FPM
```ini
; /etc/php/8.1/fpm/php.ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 512M
```

---

## 🔒 إعداد SSL

### 1. تثبيت Certbot
```bash
sudo apt install certbot python3-certbot-nginx
```

### 2. الحصول على شهادة SSL
```bash
sudo certbot --nginx -d yourdomain.com
```

### 3. إعداد التجديد التلقائي
```bash
sudo crontab -e
# إضافة السطر التالي
0 12 * * * /usr/bin/certbot renew --quiet
```

---

## ⏰ إعداد Cron Jobs

### 1. إضافة Cron Job
```bash
sudo crontab -e
```

### 2. إضافة المهام
```bash
# تشغيل Laravel Scheduler
* * * * * cd /var/www/laravel-service-system && php artisan schedule:run >> /dev/null 2>&1

# تنظيف الملفات المؤقتة
0 2 * * * cd /var/www/laravel-service-system && php artisan cache:clear >> /dev/null 2>&1

# النسخ الاحتياطي لقاعدة البيانات
0 3 * * * mysqldump -u laravel_user -p'password' laravel_service_system > /backup/db_$(date +\%Y\%m\%d).sql
```

---

## 💾 النسخ الاحتياطي

### 1. إنشاء مجلد النسخ الاحتياطي
```bash
sudo mkdir -p /backup
sudo chown www-data:www-data /backup
```

### 2. سكريبت النسخ الاحتياطي
```bash
#!/bin/bash
# /var/www/laravel-service-system/backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup"
PROJECT_DIR="/var/www/laravel-service-system"

# نسخ احتياطي لقاعدة البيانات
mysqldump -u laravel_user -p'password' laravel_service_system > $BACKUP_DIR/db_$DATE.sql

# نسخ احتياطي للملفات
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $PROJECT_DIR .

# حذف النسخ الاحتياطية القديمة (احتفظ بآخر 7 نسخ)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

### 3. جعل السكريبت قابل للتنفيذ
```bash
chmod +x /var/www/laravel-service-system/backup.sh
```

---

## 📊 المراقبة

### 1. تثبيت أدوات المراقبة
```bash
sudo apt install htop iotop nethogs
```

### 2. مراقبة السجلات
```bash
# مراقبة سجلات Laravel
tail -f /var/www/laravel-service-system/storage/logs/laravel.log

# مراقبة سجلات Nginx
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# مراقبة سجلات PHP-FPM
tail -f /var/log/php8.1-fpm.log
```

### 3. إعداد التنبيهات
```bash
# إرسال تنبيه عند انخفاض المساحة
df -h | awk '$5 > "80%" {print "Warning: Disk space is running low on " $1}'

# إرسال تنبيه عند ارتفاع استخدام الذاكرة
free -m | awk 'NR==2{if ($3/$2 * 100 > 80) print "Warning: Memory usage is high"}'
```

---

## 🔧 استكشاف الأخطاء

### مشاكل شائعة

#### 1. خطأ في الصلاحيات
```bash
sudo chown -R www-data:www-data /var/www/laravel-service-system
sudo chmod -R 775 storage bootstrap/cache
```

#### 2. خطأ في قاعدة البيانات
```bash
php artisan config:clear
php artisan cache:clear
```

#### 3. خطأ في التخزين
```bash
php artisan storage:link
sudo chmod -R 775 storage
```

---

## 📈 تحسين الأداء

### 1. إعداد OPcache
```ini
; /etc/php/8.1/fpm/conf.d/10-opcache.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 2. إعداد Redis
```bash
sudo apt install redis-server
sudo systemctl enable redis-server
```

### 3. ضغط Gzip
```nginx
# في إعدادات Nginx
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;
```

---

## 🚀 النشر المستمر

### 1. سكريبت النشر
```bash
#!/bin/bash
# deploy.sh

echo "Starting deployment..."

# الانتقال إلى مجلد المشروع
cd /var/www/laravel-service-system

# جلب التحديثات
git pull origin main

# تثبيت التبعيات
composer install --optimize-autoloader --no-dev

# بناء الأصول
npm run build

# تشغيل الهجرات
php artisan migrate --force

# مسح الكاش
php artisan config:cache
php artisan route:cache
php artisan view:cache

# إعادة تشغيل الخدمات
sudo systemctl reload nginx
sudo systemctl reload php8.1-fpm

echo "Deployment completed!"
```

### 2. جعل السكريبت قابل للتنفيذ
```bash
chmod +x deploy.sh
```

---

## 📞 الدعم

### معلومات الاتصال
- **البريد الإلكتروني**: devops@laundry-system.com
- **التوثيق**: [Wiki](https://github.com/your-username/laravel-service-system/wiki)
- **المساعدة**: [Issues](https://github.com/your-username/laravel-service-system/issues)

---

*آخر تحديث: ديسمبر 2024*  
*الإصدار: 2.0*  
*المطور: فريق التطوير*
