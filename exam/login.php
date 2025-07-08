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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เข้าสู่ระบบ</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome 6 for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e0f2f7 0%, #c8e6c9 100%); /* พื้นหลังไล่สีโทนสบายตา */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* ความสูงเต็มหน้าจอ */
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* ฟอนต์ที่อ่านง่าย */
        }
        .login-card {
            background-color: #ffffff;
            border-radius: 1.25rem; /* ขอบโค้งมนมากขึ้น */
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.15); /* เงาที่ดูนุ่มนวล */
            padding: 2.5rem; /* เพิ่ม padding */
            width: 100%;
            max-width: 450px; /* ความกว้างสูงสุดของการ์ด */
            animation: fadeIn 0.8s ease-out; /* เพิ่ม animation */
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-card h3 {
            color: #2c3e50; /* สีข้อความหัวข้อที่เข้มขึ้น */
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600; /* ตัวหนาขึ้น */
        }
        .login-card .form-control {
            border-radius: 0.75rem; /* ขอบโค้งของช่องกรอก */
            padding: 0.85rem 1rem; /* เพิ่ม padding ในช่องกรอก */
            border: 1px solid #ced4da; /* สีขอบ */
            transition: all 0.3s ease; /* เพิ่ม transition */
        }
        .login-card .form-control:focus {
            border-color: #80bdff; /* สีขอบเมื่อ focus */
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25); /* เงาเมื่อ focus */
        }
        .login-card .btn-primary { /* เปลี่ยนเป็น btn-primary เพื่อให้เข้ากับโทนสี */
            background-color: #007bff; /* สีน้ำเงินหลัก */
            border-color: #007bff;
            width: 100%; /* ปุ่มเต็มความกว้าง */
            border-radius: 0.75rem;
            padding: 0.85rem 1.5rem; /* ขนาดปุ่ม */
            font-size: 1.15rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .login-card .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
            transform: translateY(-2px); /* ยกขึ้นเล็กน้อยเมื่อ hover */
        }
        .form-check {
            margin-top: 1.25rem;
            text-align: center;
        }
        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.95rem;
            color: #555;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 0.75rem;
            font-size: 0.95rem;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="login-card">
                    <h3 class="text-center">
                        <i class="fa-solid fa-lock me-3"></i> <!-- ไอคอนกุญแจ -->
                        เข้าสู่ระบบ
                    </h3>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>
                    <form name="formlogin" action="checklogin.php" method="POST" id="login" class="form-horizontal">
                        <div class="mb-4"> <!-- เพิ่ม margin-bottom -->
                            <label for="username" class="form-label visually-hidden">ชื่อผู้ใช้</label>
                            <input type="text" name="username" id="username" class="form-control form-control-lg" required placeholder="ชื่อผู้ใช้" />
                        </div>
                        <div class="mb-4"> <!-- เพิ่ม margin-bottom -->
                            <label for="password" class="form-label visually-hidden">รหัสผ่าน</label>
                            <input type="password" name="password" id="password" class="form-control form-control-lg" required placeholder="รหัสผ่าน" />
                        </div>
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg" id="btn">
                                <i class="fa-solid fa-right-to-bracket me-2"></i> <!-- ไอคอนเข้าสู่ระบบ -->
                                เข้าสู่ระบบ
                            </button>
                        </div>
                        <div class="form-check text-center">
                            <input class="form-check-input" type="checkbox" value="remember-me" id="rememberMeCheckbox" name="remember">
                            <label class="form-check-label" for="rememberMeCheckbox">
                                จดจำฉันไว้
                            </label>
                        </div>
                    </form>
                    <div class="register-link">
                        ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิกที่นี่</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (รวม Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
