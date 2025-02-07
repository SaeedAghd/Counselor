<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/jdf.php';

// بررسی ورود کاربر
if (!is_logged_in()) {
    redirect('login.php');
}

$db = new Database();
$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// دریافت اطلاعات کاربر
$user = $db->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// دریافت نوبت‌های کاربر
$appointments = $db->query("
    SELECT 
        a.*,
        c.full_name as counselor_name,
        c.speciality as counselor_speciality
    FROM appointments a
    JOIN counselors c ON a.counselor_id = c.id
    WHERE a.user_id = $user_id
    ORDER BY a.appointment_date DESC
");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'update_profile') {
        $full_name = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($full_name)) {
            $error = 'نام و نام خانوادگی نمی‌تواند خالی باشد.';
        } else {
            $updates = [];
            $full_name = $db->escape($full_name);
            $phone = $db->escape($phone);
            
            $updates[] = "full_name = '$full_name'";
            $updates[] = "phone = '$phone'";
            
            // اگر کاربر می‌خواهد رمز عبور را تغییر دهد
            if (!empty($current_password)) {
                if (password_verify($current_password, $user['password'])) {
                    if (empty($new_password) || empty($confirm_password)) {
                        $error = 'لطفاً رمز عبور جدید و تکرار آن را وارد کنید.';
                    } elseif ($new_password !== $confirm_password) {
                        $error = 'رمز عبور جدید و تکرار آن مطابقت ندارند.';
                    } elseif (strlen($new_password) < 6) {
                        $error = 'رمز عبور جدید باید حداقل 6 کاراکتر باشد.';
                    } else {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $updates[] = "password = '$hashed_password'";
                    }
                } else {
                    $error = 'رمز عبور فعلی اشتباه است.';
                }
            }
            
            if (empty($error)) {
                $update_sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = $user_id";
                if ($db->query($update_sql)) {
                    $success = 'اطلاعات پروفایل با موفقیت به‌روز شد.';
                    // به‌روزرسانی اطلاعات کاربر
                    $user = $db->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
                } else {
                    $error = 'خطا در به‌روزرسانی اطلاعات.';
                }
            }
        }
    } elseif ($action == 'cancel_appointment') {
        $appointment_id = (int)$_POST['appointment_id'];
        
        // بررسی مالکیت نوبت
        $check_sql = "SELECT * FROM appointments WHERE id = $appointment_id AND user_id = $user_id";
        $appointment = $db->query($check_sql)->fetch_assoc();
        
        if ($appointment && $appointment['status'] == 'pending') {
            if ($db->query("UPDATE appointments SET status = 'cancelled' WHERE id = $appointment_id")) {
                $success = 'نوبت با موفقیت لغو شد.';
                // به‌روزرسانی لیست نوبت‌ها
                $appointments = $db->query("
                    SELECT 
                        a.*,
                        c.full_name as counselor_name,
                        c.speciality as counselor_speciality
                    FROM appointments a
                    JOIN counselors c ON a.counselor_id = c.id
                    WHERE a.user_id = $user_id
                    ORDER BY a.appointment_date DESC
                ");
            } else {
                $error = 'خطا در لغو نوبت.';
            }
        } else {
            $error = 'امکان لغو این نوبت وجود ندارد.';
        }
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پروفایل کاربری | سامانه مشاوره آنلاین</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .profile-content {
            padding: 20px;
        }
        
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .profile-tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        
        .profile-tab.active {
            border-bottom-color: #007bff;
            color: #007bff;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        
        .form-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .appointments-list {
            margin-top: 20px;
        }
        
        .appointment-item {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .appointment-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            margin-right: 10px;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-completed { background: #cce5ff; color: #004085; }
        
        .error-message, .success-message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            
            <div class="profile-content">
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="profile-tabs">
                    <div class="profile-tab active" data-tab="profile">اطلاعات شخصی</div>
                    <div class="profile-tab" data-tab="appointments">نوبت‌های من</div>
                </div>
                
                <div id="profile" class="tab-content active">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label for="full_name">نام و نام خانوادگی:</label>
                            <input type="text" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">ایمیل:</label>
                            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">شماره تماس:</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>
                        
                        <h3>تغییر رمز عبور</h3>
                        <div class="form-group">
                            <label for="current_password">رمز عبور فعلی:</label>
                            <input type="password" id="current_password" name="current_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">رمز عبور جدید:</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">تکرار رمز عبور جدید:</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                    </form>
                </div>
                
                <div id="appointments" class="tab-content">
                    <div class="appointments-list">
                        <?php if ($appointments && $appointments->num_rows > 0): ?>
                            <?php while ($appointment = $appointments->fetch_assoc()): ?>
                                <div class="appointment-item">
                                    <div>
                                        <strong>مشاور:</strong> 
                                        <?php echo htmlspecialchars($appointment['counselor_name']); ?> 
                                        (<?php echo htmlspecialchars($appointment['counselor_speciality']); ?>)
                                    </div>
                                    <div>
                                        <strong>تاریخ:</strong> 
                                        <?php echo jdate('Y/m/d H:i', strtotime($appointment['appointment_date'])); ?>
                                    </div>
                                    <div>
                                        <strong>وضعیت:</strong>
                                        <span class="appointment-status status-<?php echo $appointment['status']; ?>">
                                            <?php
                                            switch($appointment['status']) {
                                                case 'pending':
                                                    echo 'در انتظار تایید';
                                                    break;
                                                case 'confirmed':
                                                    echo 'تایید شده';
                                                    break;
                                                case 'cancelled':
                                                    echo 'لغو شده';
                                                    break;
                                                case 'completed':
                                                    echo 'انجام شده';
                                                    break;
                                            }
                                            ?>
                                        </span>
                                        
                                        <?php if ($appointment['status'] == 'pending'): ?>
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="action" value="cancel_appointment">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                <button type="submit" class="btn btn-danger" 
                                                        onclick="return confirm('آیا از لغو این نوبت اطمینان دارید؟')">
                                                    لغو نوبت
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>شما هنوز هیچ نوبتی ثبت نکرده‌اید.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script>
        // تب‌ها
        const tabs = document.querySelectorAll('.profile-tab');
        const contents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                
                // فعال/غیرفعال کردن تب‌ها
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // نمایش/مخفی کردن محتوا
                contents.forEach(content => {
                    content.classList.remove('active');
                    if (content.id === target) {
                        content.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>
</html> 