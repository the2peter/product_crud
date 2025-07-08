<?php
// register.php
require_once 'db_config.php'; // ใช้ไฟล์เชื่อมต่อฐานข้อมูลใหม่

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // ตรวจสอบว่ามีข้อมูลครบถ้วน
    if (empty($username) || empty($password)) {
        $message = '<div class="alert alert-danger" role="alert">กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน</div>';
    } else {
        // Hash รหัสผ่านก่อนบันทึก
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // ตรวจสอบว่า username ซ้ำหรือไม่
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = '<div class="alert alert-danger" role="alert">ชื่อผู้ใช้นี้มีผู้ใช้งานแล้ว</div>';
        } else {
            // บันทึกข้อมูลผู้ใช้ (เริ่มต้นเป็น level 'user')
            $stmt = $conn->prepare("INSERT INTO users (username, password, level) VALUES (?, ?, 'user')");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $message = '<div class="alert alert-success" role="alert">สมัครสมาชิกสำเร็จแล้ว! คุณสามารถเข้าสู่ระบบได้เลย <a href="login.php" class="alert-link">เข้าสู่ระบบ</a></div>';
            } else {
                $message = '<div class="alert alert-danger" role="alert">เกิดข้อผิดพลาดในการสมัครสมาชิก: ' . $stmt->error . '</div>';
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome 6 for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .register-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 2rem;
            width: 100%;
            max-width: 450px;
        }
        .register-card h3 {
            color: #343a40;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card .form-control {
            border-radius: 0.5rem;
        }
        .register-card .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            width: 100%;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-size: 1.1rem;
        }
        .register-card .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="register-card">
                    <h3 class="text-center">
                        <i class="fa-solid fa-user-plus me-2"></i> <!-- ไอคอนสมัครสมาชิก -->
                        สมัครสมาชิก
                    </h3>
                    <?php echo $message; ?>
                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label visually-hidden">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control form-control-lg" id="username" name="username" required placeholder="ชื่อผู้ใช้">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label visually-hidden">รหัสผ่าน</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" required placeholder="รหัสผ่าน">
                        </div>
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-user-plus me-2"></i>สมัครสมาชิก</button>
                        </div>
                    </form>
                    <div class="login-link">
                        มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
