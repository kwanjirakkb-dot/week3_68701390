
<?php

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

include 'condb.php';

include 'condb.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // GET: ดึงข้อมูลทั้งหมด
    if ($method === "GET") {
        $stmt = $conn->prepare(
            "SELECT * FROM contacts ORDER BY id DESC"
        );
        $stmt->execute();

        echo json_encode([
            "success" => true,
            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ], JSON_UNESCAPED_UNICODE);
    }

    // POST: เพิ่มข้อมูล
    elseif ($method === "POST") {
        $data = json_decode(
            file_get_contents("php://input"), true
        );

        if (!is_array($data)) {
            throw new Exception("ข้อมูล JSON ไม่ถูกต้อง");
        }

        $fields = [
            "subject", "detail", "fullname", "email"
        ];

        foreach ($fields as $field) {
            if (!isset($data[$field]) ||
                trim((string)$data[$field]) === "") {
                throw new Exception("กรุณากรอกข้อมูลให้ครบ");
            }
        }

        $stmt = $conn->prepare(
            "INSERT INTO contacts (subject, detail, fullname, email)
            VALUES (:subject, :detail, :fullname, :email, NOW())"
        );

        $stmt->execute([
            ":subject" => $data["subject"],
            ":detail" => $data["detail"],
            ":fullname" => $data["fullname"],
            ":email" => $data["email"]
        ]);

        echo json_encode([
            "success" => true,
            "message" => "เพิ่มข้อมูลเรียบร้อย"
        ], JSON_UNESCAPED_UNICODE);
    }

    // PUT: แก้ไขข้อมูล
    elseif ($method === "PUT") {
        $data = json_decode(
            file_get_contents("php://input"), true
        );

        if (!is_array($data) ||
            !isset($data["id"]) ||
            !is_numeric($data["id"])) {
            throw new Exception("ไม่พบค่า id");
        }

        $fields = [
            "subject", "detail", "fullname", "email"
        ];

        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                throw new Exception("ข้อมูลไม่ครบ: " . $field);
            }
        }

        $stmt = $conn->prepare(
            "UPDATE contacts SET
                subject = :subject,
                detail = :detail,
                fullname = :fullname,
                email = :email
             WHERE id = :id"
        );

        $stmt->execute([
            ":subject" => $data["subject"],
            ":detail" => $data["detail"],
            ":fullname" => $data["fullname"],
            ":email" => $data["email"],
            ":id" => (int)$data["id"]
        ]);

        echo json_encode([
            "success" => true,
            "message" => "แก้ไขข้อมูลเรียบร้อย"
        ], JSON_UNESCAPED_UNICODE);
    }

    // DELETE: ลบข้อมูล
    elseif ($method === "DELETE") {
        $data = json_decode(
            file_get_contents("php://input"), true
        );

        if (!is_array($data) ||
            !isset($data["id"]) ||
            !is_numeric($data["id"])) {
            throw new Exception("ไม่พบค่า id");
        }

        $stmt = $conn->prepare(
            "DELETE FROM contacts WHERE id = :id"
        );

        $stmt->execute([
            ":id" => (int)$data["id"]
        ]);

        echo json_encode([
            "success" => true,
            "message" => "ลบข้อมูลเรียบร้อย"
        ], JSON_UNESCAPED_UNICODE);
    }

    else {
        http_response_code(405);

        echo json_encode([
            "success" => false,
            "message" => "Method ไม่ถูกต้อง"
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Throwable $e) {
    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
