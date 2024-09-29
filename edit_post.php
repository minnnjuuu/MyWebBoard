<?php
session_start();
$conn = new mysqli('127.0.0.1', 'secret', 'secret', 'board');
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        textarea {
            height: 150px;
            resize: vertical;
        }
        input[type="file"] {
            margin-bottom: 15px;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .cancel-link {
            display: block;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
            text-align: center;
        }
        .cancel-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Post</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required><?php echo $content; ?></textarea>

        <label for="file">Upload File:</label>
        <input type="file" id="file" name="file">

        <button type="submit">Update Post</button>
    </form>
    <a href="view_post.php?id=<?php echo $post_id; ?>" class="cancel-link">Cancel</a>
</div>
</body>
</html>
