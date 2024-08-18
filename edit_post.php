<?php
session_start();
$conn = new mysqli('127.0.0.1', 'minnnjuuu', '020411', 'board');
$post_id = $_GET['id'];

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

    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, file_path = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param('sssii', $title, $content, $file_path, $post_id, $user_id);

    if ($stmt->execute()) {
        header("Location: view_post.php?id=$post_id");
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    $stmt = $conn->prepare("SELECT title, content FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $post_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($title, $content);
    $stmt->fetch();
}
?>

<form method="post" enctype="multipart/form-data">
    Title: <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>">
    Content: <textarea name="content"><?php echo htmlspecialchars($content); ?></textarea>
    File: <input type="file" name="file">
    <button type="submit">Update Post</button>
</form>

