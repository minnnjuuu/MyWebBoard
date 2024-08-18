<?php
session_start();
$conn = new mysqli('127.0.0.1', 'minnnjuuu', '020411', 'board');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // 파일 업로드 처리
    $file_path = null;
    if (!empty($_FILES['file']['name'])) {
        $target_dir = "uploads/";
        $file_path = $target_dir . basename($_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, file_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isss', $user_id, $title, $content, $file_path);

    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<form method="post" enctype="multipart/form-data">
    Title: <input type="text" name="title">
    Content: <textarea name="content"></textarea>
    File: <input type="file" name="file">
    <button type="submit">Create Post</button>
</form>

