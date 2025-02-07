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
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $error = 'لطفاً ایمیل خود را وارد کنید.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'لطفاً یک ایمیل معتبر وارد کنید.';
    } else {
        $db = new Database();
        $email = $db->escape($email);
        
        // بررسی وجود ایمیل در دیتابیس
        $result = $db->query("SELECT id, full_name FROM users WHERE email = '$email' AND status = 'active'");
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // ایجاد توکن بازیابی رمز عبور
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // ذخیره توکن در دیتابیس
            $sql = "INSERT INTO password_resets (user_id, token, expires_at) 
                    VALUES ({$user['id']}, '$token', '$expires')";
            
            if ($db->query($sql)) {
                // ارسال ایمیل بازیابی رمز عبور
                $reset_link = "http://{$_SERVER['HTTP_HOST']}/reset_password.php?token=$token";
                $to = $email;
                $subject = 'بازیابی رمز عبور';
                $message = "
                    <html>
                    <head>
                        <title>بازیابی رمز عبور</title>
                    </head>
                    <body dir='rtl'>
                        <h2>درخواست بازیابی رمز عبور</h2>
                        <p>کاربر گرامی {$user['full_name']}</p>
                        <p>شما درخواست بازیابی رمز عبور کرده‌اید. برای تغییر رمز عبور خود روی لینک زیر کلیک کنید:</p>
                        <p><a href='$reset_link'>بازیابی رمز عبور</a></p>
                        <p>این لینک تا یک ساعت معتبر است.</p>
                        <p>اگر شما این درخواست را نداده‌اید، این ایمیل را نادیده بگیرید.</p>
                    </body>
                    </html>
                ";
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: سامانه مشاوره آنلاین <noreply@example.com>" . "\r\n";
                
                if (mail($to, $subject, $message, $headers)) {
                    $success = 'لینک بازیابی رمز عبور به ایمیل شما ارسال شد. لطفاً ایمیل خود را بررسی کنید.';
                    $email = '';
                } else {
                    $error = 'خطا در ارسال ایمیل. لطفاً بعداً دوباره تلاش کنید.';
                }
            } else {
                $error = 'خطا در ثبت درخواست. لطفاً دوباره تلاش کنید.';
            }
        } else {
            $error = 'کاربری با این ایمیل یافت نشد.';
        }
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بازیابی رمز عبور | سامانه مشاوره آنلاین</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .forgot-password-container {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .forgot-password-container h1 {
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
        <div class="forgot-password-container">
            <h1>بازیابی رمز عبور</h1>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <?php echo $success; ?>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">ایمیل:</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        ارسال لینک بازیابی
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="login-link">
                <p><a href="login.php">بازگشت به صفحه ورود</a></p>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html> 