// register.php
<?php
$conn = new mysqli('127.0.0.1', 'minnnjuuu', '020411', 'board');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param('ss', $username, $password);
    if($stmt->execute()){
	    header("Location : index.php");
	    exit;
	} else {
		echo "Error: ".$stmt->error;
	}
}
?>

<form method="post">
    Username: <input type="text" name="username">
    Password: <input type="password" name="password">
    <button type="submit">Register</button>
</form>

