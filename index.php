<?php
session_start();
$conn = new mysqli('127.0.0.1', 'secret', 'secret', 'board');

// 사용자가 로그인했는지 확인
$logged_in = isset($_SESSION['user_id']);

// 로그인 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $logged_in = true;
        header("Location: index.php");
        exit;
    } else {
        $login_error = "Invalid login credentials";
    }
}

// 검색 기능 구현
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

$query = "SELECT posts.id, posts.title, users.username, posts.created_at FROM posts 
          JOIN users ON posts.user_id = users.id 
          WHERE posts.title LIKE ? ORDER BY posts.created_at DESC";
$stmt = $conn->prepare($query);
$search_term = '%' . $search_query . '%';
$stmt->bind_param('s', $search_term);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
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
            margin-bottom: 20px;
        }
        h2 {
            font-size: 22px;
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
            margin-bottom: 10px;
        }
        .post-item {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        .post-item:last-child {
            border-bottom: none;
        }
        .search-bar {
            margin-bottom: 30px;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 70%;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }
        .search-bar button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .search-bar button:hover {
            background-color: #0056b3;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-form button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .login-form button:hover {
            background-color: #218838;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .register-link {
            margin-top: 10px;
            display: block;
            color: #007BFF;
            text-decoration: none;
        }
        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
	<h1>Board</h1>

	<?php if ($logged_in): ?>
	<p>Welcome! <a href="logout.php">Logout</a> | <a href="delete_user.php">Delete Account</a></p>
	    <a href="create_post.php">Create New Post</a>

	    <form method="get" class="search-var">
       		 <input type="text" name="search" placeholder="Search posts..." value="<?php echo htmlspecialchars($search_query); ?>">
	         <button type="submit">Search</button>
   	    </form>

	    <?php while ($row = $result->fetch_assoc()): ?>
    		    <div class="post-item">
        		    <h2><a href="view_post.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
          		    <p class="post-meta">By <?php echo htmlspecialchars($row['username']); ?> on <?php echo $row['created_at']; ?></p>
     		    </div>
   	    <?php endwhile; ?>

	<?php else: ?>
	    <h2>Login</h2>

 	    <?php if (isset($login_error)): ?>
       		 <p class="error-message"><?php echo $login_error; ?></p>
   	    <?php endif; ?>

	    <form method="post" class="login-form">
       		 <input type="text" name="username" placeholder="Username" required>
        	 <input type="password" name="password" placeholder="Password" required>
       		 <button type="submit" name="login">Login</button>
   	    </form>

	    <a href="register.php" class="register-link">Don't have an account? Register here</a>
            <?php endif; ?>
</div>

</body>
</html>

