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

// دریافت لیست مشاوران
$counselors = $db->query("SELECT * FROM counselors WHERE status = 'active' ORDER BY full_name");

// دریافت ساعات کاری (8 صبح تا 8 شب)
$working_hours = [];
for ($i = 8; $i <= 20; $i++) {
    $working_hours[] = sprintf('%02d:00', $i);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $counselor_id = (int)$_POST['counselor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    
    if (empty($counselor_id) || empty($appointment_date) || empty($appointment_time)) {
        $error = 'لطفاً همه فیلدها را پر کنید.';
    } else {
        // تبدیل تاریخ به میلادی
        $date_parts = explode('/', $appointment_date);
        if (count($date_parts) == 3) {
            $g_date = jalali_to_gregorian($date_parts[0], $date_parts[1], $date_parts[2]);
            $appointment_datetime = sprintf('%04d-%02d-%02d %s:00', 
                $g_date[0], $g_date[1], $g_date[2], $appointment_time);
            
            // بررسی تاریخ گذشته
            if (strtotime($appointment_datetime) < time()) {
                $error = 'تاریخ انتخاب شده نمی‌تواند در گذشته باشد.';
            } else {
                // بررسی تداخل زمانی
                $check_sql = "SELECT id FROM appointments 
                            WHERE counselor_id = $counselor_id 
                            AND appointment_date = '$appointment_datetime'
                            AND status IN ('pending', 'confirmed')";
                $check_result = $db->query($check_sql);
                
                if ($check_result && $check_result->num_rows > 0) {
                    $error = 'این زمان قبلاً رزرو شده است. لطفاً زمان دیگری انتخاب کنید.';
                } else {
                    // ثبت نوبت
                    $sql = "INSERT INTO appointments (user_id, counselor_id, appointment_date, status, created_at) 
                            VALUES ($user_id, $counselor_id, '$appointment_datetime', 'pending', NOW())";
                    
                    if ($db->query($sql)) {
                        $success = 'نوبت شما با موفقیت ثبت شد و در انتظار تایید مشاور است.';
                    } else {
                        $error = 'خطا در ثبت نوبت. لطفاً دوباره تلاش کنید.';
                    }
                }
            }
        } else {
            $error = 'فرمت تاریخ نامعتبر است.';
        }
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رزرو نوبت مشاوره | سامانه مشاوره آنلاین</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/persian-datepicker.min.css">
    <style>
        .booking-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .booking-container h1 {
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
        
        .form-group select, 
        .form-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .counselor-info {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            display: none;
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
        
        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .time-slot {
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .time-slot:hover {
            background: #f8f9fa;
        }
        
        .time-slot.selected {
            background: #007bff;
            color: white;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="booking-container">
            <h1>رزرو نوبت مشاوره</h1>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <?php echo $success; ?>
                    <p><a href="profile.php">مشاهده نوبت‌های من</a></p>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="counselor_id">انتخاب مشاور:</label>
                        <select id="counselor_id" name="counselor_id" required>
                            <option value="">لطفاً انتخاب کنید</option>
                            <?php while ($counselor = $counselors->fetch_assoc()): ?>
                                <option value="<?php echo $counselor['id']; ?>" 
                                        data-speciality="<?php echo htmlspecialchars($counselor['speciality']); ?>"
                                        data-bio="<?php echo htmlspecialchars($counselor['bio']); ?>">
                                    <?php echo htmlspecialchars($counselor['full_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div id="counselorInfo" class="counselor-info"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="appointment_date">تاریخ مشاوره:</label>
                        <input type="text" id="appointment_date" name="appointment_date" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <label>ساعت مشاوره:</label>
                        <input type="hidden" id="appointment_time" name="appointment_time" required>
                        <div class="time-slots">
                            <?php foreach ($working_hours as $time): ?>
                                <div class="time-slot" data-time="<?php echo $time; ?>">
                                    <?php echo $time; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">ثبت نوبت</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/persian-date.min.js"></script>
    <script src="assets/js/persian-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            // تنظیمات تقویم
            $('#appointment_date').persianDatepicker({
                format: 'YYYY/MM/DD',
                minDate: new Date(),
                autoClose: true
            });
            
            // نمایش اطلاعات مشاور
            $('#counselor_id').change(function() {
                const selected = $(this).find('option:selected');
                const speciality = selected.data('speciality');
                const bio = selected.data('bio');
                
                if (speciality || bio) {
                    let info = '';
                    if (speciality) info += `<strong>تخصص:</strong> ${speciality}<br>`;
                    if (bio) info += `<strong>درباره مشاور:</strong> ${bio}`;
                    
                    $('#counselorInfo').html(info).show();
                } else {
                    $('#counselorInfo').hide();
                }
            });
            
            // انتخاب ساعت
            $('.time-slot').click(function() {
                $('.time-slot').removeClass('selected');
                $(this).addClass('selected');
                $('#appointment_time').val($(this).data('time'));
            });
        });
    </script>
</body>
</html> 