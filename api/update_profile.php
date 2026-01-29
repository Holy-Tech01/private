<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

$user_id = $_POST['user_id'] ?? '';
$fullName = $_POST['fullName'] ?? '';
$recognitionName = $_POST['recognitionName'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

if (!$user_id) {
    echo json_encode(["error" => "User ID required"]);
    exit();
}

try {
    $profilePicturePath = null;

    // Handle default avatar selection
    if (isset($_POST['default_avatar']) && !empty($_POST['default_avatar'])) {
        $profilePicturePath = $_POST['default_avatar'];
    }
    // Handle file upload
    elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/profiles/';

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            echo json_encode(["error" => "Invalid file type. Only JPG, PNG, and GIF are allowed."]);
            exit();
        }

        $fileName = 'profile_' . $user_id . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
            $profilePicturePath = 'uploads/profiles/' . $fileName;
        } else {
            echo json_encode(["error" => "Failed to upload file"]);
            exit();
        }
    }

    // Update user profile
    if ($profilePicturePath) {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, recognition_name = ?, email = ?, phone = ?, profile_picture = ? WHERE id = ?");
        $stmt->execute([$fullName, $recognitionName, $email, $phone, $profilePicturePath, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, recognition_name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$fullName, $recognitionName, $email, $phone, $user_id]);
    }

    // Fetch updated user data
    $stmt = $pdo->prepare("SELECT id, full_name, recognition_name, email, phone, matric_number, role, profile_picture FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
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
        echo json_encode(["error" => "User not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>