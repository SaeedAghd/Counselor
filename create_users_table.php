<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();

// حذف جدول قبلی اگر وجود دارد
$db->query("DROP TABLE IF EXISTS users");

// ایجاد جدول جدید users
$sql = "CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci";

if ($db->query($sql)) {
    echo "✓ جدول users با موفقیت ایجاد شد.<br>";
    
    // اضافه کردن یک کاربر ادمین پیش‌فرض
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql_admin = "INSERT INTO users (full_name, email, password, role, status) 
                  VALUES ('مدیر سیستم', 'admin@example.com', '$admin_password', 'admin', 'active')";
    
    if ($db->query($sql_admin)) {
        echo "✓ کاربر ادمین با موفقیت اضافه شد.<br>";
        echo "ایمیل: admin@example.com<br>";
        echo "رمز عبور: admin123<br>";
    } else {
        echo "✗ خطا در ایجاد کاربر ادمین: " . $db->getError() . "<br>";
    }
} else {
    echo "✗ خطا در ایجاد جدول users: " . $db->getError() . "<br>";
}
?> 