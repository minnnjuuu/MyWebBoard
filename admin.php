<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 관리자 권한 확인
if (!isset($_SESSION['user_id'])) {
    header("Location:index.php");
    exit;
}

$conn = new mysqli('127.0.0.1', 'minnnjuuu', '020411', 'board');

$user_id=$_SESSION['user_id'];
$stmt=$conn->prepare("SELECT username FROM users where id = ?");
$stmt->bind_param('i',$user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

if ($username !== 'admin'){
	header("Location:index.php");
	exit;
}



// 사용자 삭제 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = $_POST['delete_user_id'];

    if ($delete_user_id != $user_id) { // Admin 자신을 삭제하는 것을 방지

        // 먼저 게시물의 user_id를 1000으로 변경
        $update_stmt = $conn->prepare("UPDATE posts SET user_id = 17 WHERE user_id = ?");
        if ($update_stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $update_stmt->bind_param('i', $delete_user_id);
        $update_stmt->execute();
        $update_stmt->close();

        // 사용자 삭제
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('i', $delete_user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // 삭제 성공
            header("Location: admin.php");
            exit;
        } else {
            // 삭제 실패 - 해당 ID가 없을 수 있음
            echo "<script>alert('Failed to delete user. User may not exist.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Admin account cannot be deleted.');</script>";
    }
}


// 사용자 목록 가져오기
$user_result = $conn->query("SELECT id, username FROM users");

// 게시물 목록 가져오기
$post_result = $conn->query("SELECT id, title, user_id, created_at FROM posts");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            width: 100%;
            margin: 20px;
        }
        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-buttons a {
            padding: 8px 12px;
            margin-right: 5px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .edit {
            background-color: #28a745;
        }
        .delete {
            background-color: #dc3545;
        }
        .logout {
            background-color: #6c757d;
            text-align: center;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Admin Dashboard</h1>

    <!-- 사용자 목록 -->
    <h2>Users</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
	</thead>
        <tbody>
            <?php while($user = $user_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
		<td class="action-buttons">
		    <form method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" class="delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- 게시물 목록 -->
    <h2>Posts</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Date Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($post = $post_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $post['id']; ?></td>
                <td><?php echo htmlspecialchars($post['title']); ?></td>
                <td><?php echo $post['created_at']; ?></td>
                <td class="action-buttons">
                    <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="edit">Edit</a>
                    <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="delete" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="logout.php" class="logout">Logout</a>
</div>
<?php
$user_result->free();
$post_result->free();

$conn->close();
?>

</body>
</html>

