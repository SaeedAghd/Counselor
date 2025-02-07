<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$db = new Database();

// دریافت شناسه مشاور
if (!isset($_GET['id'])) {
    redirect('counselors.php');
}

$counselor_id = (int)$_GET['id'];
$sql = "SELECT * FROM counselors WHERE id = $counselor_id AND status = 'active'";
$result = $db->query($sql);

if (!$result || $result->num_rows == 0) {
    redirect('counselors.php');
}

$counselor = $result->fetch_assoc();

// دریافت زمان‌های رزرو شده برای نمایش در تقویم
$booked_times_sql = "SELECT appointment_date FROM appointments 
                     WHERE counselor_id = $counselor_id 
                     AND status IN ('pending', 'confirmed')";
$booked_times_result = $db->query($booked_times_sql);
$booked_times = [];
if ($booked_times_result) {
    while ($row = $booked_times_result->fetch_assoc()) {
        $booked_times[] = date('Y-m-d H:i:s', strtotime($row['appointment_date']));
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $counselor['full_name']; ?> | سایت مشاوره</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .counselor-profile {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .counselor-header {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .counselor-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .counselor-info {
            flex: 1;
        }
        
        .counselor-speciality {
            color: #666;
            margin-bottom: 15px;
        }
        
        .counselor-contact {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .contact-item {
            margin-bottom: 10px;
        }
        
        .contact-item i {
            width: 20px;
            text-align: center;
            margin-left: 10px;
            color: #3498db;
        }
        
        .booking-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        
        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        
        .time-slot {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .time-slot:hover {
            background-color: #f8f9fa;
        }
        
        .time-slot.booked {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            cursor: not-allowed;
        }
        
        .time-slot.selected {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        @media (max-width: 768px) {
            .counselor-header {
                flex-direction: column;
                text-align: center;
            }
            
            .counselor-image {
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="counselor-profile">
            <div class="counselor-header">
                <img src="<?php echo $counselor['image']; ?>" alt="<?php echo $counselor['full_name']; ?>" class="counselor-image">
                <div class="counselor-info">
                    <h1><?php echo $counselor['full_name']; ?></h1>
                    <div class="counselor-speciality"><?php echo $counselor['speciality']; ?></div>
                    <p><?php echo $counselor['description']; ?></p>
                    
                    <div class="counselor-contact">
                        <div class="contact-item">
                            <i>📞</i>
                            <?php echo $counselor['phone']; ?>
                        </div>
                        <div class="contact-item">
                            <i>📧</i>
                            <?php echo $counselor['email']; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (is_logged_in()): ?>
                <div class="booking-section">
                    <h2>رزرو نوبت</h2>
                    <p>برای رزرو نوبت، لطفاً روی دکمه زیر کلیک کنید:</p>
                    <a href="book_appointment.php?counselor_id=<?php echo $counselor['id']; ?>" class="btn btn-primary">
                        رزرو نوبت مشاوره
                    </a>
                </div>
            <?php else: ?>
                <div class="booking-section">
                    <p>برای رزرو نوبت، لطفاً ابتدا وارد شوید:</p>
                    <a href="login.php" class="btn btn-primary">ورود به سایت</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html> 