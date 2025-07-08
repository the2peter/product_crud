<?php
session_start();
require_once 'db_config.php'; // ใช้ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบสิทธิ์การเข้าถึง: ต้องเป็น Admin เท่านั้น
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header("Location: login.php?error=" . urlencode("คุณไม่มีสิทธิ์เข้าถึงหน้านี้"));
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = trim($_POST['product_name']);
    $model = trim($_POST['model']);
    $manufacture_date = trim($_POST['manufacture_date']);
    $price = trim($_POST['price']);
    $image_name = null; // ค่าเริ่มต้นเป็น null

    // ตรวจสอบการอัปโหลดรูปภาพ
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "product_images/";
        // สร้างชื่อไฟล์ที่ไม่ซ้ำกัน
        $imageFileType = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
        $new_file_name = uniqid("img_") . "." . $imageFileType;
        $target_file = $target_dir . $new_file_name;
        $uploadOk = 1;

        // ตรวจสอบประเภทไฟล์
        $check = getimagesize($_FILES["product_image"]["tmp_name"]);
        if($check !== false) {
            // อนุญาตเฉพาะบางนามสกุลไฟล์
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $message .= '<div class="alert alert-danger" role="alert">ขออภัย, อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้น</div>';
                $uploadOk = 0;
            }
        } else {
            $message .= '<div class="alert alert-danger" role="alert">ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ</div>';
            $uploadOk = 0;
        }

        // ตรวจสอบขนาดไฟล์ (เช่น ไม่เกิน 5MB)
        if ($_FILES["product_image"]["size"] > 5000000) { // 5MB
            $message .= '<div class="alert alert-danger" role="alert">ขออภัย, ไฟล์รูปภาพมีขนาดใหญ่เกินไป (สูงสุด 5MB)</div>';
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                $image_name = $new_file_name;
            } else {
                $message .= '<div class="alert alert-danger" role="alert">เกิดข้อผิดพลาดในการอัปโหลดไฟล์รูปภาพ</div>';
            }
        }
    } elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] != UPLOAD_ERR_NO_FILE) {
        // กรณีอัปโหลดไฟล์มีปัญหาอื่นๆ ที่ไม่ใช่ไม่มีไฟล์
        $message .= '<div class="alert alert-danger" role="alert">เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ: รหัสข้อผิดพลาด ' . $_FILES['product_image']['error'] . '</div>';
    }


    if (empty($product_name) || empty($price) || !is_numeric($price)) {
        $message .= '<div class="alert alert-danger" role="alert">กรุณากรอกชื่อสินค้าและราคาให้ถูกต้อง!</div>';
    } else {
        if (empty($message)) { // ถ้าไม่มีข้อผิดพลาดจากการอัปโหลดไฟล์
            // ใช้ Prepared Statements เพื่อป้องกัน SQL Injection
            $stmt = $conn->prepare("INSERT INTO products (product_name, model, manufacture_date, price, image_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssds", $product_name, $model, $manufacture_date, $price, $image_name); // s: string, d: double

            if ($stmt->execute()) {
                $message = '<div class="alert alert-success" role="alert">เพิ่มสินค้าใหม่สำเร็จแล้ว! <a href="admin_dashboard.php" class="alert-link">กลับสู่หน้ารายการสินค้า</a></div>';
            } else {
                $message = '<div class="alert alert-danger" role="alert">เกิดข้อผิดพลาดในการเพิ่มสินค้า: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้าใหม่</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">เพิ่มสินค้าใหม่</h2>
        <?php echo $message; ?>
        <form action="create_product.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="product_name" class="form-label">ชื่อสินค้า:</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>
            <div class="mb-3">
                <label for="model" class="form-label">รุ่น:</label>
                <input type="text" class="form-control" id="model" name="model">
            </div>
            <div class="mb-3">
                <label for="manufacture_date" class="form-label">วันเดือนปีที่ผลิต:</label>
                <input type="date" class="form-control" id="manufacture_date" name="manufacture_date">
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">ราคา:</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="product_image" class="form-label">รูปภาพสินค้า:</label>
                <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
                <div class="form-text">อัปโหลดไฟล์รูปภาพ (JPG, JPEG, PNG, GIF) สูงสุด 5MB</div>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>บันทึกสินค้า</button>
            <a href="admin_dashboard.php" class="btn btn-secondary ms-2"><i class="fas fa-arrow-alt-circle-left me-2"></i>ยกเลิก</a>
        </form>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
