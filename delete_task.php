<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Delete task
    $sql = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $task_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Error deleting task.";
    }
}
?>
