<?php
session_start();
require_once 'db_config.php'; // ใช้ไฟล์เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=" . urlencode("กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน"));
        exit();
    }

    // ค้นหาผู้ใช้จาก username
    $stmt = $conn->prepare("SELECT id, username, password, level FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($user) {
        // ตรวจสอบรหัสผ่านที่ Hash ไว้
        if (password_verify($password, $user['password'])) {
            // รหัสผ่านถูกต้อง, ตั้งค่า Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['level'] = $user['level'];

            // Redirect ไปยัง Dashboard ตามสิทธิ์
            if ($user['level'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            // รหัสผ่านไม่ถูกต้อง
            header("Location: login.php?error=" . urlencode("ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"));
            exit();
        }
    } else {
        // ไม่พบชื่อผู้ใช้
        header("Location: login.php?error=" . urlencode("ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"));
        exit();
    }
} else {
    // ถ้าไม่ได้ส่งข้อมูลแบบ POST มา
    header("Location: login.php");
    exit();
}
?>
