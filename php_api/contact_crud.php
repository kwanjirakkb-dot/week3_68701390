<?php
include 'condb.php';
header("Content-Type: application/json; charset=UTF-8");

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // ✅ ดึงข้อมูลลูกค้าทั้งหมด
    if ($method === "GET") {
        $stmt = $conn->prepare("SELECT * FROM contacts ORDER BY id DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $result]);
    }

    // ✅ เพิ่มข้อมูลลูกค้า
    elseif ($method === "POST") {
        // ตรวจสอบว่าข้อมูลมาจาก JSON หรือ form-data
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            $data = $_POST;
        }

        // ตรวจสอบค่าว่าง
        if (empty($data["firstName"]) || empty($data["lastName"]) || empty($data["phone"]) || empty($data["username"]) || empty($data["password"])) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }


        // เพิ่มข้อมูลลูกค้า
        $stmt = $conn->prepare("INSERT INTO customers (firstName, lastName, phone, username, password)
                                VALUES (:firstName, :lastName, :phone, :username, :password)");

        $stmt->bindParam(":firstName", $data["firstName"]);
        $stmt->bindParam(":lastName", $data["lastName"]);
        $stmt->bindParam(":phone", $data["phone"]);
        $stmt->bindParam(":username", $data["username"]);
        $stmt->bindParam(":password", $data[password]);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "เพิ่มข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถเพิ่มข้อมูลได้"]);
        }
    }

    // ✅ แก้ไขข้อมูล
    elseif ($method === "PUT") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า id"]);
            exit;
        }

        $customer_id = intval($data["customer_id"]);

            $sql = "UPDATE customers 
                    SET firstName = :firstName, 
                        lastName = :lastName, 
                        phone = :phone, 
                        username = :username
                    WHERE customer_id = :id";
        

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":firstName", $data["firstName"]);
        $stmt->bindParam(":lastName", $data["lastName"]);
        $stmt->bindParam(":phone", $data["phone"]);
        $stmt->bindParam(":username", $data["username"]);
        if (!empty($data["password"])) {
            $stmt->bindParam(":password", $password_hash);
        }
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถแก้ไขข้อมูลได้"]);
        }
    }

    // ✅ ลบข้อมูล
    elseif ($method === "DELETE") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า id"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM contacts WHERE id = :id");
        $stmt->bindParam(":id", $data["id"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "ลบข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลได้"]);
        }
    }

    else {
        echo json_encode(["success" => false, "message" => "Method ไม่ถูกต้อง"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
