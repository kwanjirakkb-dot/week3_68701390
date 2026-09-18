<?php

include 'condb.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {

    // รับข้อมูลจาก AddContact.vue
    $subject = $_POST['subject'] ?? '';
    $detail = $_POST['detail'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';

    // ตรวจสอบข้อมูล
    if ($subject == '' || $detail == '' || $fullname == '' || $email == '') {

        echo json_encode([
            "success" => false,
            "message" => "กรุณากรอกข้อมูลให้ครบ"
        ]);

        exit;
    }

    // เพิ่มข้อมูลลงตาราง contacts
    $stmt = $conn->prepare("
        INSERT INTO contacts
        (subject, detail, fullname, email)
        VALUES
        (:subject, :detail, :fullname, :email)
    ");

    $stmt->execute([
        ":subject" => $subject,
        ":detail" => $detail,
        ":fullname" => $fullname,
        ":email" => $email
    ]);

    echo json_encode([
        "success" => true,
        "message" => "บันทึกข้อมูลสำเร็จ"
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}

?>