<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سامانه مشاوره آنلاین</title>
    <style>
        .header {
            background-color: #2c3e50;
            padding: 15px 0;
            color: white;
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        
        .nav-menu {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .nav-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-menu-button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .user-menu-content {
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 4px;
            min-width: 200px;
            z-index: 1000;
        }
        
        .user-menu-content a {
            color: #333;
            padding: 10px 15px;
            display: block;
            text-decoration: none;
        }
        
        .user-menu-content a:hover {
            background-color: #f8f9fa;
        }
        
        .user-menu:hover .user-menu-content {
            display: block;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <a href="index.php" class="logo">مشاوره آنلاین</a>
            
            <nav class="nav-menu">
                <a href="index.php">صفحه اصلی</a>
                <a href="counselors.php">مشاوران</a>
                <a href="about.php">درباره ما</a>
                <a href="contact.php">تماس با ما</a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <button class="user-menu-button">
                            <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'کاربر'; ?>
                            <span>▼</span>
                        </button>
                        <div class="user-menu-content">
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                                <a href="admin/dashboard.php">پنل مدیریت</a>
                            <?php endif; ?>
                            <a href="profile.php">پروفایل من</a>
                            <a href="book_appointment.php">رزرو نوبت</a>
                            <a href="logout.php">خروج</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php">ورود</a>
                    <a href="register.php">ثبت نام</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
</body>
</html> 