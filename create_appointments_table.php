<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();

$sql = "CREATE TABLE IF NOT EXISTS appointments (
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

if ($db->query($sql)) {
    echo "جدول نوبت‌ها با موفقیت ایجاد شد.";
} else {
    echo "خطا در ایجاد جدول نوبت‌ها: " . $db->getError();
}
?> 