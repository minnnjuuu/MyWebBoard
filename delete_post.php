<?php
session_start();
$conn = new mysqli('127.0.0.1', 'secret', 'secret', 'board');
$post_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii', $post_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    header("Location: index.php");
} else {
    echo "Error: " . $stmt->error;
}
?>

