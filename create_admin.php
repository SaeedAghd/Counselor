<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$db = new Database();

// پاک کردن کاربر ادمین قبلی
$db->query("DELETE FROM users WHERE username = 'admin'");

// اطلاعات ادمین جدید - با رمز عبور ساده
$admin_username = 'admin';
$admin_password = '1234'; // رمز عبور ساده‌تر
$admin_email = 'admin@example.com';
$admin_fullname = 'مدیر سیستم';

// هش کردن رمز عبور
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// درج ادمین در دیتابیس
$sql = "INSERT INTO users (username, password, email, full_name, is_admin) 
        VALUES ('$admin_username', '$hashed_password', '$admin_email', '$admin_fullname', 1)";

if ($db->query($sql)) {
    echo "<div style='direction: rtl; text-align: right;'>";
    echo "<h2>ادمین با موفقیت ایجاد شد</h2>";
    echo "<p>اطلاعات ورود:</p>";
    echo "<ul>";
    echo "<li>نام کاربری: $admin_username</li>";
    echo "<li>رمز عبور: $admin_password</li>";
    echo "</ul>";
    
    // نمایش هش رمز عبور برای اشکال‌زدایی
    echo "<p>هش رمز عبور: $hashed_password</p>";
    
    // تست رمز عبور
    if (password_verify($admin_password, $hashed_password)) {
        echo "<p style='color: green;'>تست رمز عبور: موفق</p>";
    } else {
        echo "<p style='color: red;'>تست رمز عبور: ناموفق</p>";
    }
    
    echo "<p><a href='login.php'>رفتن به صفحه ورود</a></p>";
    echo "</div>";
} else {
    echo "خطا در ایجاد ادمین: " . $db->error;
}