<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();

// اضافه کردن ستون is_admin
$sql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) DEFAULT 0";

if ($db->query($sql)) {
    echo "ستون is_admin با موفقیت به جدول users اضافه شد.";
} else {
    echo "خطا در بروزرسانی جدول users.";
} 