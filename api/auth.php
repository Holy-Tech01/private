<?php
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data) {
    echo json_encode(["error" => "Invalid input"]);
    exit();
}

$action = $data->action ?? '';

if ($action === 'register') {
    $fullName = $data->fullName;
    $recognitionName = $data->recognitionName;
    $email = $data->email ?? '';
    $phone = $data->phone ?? '';
    $matricNumber = $data->matricNumber;
    $password = password_hash($data->password, PASSWORD_DEFAULT);

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE matric_number = ?");
    $stmt->execute([$matricNumber]);
    if ($stmt->fetch()) {
        echo json_encode(["error" => "User with this Matric Number already exists"]);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, recognition_name, email, phone, matric_number, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fullName, $recognitionName, $email, $phone, $matricNumber, $password]);

        $userId = $pdo->lastInsertId();

        // Return user data (excluding password)
        echo json_encode([
            "success" => true,
            "user" => [
                "id" => $userId,
                "fullName" => $fullName,
                "recognitionName" => $recognitionName,
                "email" => $email,
                "phone" => $phone,
                "matricNumber" => $matricNumber,
                "role" => "student"
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Registration invalid: " . $e->getMessage()]);
    }

} elseif ($action === 'login') {
    $matricNumber = $data->matricNumber;
    $password = $data->password;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE matric_number = ?");
    $stmt->execute([$matricNumber]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        echo json_encode([
            "success" => true,
            "user" => [
                "id" => $user['id'],
                "fullName" => $user['full_name'],
                "recognitionName" => $user['recognition_name'],
                "email" => $user['email'],
                "phone" => $user['phone'],
                "matricNumber" => $user['matric_number'],
                "role" => $user['role'],
                "profilePicture" => $user['profile_picture'] ? 'http://localhost/HOD%20Sport/' . $user['profile_picture'] : null
            ]
        ]);
    } else {
        echo json_encode(["error" => "Invalid Matric Number or Password"]);
    }
} else {
    echo json_encode(["error" => "Invalid action"]);
}
?>