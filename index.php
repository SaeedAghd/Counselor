<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$db = new Database();

// دریافت مشاوران فعال
$counselors = $db->query("SELECT * FROM counselors WHERE status = 'active' ORDER BY id DESC LIMIT 6");
?>

<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سامانه مشاوره آنلاین</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero {
            background-color: #f8f9fa;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .hero h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        
        .hero p {
            color: #666;
            max-width: 600px;
            margin: 0 auto 30px;
        }
        
        .featured-counselors {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        
        .counselor-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .counselor-card:hover {
            transform: translateY(-5px);
        }
        
        .counselor-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .counselor-info {
            padding: 20px;
        }
        
        .counselor-name {
            font-size: 1.2em;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .counselor-speciality {
            color: #666;
            margin-bottom: 15px;
        }
        
        .services {
            background-color: #fff;
            padding: 60px 0;
            margin: 40px 0;
        }
        
        .services h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #2c3e50;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .service-card {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .service-card h3 {
            margin: 20px 0;
            color: #2c3e50;
        }
        
        .service-card p {
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="hero">
        <div class="container">
            <h1>به سامانه مشاوره آنلاین خوش آمدید</h1>
            <p>با کمک مشاوران متخصص ما، مسیر رشد و پیشرفت خود را هموار کنید</p>
            <?php if (!is_logged_in()): ?>
                <a href="register.php" class="btn btn-primary">ثبت نام کنید</a>
                <a href="login.php" class="btn btn-outline">ورود به سایت</a>
            <?php else: ?>
                <a href="book_appointment.php" class="btn btn-primary">رزرو نوبت مشاوره</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <h2>خدمات ما</h2>
        <div class="services-grid">
            <div class="service-card">
                <h3>مشاوره خانواده</h3>
                <p>حل مشکلات خانوادگی و بهبود روابط با کمک متخصصین مجرب</p>
            </div>
            <div class="service-card">
                <h3>مشاوره تحصیلی</h3>
                <p>راهنمایی در مسیر تحصیلی و انتخاب رشته با بهترین مشاوران</p>
            </div>
            <div class="service-card">
                <h3>مشاوره ازدواج</h3>
                <p>مشاوره قبل و بعد از ازدواج برای داشتن زندگی مشترک موفق</p>
            </div>
        </div>

        <!-- بخش خدمات حقوقی -->
        <section class="legal-services">
            <div class="container">
                <h2 class="section-title">خدمات مشاوره حقوقی</h2>
                <div class="legal-grid">
                    <div class="legal-card">
                        <div class="legal-icon">⚖️</div>
                        <h3>مشاوره با وکلای مجرب</h3>
                        <p>دسترسی به وکلای باتجربه در زمینه‌های مختلف حقوقی</p>
                        <ul class="legal-features">
                            <li>مشاوره در امور خانواده</li>
                            <li>دعاوی ملکی و ثبتی</li>
                            <li>امور کیفری</li>
                            <li>قراردادهای تجاری</li>
                            <li>دعاوی بیمه</li>
                        </ul>
                    </div>
                    
                    <div class="legal-card featured">
                        <div class="legal-icon">📋</div>
                        <h3>خدمات ویژه حقوقی</h3>
                        <p>ارائه خدمات تخصصی با بالاترین کیفیت</p>
                        <ul class="legal-features">
                            <li>تنظیم انواع قرارداد و لوایح</li>
                            <li>مشاوره آنلاین 24 ساعته</li>
                            <li>پیگیری پرونده‌های قضایی</li>
                            <li>ثبت شرکت و برند</li>
                            <li>داوری و میانجیگری</li>
                        </ul>
                    </div>
                    
                    <div class="legal-card">
                        <div class="legal-icon">🤝</div>
                        <h3>مزایای همکاری با ما</h3>
                        <p>اطمینان از کیفیت خدمات حقوقی</p>
                        <ul class="legal-features">
                            <li>تضمین محرمانگی اطلاعات</li>
                            <li>مشاوره رایگان اولیه</li>
                            <li>تعرفه‌های منصفانه</li>
                            <li>پشتیبانی مستمر</li>
                            <li>گزارش‌دهی منظم</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- بخش خدمات عمومی -->
        <section class="services">
            <h2>سایر خدمات مشاوره</h2>
            <div class="services-grid">
                <div class="service-card">
                    <h3>مشاوره خانواده</h3>
                    <p>حل مشکلات خانوادگی و بهبود روابط با کمک متخصصین مجرب</p>
                </div>
                <div class="service-card">
                    <h3>مشاوره تحصیلی</h3>
                    <p>راهنمایی در مسیر تحصیلی و انتخاب رشته با بهترین مشاوران</p>
                </div>
                <div class="service-card">
                    <h3>مشاوره ازدواج</h3>
                    <p>مشاوره قبل و بعد از ازدواج برای داشتن زندگی مشترک موفق</p>
                </div>
            </div>
        </section>

        <!-- بخش مشاوران برتر -->
        <section class="featured-counselors">
            <h2>مشاوران برتر ما</h2>
            <div class="counselors-grid">
                <?php if ($counselors && $counselors->num_rows > 0): ?>
                    <?php while ($counselor = $counselors->fetch_assoc()): ?>
                        <div class="counselor-card">
                            <div class="counselor-info">
                                <h3 class="counselor-name"><?php echo $counselor['full_name']; ?></h3>
                                <div class="counselor-speciality"><?php echo $counselor['speciality']; ?></div>
                                <div class="counselor-description">
                                    <?php 
                                        $experience = isset($counselor['experience']) ? $counselor['experience'] : '10';
                                        $rating = isset($counselor['rating']) ? $counselor['rating'] : '4.5';
                                    ?>
                                    <div class="counselor-stats">
                                        <div class="stat">
                                            <span class="stat-value"><?php echo $experience; ?></span>
                                            <span class="stat-label">سال تجربه</span>
                                        </div>
                                        <div class="stat">
                                            <span class="stat-value"><?php echo $rating; ?></span>
                                            <span class="stat-label">امتیاز</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="book_appointment.php?id=<?php echo $counselor['id']; ?>" class="btn btn-primary">رزرو مشاوره</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>در حال حاضر مشاوری در سیستم ثبت نشده است.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>