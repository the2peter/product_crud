<?php
session_start();
require_once 'db_config.php'; // ใช้ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบสิทธิ์การเข้าถึง: ต้องเป็น Admin เท่านั้น
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header("Location: login.php?error=" . urlencode("คุณไม่มีสิทธิ์เข้าถึงหน้านี้"));
    exit();
}

$message = '';
$product = null;

// ตรวจสอบว่ามีการส่งค่า ID มาหรือไม่
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // ดึงข้อมูลสินค้าที่จะแก้ไข
    $stmt = $conn->prepare("SELECT id, product_name, model, manufacture_date, price, image_name FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
    } else {
        $message = '<div class="alert alert-danger" role="alert">ไม่พบสินค้าที่ต้องการแก้ไข</div>';
    }
    $stmt->close();
} else {
    $message = '<div class="alert alert-danger" role="alert">ไม่ระบุ ID สินค้าที่ต้องการแก้ไข</div>';
}

// เมื่อมีการส่งข้อมูลจากฟอร์มเพื่อบันทึกการแก้ไข
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    $product_name = trim($_POST['product_name']);
    $model = trim($_POST['model']);
    $manufacture_date = trim($_POST['manufacture_date']);
    $price = trim($_POST['price']);
    $current_image_name = trim($_POST['current_image_name']); // ชื่อรูปภาพเดิม
    $new_image_name = $current_image_name; // ตั้งค่าเริ่มต้นเป็นชื่อรูปภาพเดิม

    if (empty($product_name) || empty($price) || !is_numeric($price)) {
        $message = '<div class="alert alert-danger" role="alert">กรุณากรอกชื่อสินค้าและราคาให้ถูกต้อง!</div>';
    } else {
        $uploadOk = 1;
        // จัดการการอัปโหลดรูปภาพใหม่
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "product_images/";
            $imageFileType = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
            $temp_new_file_name = uniqid("img_") . "." . $imageFileType;
            $target_file = $target_dir . $temp_new_file_name;

            // ตรวจสอบประเภทไฟล์
            $check = getimagesize($_FILES["product_image"]["tmp_name"]);
            if($check !== false) {
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                    $message .= '<div class="alert alert-danger" role="alert">ขออภัย, อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้น</div>';
                    $uploadOk = 0;
                }
            } else {
                $message .= '<div class="alert alert-danger" role="alert">ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ</div>';
                $uploadOk = 0;
            }

            // ตรวจสอบขนาดไฟล์
            if ($_FILES["product_image"]["size"] > 5000000) { // 5MB
                $message .= '<div class="alert alert-danger" role="alert">ขออภัย, ไฟล์รูปภาพมีขนาดใหญ่เกินไป (สูงสุด 5MB)</div>';
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                    // ลบรูปเก่าถ้ามีและไม่ใช่รูปภาพเริ่มต้น (เช่น placeholder)
                    if ($current_image_name && file_exists($target_dir . $current_image_name) && $current_image_name !== 'no_image.png') { // ตรวจสอบชื่อไฟล์ที่ไม่ใช่ placeholder
                        unlink($target_dir . $current_image_name);
                    }
                    $new_image_name = $temp_new_file_name;
                } else {
                    $message .= '<div class="alert alert-danger" role="alert">เกิดข้อผิดพลาดในการอัปโหลดไฟล์รูปภาพใหม่</div>';
                    $uploadOk = 0; // ตั้งค่า uploadOk เป็น 0 เพื่อไม่ให้บันทึกลง DB หากอัปโหลดไม่สำเร็จ
                }
            }
        } elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] != UPLOAD_ERR_NO_FILE) {
             $message .= '<div class="alert alert-danger" role="alert">เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ: รหัสข้อผิดพลาด ' . $_FILES['product_image']['error'] . '</div>';
             $uploadOk = 0;
        }

        if ($uploadOk == 1 && empty($message)) { // ตรวจสอบว่าไม่มีข้อผิดพลาดจากการอัปโหลดก่อนที่จะอัปเดต DB
            $stmt = $conn->prepare("UPDATE products SET product_name = ?, model = ?, manufacture_date = ?, price = ?, image_name = ? WHERE id = ?");
            $stmt->bind_param("sssdsi", $product_name, $model, $manufacture_date, $price, $new_image_name, $id);

            if ($stmt->execute()) {
                $message = '<div class="alert alert-success" role="alert">แก้ไขข้อมูลสินค้าสำเร็จแล้ว! <a href="admin_dashboard.php" class="alert-link">กลับสู่หน้ารายการสินค้า</a></div>';
                // อัปเดตข้อมูลในตัวแปร $product เพื่อแสดงข้อมูลล่าสุด
                $product['product_name'] = $product_name;
                $product['model'] = $model;
                $product['manufacture_date'] = $manufacture_date;
                $product['price'] = $price;
                $product['image_name'] = $new_image_name;
            } else {
                $message = '<div class="alert alert-danger" role="alert">เกิดข้อผิดพลาดในการแก้ไขสินค้า: ' . $stmt->error . '</div>';
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
    <title>แก้ไขข้อมูลสินค้า</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .current-product-img {
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #ddd;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">แก้ไขข้อมูลสินค้า</h2>
        <?php echo $message; ?>

        <?php if ($product): ?>
            <form action="update_product.php?id=<?php echo htmlspecialchars($product['id']); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                <input type="hidden" name="current_image_name" value="<?php echo htmlspecialchars($product['image_name']); ?>">

                <div class="mb-3">
                    <label for="product_name" class="form-label">ชื่อสินค้า:</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="model" class="form-label">รุ่น:</label>
                    <input type="text" class="form-control" id="model" name="model" value="<?php echo htmlspecialchars($product['model']); ?>">
                </div>
                <div class="mb-3">
                    <label for="manufacture_date" class="form-label">วันเดือนปีที่ผลิต:</label>
                    <input type="date" class="form-control" id="manufacture_date" name="manufacture_date" value="<?php echo htmlspecialchars($product['manufacture_date']); ?>">
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">ราคา:</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="product_image" class="form-label">รูปภาพสินค้า:</label>
                    <?php if ($product['image_name']): ?>
                        <p>รูปภาพปัจจุบัน:</p>
                        <img src="product_images/<?php echo htmlspecialchars($product['image_name']); ?>" alt="รูปภาพสินค้าปัจจุบัน" class="current-product-img">
                    <?php else: ?>
                        <p>ยังไม่มีรูปภาพสำหรับสินค้านี้</p>
                    <?php endif; ?>
                    <input type="file" class="form-control mt-2" id="product_image" name="product_image" accept="image/*">
                    <div class="form-text">เลือกไฟล์ใหม่เพื่อเปลี่ยนรูปภาพ (JPG, JPEG, PNG, GIF) สูงสุด 5MB</div>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>บันทึกการแก้ไข</button>
                <a href="admin_dashboard.php" class="btn btn-secondary ms-2"><i class="fas fa-arrow-alt-circle-left me-2"></i>ยกเลิก</a>
            </form>
        <?php else: ?>
            <div class="text-center">
                <a href="admin_dashboard.php" class="btn btn-primary"><i class="fas fa-arrow-alt-circle-left me-2"></i>กลับสู่หน้ารายการสินค้า</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
