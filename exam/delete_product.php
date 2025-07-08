<?php
session_start();
require_once 'db_config.php'; // ใช้ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบสิทธิ์การเข้าถึง: ต้องเป็น Admin เท่านั้น
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header("Location: login.php?error=" . urlencode("คุณไม่มีสิทธิ์เข้าถึงหน้านี้"));
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $image_name = $_GET['image'] ?? ''; // รับชื่อรูปภาพมาด้วย

    // ลบไฟล์รูปภาพออกจากเซิร์ฟเวอร์ก่อน
    $image_path = "product_images/" . $image_name;
    if (file_exists($image_path) && !empty($image_name)) {
        unlink($image_path); // ลบไฟล์จริง
    }

    // ใช้ Prepared Statements เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id); // i: integer

    if ($stmt->execute()) {
        // ลบสำเร็จ
        header("Location: admin_dashboard.php?message=deleted_success");
        exit();
    } else {
        // เกิดข้อผิดพลาด
        header("Location: admin_dashboard.php?message=deleted_fail&error=" . urlencode($stmt->error));
        exit();
    }
    $stmt->close();
} else {
    // ไม่มีการระบุ ID
    header("Location: admin_dashboard.php?message=no_id");
    exit();
}
$conn->close();
?>
