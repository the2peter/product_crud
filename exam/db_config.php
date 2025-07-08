<?php
// db_config.php

$servername = "localhost";
$username = "root";     // ชื่อผู้ใช้ฐานข้อมูลของคุณ
$password = "";         // รหัสผ่านฐานข้อมูลของคุณ
$dbname = "my_product_crud"; // ชื่อฐานข้อมูลที่คุณสร้าง

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตั้งค่า charset เป็น utf8mb4 เพื่อรองรับภาษาไทย
$conn->set_charset("utf8mb4");

// echo "Connected successfully"; // บรรทัดนี้ใช้ทดสอบการเชื่อมต่อ เมื่อใช้งานจริงให้ลบออกหรือคอมเมนต์ไว้
?>
