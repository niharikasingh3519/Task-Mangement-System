<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : "";
$filter_priority = isset($_GET['priority']) ? trim($_GET['priority']) : "";

// Prepare SQL query with optional filtering
$query = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if ($filter_status && in_array($filter_status, ['To-Do', 'In Progress', 'Done'])) {
    $query .= " AND status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

if ($filter_priority && in_array($filter_priority, ['High', 'Medium', 'Low'])) {
    $query .= " AND priority = ?";
    $params[] = $filter_priority;
    $types .= "s";
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Task Management Dashboard</h2>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <a href="add_task.php" class="btn btn-primary mb-3">➕ Add Task</a>

        <!-- Task Filters -->
        <form method="GET" class="mb-3 d-flex gap-3">
            <div>
                <label class="form-label fw-bold">Filter by Status:</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="To-Do" <?= $filter_status === 'To-Do' ? 'selected' : '' ?>>To-Do</option>
                    <option value="In Progress" <?= $filter_status === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Done" <?= $filter_status === 'Done' ? 'selected' : '' ?>>Done</option>
                </select>
            </div>
            
            <div>
                <label class="form-label fw-bold">Filter by Priority:</label>
                <select name="priority" class="form-select">
                    <option value="">All</option>
                    <option value="High" <?= $filter_priority === 'High' ? 'selected' : '' ?>>High 🔥</option>
                    <option value="Medium" <?= $filter_priority === 'Medium' ? 'selected' : '' ?>>Medium ⚡</option>
                    <option value="Low" <?= $filter_priority === 'Low' ? 'selected' : '' ?>>Low ✅</option>
                </select>
            </div>

            <div class="d-flex align-items-end">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="dashboard.php" class="btn btn-light ms-2">Reset</a>
            </div>
        </form>

        <!-- Task List -->
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['status'] == 'Done' ? 'success' : ($row['status'] == 'In Progress' ? 'warning' : 'secondary'); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $row['priority'] == 'High' ? 'danger' : ($row['priority'] == 'Medium' ? 'warning' : 'info'); ?>">
                                    <?php echo htmlspecialchars($row['priority']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_task.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                                <a href="delete_task.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');" class="btn btn-danger btn-sm">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-muted">No tasks found for the selected filter.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
