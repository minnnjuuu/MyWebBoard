<?php
session_start();
$conn = new mysqli('127.0.0.1', 'secret', 'secret', 'board');
$post_id = $_GET['id'];

$stmt = $conn->prepare("SELECT posts.title, posts.content, posts.file_path, users.username, posts.created_at
                        FROM posts JOIN users ON posts.user_id = users.id
                        WHERE posts.id = ?");
$stmt->bind_param('i', $post_id);
$stmt->execute();
$stmt->bind_result($title, $content, $file_path, $username, $created_at);
$stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
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
            max-width: 800px;
            width: 100%;
            margin: 20px;
        }
        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }
        .post-meta {
            font-size: 14px;
            color: #999;
            margin-bottom: 20px;
        }
        .file-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .file-link:hover {
            background-color: #0056b3;
        }
        .action-links {
            margin-top: 30px;
        }
        .action-links a {
            margin-right: 15px;
            color: #007BFF;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }
        .action-links a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <p class="post-meta">By <?php echo htmlspecialchars($username); ?> on <?php echo $created_at; ?></p>
    <p><?php echo $content; ?></p>

    <?php if ($file_path): ?>
        <a class="file-link" href="<?php echo htmlspecialchars($file_path); ?>">Download Attachment</a>
    <?php endif; ?>

    <div class="action-links">
        <a href="edit_post.php?id=<?php echo $post_id; ?>">Edit</a>
        <a href="delete_post.php?id=<?php echo $post_id; ?>" onclick="return confirm('Are you sure?');">Delete</a>
        <a href="index.php">Back to list</a>
    </div>
</div>

</body>
</html>
