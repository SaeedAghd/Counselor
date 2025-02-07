<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();

// نمایش ساختار جدول users
echo "<h2>ساختار جدول users:</h2>";
$result = $db->query("DESCRIBE users");
if ($result) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "خطا در خواندن ساختار جدول: " . $db->getError();
}

// نمایش رکوردهای موجود
echo "<h2>رکوردهای موجود در جدول users:</h2>";
$result = $db->query("SELECT * FROM users");
if ($result) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "خطا در خواندن رکوردها: " . $db->getError();
}
?>