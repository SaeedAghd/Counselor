<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();

// حذف جدول قبلی اگر وجود دارد
$db->query("DROP TABLE IF EXISTS counselors");

$sql = "CREATE TABLE counselors (
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

if ($db->query($sql)) {
    echo "جدول مشاوران با موفقیت ایجاد شد.";
} else {
    echo "خطا در ایجاد جدول مشاوران: " . $db->getError();
}

// ایجاد پوشه برای آپلود تصاویر
$upload_dir = "uploads/counselors";
if (!file_exists($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "<br>پوشه آپلود تصاویر با موفقیت ایجاد شد.";
        
        // کپی تصویر پیش‌فرض
        copy("assets/images/default.jpg", "$upload_dir/default.jpg");
        echo "<br>تصویر پیش‌فرض کپی شد.";
    } else {
        echo "<br>خطا در ایجاد پوشه آپلود تصاویر.";
    }
} 