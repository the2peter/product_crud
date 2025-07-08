<?php
session_start();
require_once 'db_config.php'; // ใช้ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบสิทธิ์การเข้าถึง: ต้องมีการเข้าสู่ระบบ (ทั้ง Admin และ User ดูได้)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("กรุณาเข้าสู่ระบบเพื่อดูรายการสินค้า"));
    exit();
}

$username = $_SESSION['username'];
$level = $_SESSION['level'];

$products = [];
$sql = "SELECT id, product_name, model, manufacture_date, price, image_name FROM products ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - รายการสินค้า</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .navbar-text {
            color: rgba(255, 255, 255, 0.75);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container-fluid">
            <a class="navbar-brand" href="user_dashboard.php"><i class="fas fa-boxes me-2"></i>รายการสินค้า</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="user_dashboard.php">ดูสินค้า</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item me-3">
                        <span class="navbar-text">
                            สวัสดี คุณ <?php echo htmlspecialchars($username); ?> (สิทธิ์: <?php echo htmlspecialchars($level); ?>)
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-outline-light"><i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">รายการสินค้า</h2>

        <?php if (empty($products)): ?>
            <div class="alert alert-warning text-center" role="alert">
                <i class="fas fa-box-open me-2"></i>ยังไม่มีข้อมูลสินค้าในระบบ
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>รูปภาพ</th>
                            <th>ชื่อสินค้า</th>
                            <th>รุ่น</th>
                            <th>วันผลิต</th>
                            <th>ราคา</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['id']); ?></td>
                                <td>
                                    <?php if ($product['image_name']): ?>
                                        <img src="product_images/<?php echo htmlspecialchars($product['image_name']); ?>" alt="รูปภาพ <?php echo htmlspecialchars($product['product_name']); ?>" class="product-img">
                                    <?php else: ?>
                                        <img src="https://placehold.co/80x80/cccccc/333333?text=ไม่มีรูป" alt="ไม่มีรูปภาพ" class="product-img">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['model']); ?></td>
                                <td><?php echo htmlspecialchars($product['manufacture_date']); ?></td>
                                <td><?php echo number_format($product['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Font Awesome for Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
