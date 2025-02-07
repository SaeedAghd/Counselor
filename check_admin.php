<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$db = new Database();

// بررسی اطلاعات کاربر admin
$sql = "SELECT * FROM users WHERE username = 'admin'";
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "اطلاعات کاربر ادمین:<br>";
    echo "نام کاربری: " . $user['username'] . "<br>";
    echo "ایمیل: " . $user['email'] . "<br>";
    echo "نام کامل: " . $user['full_name'] . "<br>";
    echo "وضعیت ادمین: " . ($user['is_admin'] ? 'بله' : 'خیر') . "<br>";
    echo "هش رمز عبور: " . $user['password'] . "<br>";
    
    // تست رمز عبور
    $test_password = '123456';
    echo "<br>نتیجه تست رمز عبور '" . $test_password . "':<br>";
    echo password_verify($test_password, $user['password']) ? "رمز عبور صحیح است" : "رمز عبور اشتباه است";
} else {
    echo "کاربر ادمین در دیتابیس یافت نشد.";
} 