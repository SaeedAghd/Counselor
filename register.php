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
$form_data = [
    'full_name' => '',
    'email' => '',
    'phone' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data = [
        'full_name' => $_POST['full_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? ''
    ];
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // اعتبارسنجی فرم
    if (empty($form_data['full_name']) || empty($form_data['email']) || 
        empty($password) || empty($confirm_password)) {
        $error = 'لطفاً همه فیلدهای ضروری را پر کنید.';
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'لطفاً یک ایمیل معتبر وارد کنید.';
    } elseif (strlen($password) < 6) {
        $error = 'رمز عبور باید حداقل 6 کاراکتر باشد.';
    } elseif ($password !== $confirm_password) {
        $error = 'رمز عبور و تکرار آن مطابقت ندارند.';
    } else {
        $db = new Database();
        
        // بررسی تکراری نبودن ایمیل
        $email = $db->escape($form_data['email']);
        $check_email = $db->query("SELECT id FROM users WHERE email = '$email'");
        
        if ($check_email && $check_email->num_rows > 0) {
            $error = 'این ایمیل قبلاً ثبت شده است.';
        } else {
            // ثبت کاربر جدید
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $full_name = $db->escape($form_data['full_name']);
            $phone = $db->escape($form_data['phone']);
            
            $sql = "INSERT INTO users (full_name, email, password, phone, role, status) 
                    VALUES ('$full_name', '$email', '$hashed_password', '$phone', 'user', 'active')";
            
            if ($db->query($sql)) {
                $success = 'ثبت نام با موفقیت انجام شد. اکنون می‌توانید وارد شوید.';
                // پاک کردن داده‌های فرم
                $form_data = [
                    'full_name' => '',
                    'email' => '',
                    'phone' => ''
                ];
            } else {
                $error = 'خطا در ثبت نام. لطفاً دوباره تلاش کنید.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت نام | سامانه مشاوره آنلاین</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .register-container h1 {
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
        
        .required::after {
            content: ' *';
            color: #dc3545;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="register-container">
            <h1>ثبت نام در سایت</h1>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <?php echo $success; ?>
                    <p><a href="login.php">ورود به سایت</a></p>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="full_name" class="required">نام و نام خانوادگی:</label>
                        <input type="text" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($form_data['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="required">ایمیل:</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">شماره تماس:</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($form_data['phone']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="required">رمز عبور:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="required">تکرار رمز عبور:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">ثبت نام</button>
                </form>
                
                <div class="login-link">
                    <p>قبلاً ثبت نام کرده‌اید؟ <a href="login.php">وارد شوید</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html> 