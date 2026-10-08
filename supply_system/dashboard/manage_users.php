<?php
session_start();
include "../config.php";

// ADMIN ONLY
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    header("Location: ../login.php");
    exit();
}

// MESSAGE HANDLING
$message = "";
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_POST["add"])) {
    $name = $_POST["full_name"];
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role_id"];

    $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role_id, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())");
    $stmt->bind_param("sssi", $name, $username, $password, $role);

    if ($stmt->execute()) {
        $message = "User added successfully!";
    } else {
        $message = "Failed to add user: " . $stmt->error;
    }
}

if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);

    if ($id > 0) {
        // ❌ Check foreign key constraint
        $result = $conn->query("DELETE FROM users WHERE user_id=$id");

        if ($result) {
            $_SESSION['message'] = "User deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete user: " . $conn->error;
        }
    }

    header("Location: manage_users.php");
    exit();
}

if (isset($_GET["toggle"])) {
    $id = intval($_GET["toggle"]);

    // Get current status
    $res = $conn->query("SELECT status FROM users WHERE user_id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $newStatus = ($row['status'] === 'active') ? 'inactive' : 'active';

        $conn->query("UPDATE users SET status='$newStatus' WHERE user_id=$id");
        $_SESSION['message'] = "User status updated!";
    }

    header("Location: manage_users.php");
    exit();
}

$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1e2a25; }
        .navbar-brand, .nav-link { color: #c1d1c0 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #ffffff !important; }
        .btn-custom { border-radius: 8px; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .table thead { background-color: #1e2a25; color: #c1d1c0; }
        .table tbody tr:hover { background-color: #e8f0e8; }
        .badge-low { background-color: #dc3545; color: white; }
        .badge-ok { background-color: #28a745; color: white; }
        .content { padding: 20px; }
        .alert-success { background-color: #d4edda; color: #155724; border-radius: 8px; padding: 10px; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="admin.php">
        <img src="../logo1.png" width="40" height="40" class="me-2">
        <strong>Admin Dashboard</strong>
    </a>
    <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="manage_users.php"><i class="fas fa-users-cog me-1"></i> Users</a></li>
            <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-line me-1"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="logs.php"><i class="fas fa-file-alt me-1"></i> Logs</a></li>
            <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
        </ul>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="content container">
    <h3 class="mb-4">Manage Users</h3>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- ADD USER -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white"><i class="fas fa-user-plus"></i> Add New User</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-4"><input type="text" name="full_name" class="form-control" placeholder="Full Name" required></div>
                <div class="col-md-3"><input type="text" name="username" class="form-control" placeholder="Username" required></div>
                <div class="col-md-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                <div class="col-md-2">
                    <select name="role_id" class="form-select" required>
                        <option value="">Select Role</option>
                        <option value="1">Admin</option>
                        <option value="2">Staff</option>
                        <option value="3">Employee</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" name="add" class="btn btn-success btn-custom"><i class="fas fa-plus"></i> Add User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- USER LIST -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white"><i class="fas fa-users"></i> User List</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>User Code</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php
                                $names = explode(' ', $row["full_name"]);
                                $initials = '';
                                foreach ($names as $n) { $initials .= strtoupper(substr($n,0,1)); }
                                echo $initials . str_pad($row["user_id"], 3, '0', STR_PAD_LEFT);
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row["full_name"]) ?></td>
                            <td><?= htmlspecialchars($row["username"]) ?></td>
                            <td><?= $row["role_id"]==1 ? "Admin" : ($row["role_id"]==2?"Staff":"Employee") ?></td>
                            <td>
                                <?php if($row["status"]=="active"): ?>
                                    <span class="badge badge-ok">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-low">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?toggle=<?= $row["user_id"] ?>" class="btn btn-warning btn-sm btn-custom">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                                <a href="?delete=<?= $row["user_id"] ?>" class="btn btn-danger btn-sm btn-custom" onclick="return confirm('Delete this user?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if($users->num_rows == 0): ?>
                        <tr><td colspan="6">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>