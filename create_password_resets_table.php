<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();

$sql = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci";

if ($db->query($sql)) {
    echo "✓ جدول password_resets با موفقیت ایجاد شد.";
} else {
    echo "✗ خطا در ایجاد جدول password_resets: " . $db->getError();
}
?> 