<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();

// اضافه کردن ستون phone به جدول users
$sql = "ALTER TABLE users 
        ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER email,
        ADD COLUMN status ENUM('active', 'inactive', 'banned') DEFAULT 'active' AFTER role,
        MODIFY COLUMN role ENUM('admin', 'user') DEFAULT 'user'";

if ($db->query($sql)) {
    echo "✓ جدول users با موفقیت به‌روزرسانی شد.<br>";
} else {
    echo "✗ خطا در به‌روزرسانی جدول users: " . $db->getError() . "<br>";
}
?> 