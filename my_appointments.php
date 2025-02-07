<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// بررسی ورود کاربر
if (!is_logged_in()) {
    redirect('login.php');
}

$db = new Database();
$user_id = $_SESSION['user_id'];

// لغو نوبت
if (isset($_POST['cancel_appointment'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    
    // بررسی مالکیت نوبت
    $check_sql = "SELECT * FROM appointments WHERE id = $appointment_id AND user_id = $user_id";
    $check_result = $db->query($check_sql);
    
    if ($check_result && $check_result->num_rows > 0) {
        $appointment = $check_result->fetch_assoc();
        
        // فقط نوبت‌های تایید نشده یا در انتظار تایید قابل لغو هستند
        if ($appointment['status'] == 'pending' || $appointment['status'] == 'confirmed') {
            $sql = "UPDATE appointments SET status = 'cancelled' WHERE id = $appointment_id";
            if ($db->query($sql)) {
                $success = "نوبت با موفقیت لغو شد.";
            } else {
                $error = "خطا در لغو نوبت.";
            }
        } else {
            $error = "این نوبت قابل لغو نیست.";
        }
    } else {
        $error = "نوبت مورد نظر یافت نشد.";
    }
}

// دریافت لیست نوبت‌ها
$sql = "SELECT a.*, c.full_name as counselor_name, c.speciality 
        FROM appointments a 
        JOIN counselors c ON a.counselor_id = c.id 
        WHERE a.user_id = $user_id 
        ORDER BY a.appointment_date DESC";
$result = $db->query($sql);
?>

<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نوبت‌های من | سایت مشاوره</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .appointment-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .appointment-date {
            font-size: 0.9em;
            color: #666;
        }
        
        .appointment-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        
        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }
        
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .appointment-counselor {
            margin-bottom: 10px;
        }
        
        .appointment-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            text-align: left;
        }
        
        .empty-message {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1 class="page-title">نوبت‌های من</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($appointment = $result->fetch_assoc()): ?>
                <div class="appointment-card">
                    <div class="appointment-header">
                        <div class="appointment-date">
                            <?php echo date('Y/m/d', strtotime($appointment['appointment_date'])); ?>
                            <br>
                            ساعت: <?php echo date('H:i', strtotime($appointment['appointment_date'])); ?>
                        </div>
                        <span class="appointment-status status-<?php echo $appointment['status']; ?>">
                            <?php
                            $status_labels = [
                                'pending' => 'در انتظار تایید',
                                'confirmed' => 'تایید شده',
                                'cancelled' => 'لغو شده',
                                'completed' => 'انجام شده'
                            ];
                            echo $status_labels[$appointment['status']] ?? $appointment['status'];
                            ?>
                        </span>
                    </div>
                    
                    <div class="appointment-counselor">
                        <strong>مشاور:</strong> <?php echo $appointment['counselor_name']; ?>
                        <br>
                        <small><?php echo $appointment['speciality']; ?></small>
                    </div>
                    
                    <?php if ($appointment['description']): ?>
                        <div class="appointment-description">
                            <strong>توضیحات:</strong>
                            <p><?php echo nl2br($appointment['description']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="appointment-actions">
                        <?php if ($appointment['status'] == 'pending' || $appointment['status'] == 'confirmed'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                <button type="submit" name="cancel_appointment" class="btn btn-danger" 
                                        onclick="return confirm('آیا از لغو این نوبت اطمینان دارید؟')">
                                    لغو نوبت
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-message">
                <h3>شما هنوز نوبتی ندارید</h3>
                <p>برای رزرو نوبت مشاوره، به صفحه مشاوران مراجعه کنید.</p>
                <a href="counselors.php" class="btn btn-primary">مشاهده مشاوران</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html> 