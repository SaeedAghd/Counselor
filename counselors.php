<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/jdf.php';

$db = new Database();

// دریافت لیست مشاوران فعال
$sql = "SELECT * FROM counselors WHERE status = 'active' ORDER BY id DESC";
$result = $db->query($sql);
?>

<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مشاوران ما | سایت مشاوره</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .counselors-grid {
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
            height: 250px;
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
            font-size: 0.9em;
        }
        
        .counselor-description {
            color: #666;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .counselor-contact {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .page-header {
            text-align: center;
            margin: 40px 0;
            padding: 40px 0;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .page-header h1 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .page-header p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background-color: white;
            border-radius: 8px;
            margin: 40px 0;
        }
        
        .empty-state h2 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .empty-state p {
            color: #666;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>مشاوران ما</h1>
            <p>تیم متخصص و با تجربه ما آماده ارائه خدمات مشاوره به شما عزیزان است</p>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="counselors-grid">
                <?php while ($counselor = $result->fetch_assoc()): ?>
                    <div class="counselor-card">
                        <img src="<?php echo $counselor['image']; ?>" alt="<?php echo $counselor['full_name']; ?>" class="counselor-image">
                        <div class="counselor-info">
                            <h2 class="counselor-name"><?php echo $counselor['full_name']; ?></h2>
                            <div class="counselor-speciality"><?php echo $counselor['speciality']; ?></div>
                            <p class="counselor-description"><?php echo $counselor['description']; ?></p>
                            <div class="counselor-contact">
                                <a href="counselor_details.php?id=<?php echo $counselor['id']; ?>" class="btn btn-primary">مشاهده پروفایل</a>
                                <?php if (is_logged_in()): ?>
                                    <a href="book_appointment.php?counselor_id=<?php echo $counselor['id']; ?>" class="btn btn-success">رزرو نوبت</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h2>در حال حاضر مشاوری در سیستم ثبت نشده است</h2>
                <p>لطفاً بعداً مراجعه کنید یا با پشتیبانی تماس بگیرید.</p>
                <a href="contact.php" class="btn btn-primary">تماس با ما</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html> 