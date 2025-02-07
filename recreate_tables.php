<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();

// غیرفعال کردن بررسی foreign key
$db->query("SET FOREIGN_KEY_CHECKS = 0");

// حذف جداول
$db->query("DROP TABLE IF EXISTS appointments");
$db->query("DROP TABLE IF EXISTS counselors");

// ایجاد جدول مشاوران
$sql_counselors = "CREATE TABLE counselors (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    speciality VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT 'uploads/counselors/default.jpg',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci";

if ($db->query($sql_counselors)) {
    echo "✓ جدول مشاوران با موفقیت ایجاد شد.<br>";
} else {
    echo "✗ خطا در ایجاد جدول مشاوران: " . $db->getError() . "<br>";
}

// ایجاد جدول نوبت‌ها
$sql_appointments = "CREATE TABLE appointments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    counselor_id INT(11) NOT NULL,
    appointment_date DATETIME NOT NULL,
    description TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (counselor_id) REFERENCES counselors(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci";

if ($db->query($sql_appointments)) {
    echo "✓ جدول نوبت‌ها با موفقیت ایجاد شد.<br>";
} else {
    echo "✗ خطا در ایجاد جدول نوبت‌ها: " . $db->getError() . "<br>";
}

// فعال کردن مجدد بررسی foreign key
$db->query("SET FOREIGN_KEY_CHECKS = 1");

// ایجاد پوشه برای آپلود تصاویر
$upload_dir = "uploads/counselors";
if (!file_exists($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "✓ پوشه آپلود تصاویر با موفقیت ایجاد شد.<br>";
        
        // کپی تصویر پیش‌فرض
        if (!file_exists("$upload_dir/default.jpg")) {
            if (copy("assets/images/default.jpg", "$upload_dir/default.jpg")) {
                echo "✓ تصویر پیش‌فرض کپی شد.<br>";
            } else {
                echo "✗ خطا در کپی تصویر پیش‌فرض.<br>";
            }
        }
    } else {
        echo "✗ خطا در ایجاد پوشه آپلود تصاویر.<br>";
    }
}

echo "<br>عملیات پایان یافت. حالا می‌توانید مشاوران را اضافه کنید.";
?> 