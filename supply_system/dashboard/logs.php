<?php
session_start();
include "../config.php";

// ADMIN ONLY
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    header("Location: ../login.php");
    exit();
}

$logs = $conn->query("
    SELECT a.*, u.full_name 
    FROM audit_log a
    JOIN users u ON a.user_id = u.user_id
    ORDER BY a.timestamp DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>System Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1e2a25; }
        .navbar-brand, .nav-link { color: #c1d1c0 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #ffffff !important; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .table thead { background-color: #1e2a25; color: #c1d1c0; }
        .table tbody tr:hover { background-color: #e8f0e8; }
        .content { padding: 20px; }
        .badge-info { background-color: #17a2b8; }
    </style>
</head>
<body>

<!-- TOP NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="/supply_system/dashboard/admin.php">
        <img src="/supply_system/logo1.png" width="40" height="40" class="me-2">
        <strong>Admin Dashboard</strong>
    </a>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" href="/supply_system/dashboard/admin.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/supply_system/dashboard/manage_users.php"><i class="fas fa-users-cog me-1"></i> Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/supply_system/dashboard/reports.php"><i class="fas fa-chart-line me-1"></i> Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/supply_system/dashboard/logs.php"><i class="fas fa-file-alt me-1"></i> Logs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/supply_system/logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
            </li>
        </ul>
    </div>
</nav>
<!-- MAIN CONTENT -->
<div class="content container">

    <h3 class="mb-4">System Logs</h3>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-file-alt"></i> Logs
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($logs && $logs->num_rows > 0): ?>
                        <?php while($row = $logs->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row["log_id"] ?></td>
                            <td><?= htmlspecialchars($row["full_name"]) ?></td>
                            <td><?= htmlspecialchars($row["action"]) ?></td>
                            <td><?= $row["timestamp"] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No logs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>