<?php session_start();
// ตรวจสอบว่ามีการเข้าสู่ระบบอยู่แล้วหรือไม่ ถ้ามีให้ Redirect ไป Dashboard ที่เหมาะสม
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['level'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยินดีต้อนรับ</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome 6 for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #e9ecef; /* สีพื้นหลัง */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
        }
        .welcome-container {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            padding: 3rem;
            max-width: 600px;
            width: 100%;
        }
        .welcome-container h1 {
            color: #007bff;
            margin-bottom: 1.5rem;
            font-size: 2.5rem;
        }
        .welcome-container p {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        .btn-group-lg .btn {
            padding: 0.75rem 2rem;
            font-size: 1.25rem;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="welcome-container">
                    <h1><i class="fas fa-box me-2"></i>ยินดีต้อนรับสู่ระบบจัดการสินค้า</h1>
                    <p>กรุณาเข้าสู่ระบบหรือสมัครสมาชิกเพื่อจัดการและดูรายการสินค้า</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a href="login.php" class="btn btn-primary btn-lg"><i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ</a>
                        <a href="register.php" class="btn btn-outline-secondary btn-lg"><i class="fas fa-user-plus me-2"></i>สมัครสมาชิก</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
