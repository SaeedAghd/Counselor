<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();

// حذف رکوردهای قبلی با در نظر گرفتن foreign key
$db->query("SET FOREIGN_KEY_CHECKS = 0");
$db->query("DELETE FROM counselors");
$db->query("ALTER TABLE counselors AUTO_INCREMENT = 1");
$db->query("SET FOREIGN_KEY_CHECKS = 1");

// اضافه کردن مشاور اول
$sql1 = "INSERT INTO counselors (full_name, speciality, phone, email, description, image, status) 
         VALUES ('دکتر علی محمدی', 'مشاور خانواده', '09123456789', 'mohammadi@example.com', 
                'دکتر محمدی دارای ۱۵ سال سابقه در زمینه مشاوره خانواده و روابط زوجین است.', 
                'uploads/counselors/default.jpg', 'active')";

echo "در حال اجرای کوئری اول:<br>";
echo "<pre>" . htmlspecialchars($sql1) . "</pre>";

if ($db->query($sql1)) {
    echo "<div style='color: green;'>مشاور اول با موفقیت اضافه شد.</div>";
} else {
    echo "<div style='color: red;'>خطا در افزودن مشاور اول: " . $db->getError() . "</div>";
}

// اضافه کردن مشاور دوم
$sql2 = "INSERT INTO counselors (full_name, speciality, phone, email, description, image, status) 
         VALUES ('دکتر مریم رضایی', 'روانشناس کودک', '09198765432', 'rezaei@example.com', 
                'دکتر رضایی متخصص در زمینه روانشناسی کودک و نوجوان با بیش از ۱۰ سال تجربه.', 
                'uploads/counselors/default.jpg', 'active')";

echo "<br>در حال اجرای کوئری دوم:<br>";
echo "<pre>" . htmlspecialchars($sql2) . "</pre>";

if ($db->query($sql2)) {
    echo "<div style='color: green;'>مشاور دوم با موفقیت اضافه شد.</div>";
} else {
    echo "<div style='color: red;'>خطا در افزودن مشاور دوم: " . $db->getError() . "</div>";
}

// نمایش تعداد رکوردها
$result = $db->query("SELECT COUNT(*) as count FROM counselors");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<br><strong>تعداد کل مشاوران در دیتابیس: " . $row['count'] . "</strong>";
} else {
    echo "<br><strong>خطا در شمارش مشاوران: " . $db->getError() . "</strong>";
}

// نمایش مشاوران موجود
echo "<h3>لیست مشاوران موجود:</h3>";
$result = $db->query("SELECT * FROM counselors");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['full_name']} ({$row['speciality']})<br>";
    }
} else {
    echo "خطا در خواندن مشاوران: " . $db->getError();
}

// بازگرداندن وضعیت foreign key checks
$db->query("SET FOREIGN_KEY_CHECKS = 1");
?>