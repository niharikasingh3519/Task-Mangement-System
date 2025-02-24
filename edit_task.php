<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID

// Check if task ID is provided in URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Task ID is missing.");
}

$task_id = intval($_GET['id']); // Get task ID from URL safely

// Fetch existing task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Task not found or unauthorized access.");
}

$task = $result->fetch_assoc();

// Handle form submission (updating task)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = trim($_POST['status']);
    $priority = trim($_POST['priority']); // New priority field

    if (empty($title) || empty($status) || empty($priority)) {
        echo "<script>alert('Title, Status, and Priority cannot be empty!');</script>";
    } else {
        $update_stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, status = ?, priority = ? WHERE id = ? AND user_id = ?");
        $update_stmt->bind_param("ssssii", $title, $description, $status, $priority, $task_id, $user_id);
        
        if ($update_stmt->execute()) {
            echo "<script>alert('Task updated successfully!'); window.location.href = 'dashboard.php';</script>";
        } else {
            echo "<script>alert('Error updating task!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Task</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Title:</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description:</label>
                <textarea name="description" class="form-control"><?= htmlspecialchars($task['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status:</label>
                <select name="status" class="form-select" required>
                    <option value="To-Do" <?= $task['status'] == 'To-Do' ? 'selected' : '' ?>>To-Do</option>
                    <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Done" <?= $task['status'] == 'Done' ? 'selected' : '' ?>>Done</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Priority:</label>
                <select name="priority" class="form-select" required>
                    <option value="High" <?= $task['priority'] == 'High' ? 'selected' : '' ?>>High 🔥</option>
                    <option value="Medium" <?= $task['priority'] == 'Medium' ? 'selected' : '' ?>>Medium ⚡</option>
                    <option value="Low" <?= $task['priority'] == 'Low' ? 'selected' : '' ?>>Low ✅</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update Task</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
