<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = trim($_POST['status']);
    $priority = trim($_POST['priority']);
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($status) || empty($priority)) {
        echo "<script>alert('All fields are required!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, status, priority) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $title, $description, $status, $priority);
        
        if ($stmt->execute()) {
            echo "<script>alert('Task added successfully!'); window.location.href = 'dashboard.php';</script>";
        } else {
            echo "<script>alert('Error adding task!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Task</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Add New Task</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Title:</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description:</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Status:</label>
                <select name="status" class="form-select" required>
                    <option value="To-Do">To-Do</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Done">Done</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Priority:</label>
                <select name="priority" class="form-select" required>
                    <option value="High">High 🔥</option>
                    <option value="Medium" selected>Medium ⚡</option>
                    <option value="Low">Low ✅</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>
    </div>
</body>
</html>
