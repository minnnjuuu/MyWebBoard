<?php
session_start();
$conn = new mysqli('127.0.0.1', 'minnnjuuu', '020411', 'board');

// 연결 오류 처리
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 로그인 여부 확인
if (!isset($_SESSION['user_id'])) {
    header("Location:index.php");
    exit;
}

// 계정 삭제 요청 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_delete']) && isset($_POST['confirm']) && $_POST['confirm'] === 'on') {
        $user_id = $_SESSION['user_id'];
	//게시물 user_id를 17(탈퇴한 이용자)로 변경
	$update_stmt = $conn->prepare("UPDATE posts SET user_id=17 WHERE user_id=?");
	if($update_stmt === false){
		die("Prepare failed: ".$conn->error);
	}
	$update_stmt->bind_param('i',$user_id);
	$update_stmt->execute();
	$update_stmt->close();


        // 계정 삭제 쿼리
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $user_id);
            if ($stmt->execute()) {
                // 세션 종료
                session_unset();
                session_destroy();

                // 홈 페이지로 리다이렉트
                header("Location: index.php");
                exit;
            } else {
                $error_message = "Failed to execute query: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Failed to prepare statement: " . $conn->error;
        }
    } else {
        $error_message = "Please check the box to confirm.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
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
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }
        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }
        .checkbox-label {
            font-size: 14px;
            color: #555;
            display: block;
            margin-bottom: 10px;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .delete-form input[type="checkbox"] {
            margin-right: 10px;
        }
        .delete-form button {
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .delete-form button:hover {
            background-color: #c82333;
        }
        .cancel-link {
            display: block;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
        }
        .cancel-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Delete Account</h1>

    <form method="post" class="delete-form">
        <p>탈퇴시, 작성한 게시물은 삭제되지 않습니다.</p>

        <label class="checkbox-label">
            <input type="checkbox" name="confirm" required>
            위의 내용에 동의하시면 체크박스를 눌러주세요
        </label>

        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <button type="submit" name="confirm_delete">Delete Account</button>
    </form>

    <a href="index.php" class="cancel-link">Cancel</a>
</div>
</body>
</html>
