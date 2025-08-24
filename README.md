# 🧺 Laravel Service System - نظام الخدمات اللوجستية

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg)]()

> نظام متكامل لإدارة خدمات الغسيل والكي مع نظام عملات افتراضية وإدارة ذكية للمواقع

## 📋 جدول المحتويات

- [نظرة عامة](#نظرة-عامة)
- [المميزات](#المميزات)
- [المتطلبات](#المتطلبات)
- [التثبيت](#التثبيت)
- [الاستخدام](#الاستخدام)
- [API Documentation](#api-documentation)
- [المساهمة](#المساهمة)
- [الترخيص](#الترخيص)

---

## 🎯 نظرة عامة

نظام الخدمات اللوجستية هو منصة Laravel متقدمة تتيح للعملاء طلب خدمات الغسيل والكي من المغاسل والعملاء، مع نظام عملات افتراضية وإدارة ذكية للمواقع. النظام مصمم خصيصاً للسوق السعودي مع دعم كامل للمدن والمناطق.

### 🏗️ البنية التقنية

- **Backend**: Laravel 10.x
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum
- **API**: RESTful API
- **Architecture**: MVC Pattern
- **Testing**: PHPUnit

---

## ⭐ المميزات

### 🌍 نظام المواقع المتقدم
- دعم كامل للمدن السعودية
- خوارزمية Haversine لحساب المسافات
- فلترة جغرافية ذكية
- إدارة المناطق الخدمية

### 💰 نظام العملات الافتراضية
- حزم عملات متعددة المستويات
- نظام هدايا متكامل
- إدارة رصيد العملات
- تتبع المعاملات

### 🛍️ إدارة الخدمات
- أنواع خدمات متعددة (غسيل، كي، تنظيف جاف)
- نظام موافقة متقدم
- إدارة المخزون
- تقييمات ومراجعات

### �� واجهة API متقدمة
- RESTful API كامل
- مصادقة آمنة
- صلاحيات مرنة
- توثيق شامل

### 📊 لوحة تحكم متقدمة
- إحصائيات مفصلة
- تقارير مالية
- إدارة المستخدمين
- مراقبة النظام

---

## 🔧 المتطلبات

### متطلبات النظام
- **PHP**: 8.1 أو أحدث
- **Laravel**: 10.x
- **Database**: MySQL 8.0+ أو PostgreSQL 13+
- **Composer**: 2.0+
- **Node.js**: 16+ (للتطوير)

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
```

---

## 🚀 التثبيت

### 1. استنساخ المشروع
```bash
git clone https://github.com/your-username/laravel-service-system.git
cd laravel-service-system
```

### 2. تثبيت التبعيات
```bash
composer install
npm install
```

### 3. إعداد البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 4. إعداد قاعدة البيانات
```bash
# تعديل ملف .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_service_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. تشغيل الهجرات
```bash
php artisan migrate:fresh
```

### 6. ملء قاعدة البيانات
```bash
php artisan db:seed
```

### 7. إنشاء رابط رمزي
```bash
php artisan storage:link
```

### 8. تشغيل الخادم
```bash
php artisan serve
```

---

## 📖 الاستخدام

### 🔐 إنشاء حساب جديد
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "customer",
    "phone": "+966500000000",
    "city_id": 1
  }'
```

### 🏢 إضافة خدمة جديدة
```bash
curl -X POST http://localhost:8000/api/services \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Premium Washing",
    "description": "High-quality washing service",
    "coin_cost": 150,
    "quantity": 10,
    "type": "washing"
  }'
```

### 💰 شراء حزمة عملات
```bash
curl -X POST http://localhost:8000/api/packages/purchase \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "package_id": 1,
    "payment_method": "online"
  }'
```

---

## 📚 API Documentation

### 🔗 الروابط الأساسية
- **Base URL**: `http://localhost:8000/api`
- **Documentation**: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- **Postman Collection**: [Laravel_Service_System_Postman_Collection.json](Laravel_Service_System_Postman_Collection.json)

### 📱 نقاط النهاية الرئيسية

#### المصادقة
- `POST /auth/register` - إنشاء حساب جديد
- `POST /auth/login` - تسجيل الدخول
- `POST /auth/logout` - تسجيل الخروج
- `GET /auth/profile` - الملف الشخصي

#### الخدمات
- `GET /services` - قائمة الخدمات
- `POST /services` - إنشاء خدمة جديدة
- `PUT /services/{id}` - تحديث خدمة
- `DELETE /services/{id}` - حذف خدمة

#### الطلبات
- `GET /orders` - قائمة الطلبات
- `POST /orders/purchase-service` - شراء خدمة
- `PUT /orders/{id}/status` - تحديث حالة الطلب

#### العملات
- `GET /packages` - قائمة الحزم
- `POST /packages/purchase` - شراء حزمة
- `POST /packages/gift` - إرسال هدية

---

## 🧪 الاختبار

### تشغيل الاختبارات
```bash
# جميع الاختبارات
php artisan test

# اختبارات محددة
php artisan test --filter=UserTest

# اختبارات مع تغطية
php artisan test --coverage
```

### اختبارات API
```bash
# اختبار نقاط النهاية
php artisan test --filter=ApiTest

# اختبار المصادقة
php artisan test --filter=AuthTest
```

---

## 📊 قاعدة البيانات

### الجداول الرئيسية
- **users** - المستخدمون
- **cities** - المدن
- **laundries** - المغاسل
- **agents** - الوكلاء
- **customers** - العملاء
- **services** - الخدمات
- **orders** - الطلبات
- **packages** - حزم العملات
- **invoices** - الفواتير

### العلاقات
```
User (1) -> (1) Laundry/Agent/Customer
City (1) -> (N) Laundries/Agents/Customers
Service (N) -> (1) User (Provider)
Order (N) -> (1) User (Customer)
Order (N) -> (1) Service/Package
```

---

## 🔒 الأمان

### المصادقة
- Laravel Sanctum للتوكن
- تشفير كلمات المرور
- حماية من CSRF
- Rate Limiting

### الصلاحيات
- نظام أدوار مرن
- Middleware للتحكم
- التحقق من الملكية
- تسجيل العمليات

---

## 🚀 النشر

### إعداد الإنتاج
```bash
# تحسين الأداء
php artisan config:cache
php artisan route:cache
php artisan view:cache

# إعداد Cron Jobs
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### متطلبات الخادم
- **Web Server**: Nginx/Apache
- **PHP**: 8.1+ مع OPcache
- **Database**: MySQL 8.0+ أو PostgreSQL 13+
- **Redis**: للتخزين المؤقت (اختياري)

---

## 🤝 المساهمة

نرحب بمساهماتكم! يرجى اتباع الخطوات التالية:

### 1. Fork المشروع
### 2. إنشاء فرع للميزة الجديدة
```bash
git checkout -b feature/amazing-feature
```
### 3. Commit التغييرات
```bash
git commit -m 'Add amazing feature'
```
### 4. Push للفرع
```bash
git push origin feature/amazing-feature
```
### 5. إنشاء Pull Request

### معايير الكود
- اتبع PSR-12
- اكتب اختبارات للكود الجديد
- اكتب تعليقات واضحة
- تأكد من عمل جميع الاختبارات

---

## 📝 الترخيص

هذا المشروع مرخص تحت رخصة MIT. راجع ملف [LICENSE](LICENSE) للتفاصيل.

---

## 📞 الدعم

### معلومات الاتصال
- **البريد الإلكتروني**: support@laundry-system.com
- **المساعدة**: [Issues](https://github.com/your-username/laravel-service-system/issues)
- **التوثيق**: [Wiki](https://github.com/your-username/laravel-service-system/wiki)

### الموارد
- [دليل المستخدم](GUID.md)
- [API Documentation](API_DOCUMENTATION.md)
- [Postman Collection](Laravel_Service_System_Postman_Collection.json)

---

## 🙏 الشكر

شكر خاص لجميع المساهمين والمطورين الذين ساعدوا في تطوير هذا النظام.

---

*آخر تحديث: ديسمبر 2024*  
*الإصدار: 2.0*  
*المطور: فريق التطوير*
