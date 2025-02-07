<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// اگر کاربر قبلاً وارد شده است، به صفحه اصلی هدایت شود
if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$success = '';
$token = $_GET['token'] ?? '';
$valid_token = false;
$user_id = null;

if (empty($token)) {
    $error = 'توکن نامعتبر است.';
} else {
    $db = new Database();
    $token = $db->escape($token);
    
    // بررسی اعتبار توکن
    $sql = "SELECT pr.user_id, u.email, u.full_name 
            FROM password_resets pr 
            JOIN users u ON pr.user_id = u.id 
            WHERE pr.token = '$token' 
            AND pr.expires_at > NOW() 
            ORDER BY pr.created_at DESC 
            LIMIT 1";
    
    $result = $db->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $valid_token = true;
        $user_id = $row['user_id'];
    } else {
        $error = 'توکن نامعتبر است یا منقضی شده است.';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirm_password)) {
        $error = 'لطفاً همه فیلدها را پر کنید.';
    } elseif (strlen($password) < 6) {
        $error = 'رمز عبور باید حداقل 6 کاراکتر باشد.';
    } elseif ($password !== $confirm_password) {
        $error = 'رمز عبور و تکرار آن مطابقت ندارند.';
    } else {
        // به‌روزرسانی رمز عبور
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
        
        if ($db->query($update_sql)) {
            // حذف همه توکن‌های این کاربر
            $db->query("DELETE FROM password_resets WHERE user_id = $user_id");
            
            $success = 'رمز عبور با موفقیت تغییر کرد. اکنون می‌توانید وارد شوید.';
        } else {
            $error = 'خطا در تغییر رمز عبور. لطفاً دوباره تلاش کنید.';
        }
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغییر رمز عبور | سامانه مشاوره آنلاین</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .reset-password-container {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .reset-password-container h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
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
        
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .success-message {
            color: #155724;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="reset-password-container">
            <h1>تغییر رمز عبور</h1>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <?php echo $success; ?>
                    <div class="login-link">
                        <p><a href="login.php">ورود به سایت</a></p>
                    </div>
                </div>
            <?php elseif ($valid_token): ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="password">رمز عبور جدید:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">تکرار رمز عبور جدید:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        تغییر رمز عبور
                    </button>
                </form>
            <?php endif; ?>
            
            <?php if (!$success): ?>
                <div class="login-link">
                    <p><a href="login.php">بازگشت به صفحه ورود</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html> 