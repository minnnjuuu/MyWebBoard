<?php
session_start();
$conn = new mysqli('127.0.0.1', 'secret', 'secret', 'board');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    //금지된 문자열 리스트
    $forbidden_patterns=['/script/i', '/alert/i', '/</', '/>/'];

    // 타이틀과 콘텐츠에서 금지된 문자열 검사
    foreach ($forbidden_patterns as $pattern){
	    if(preg_match($pattern, $title)||preg_match($pattern,$content)){
		    echo "Error: Your post contains forbidden characters or words";
		    exit;
	    }
    }

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a New Post</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            display: block;
            font-size: 16px;
            color: #555;
            margin-bottom: 5px;
            margin-top: 15px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="file"] {
            margin-top: 10px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Create a New Post</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Content:</label>
        <textarea id="content" name="content" rows="6" required></textarea>

        <label for="file">File:</label>
        <input type="file" id="file" name="file">

        <button type="submit">Create Post</button>
    </form>
    <button class="back-button" onclick="location.href='index.php';">Back to List</button>
</div>

</body>
</html>

