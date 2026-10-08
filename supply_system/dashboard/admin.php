<?php
session_start();
include "../config.php";

//ONLY ADMIN
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    header("Location: ../login.php");
    exit();
}

$name = $_SESSION["name"];

$totalItems = $conn->query("SELECT COUNT(*) AS total FROM inventory_items")->fetch_assoc()['total'];
$lowStock = $conn->query("SELECT COUNT(*) AS total FROM inventory_items WHERE quantity < 5")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalTransactions = $conn->query("SELECT COUNT(*) AS total FROM transactions")->fetch_assoc()['total'];

$itemsResult = $conn->query("
    SELECT i.*, c.category_name 
    FROM inventory_items i
    LEFT JOIN categories c ON i.category_id = c.category_id
    ORDER BY i.item_id DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1e2a25; }
        .navbar-brand, .nav-link { color: #c1d1c0 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #ffffff !important; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card-summary { text-align: center; padding: 20px; transition: transform 0.2s; }
        .card-summary:hover { transform: translateY(-3px); }
        .card-summary h3 { font-weight: 700; }
        .table thead { background-color: #1e2a25; color: #c1d1c0; }
        .table tbody tr:hover { background-color: #e8f0e8; }
        .badge-low { background-color: #dc3545; }
        .badge-ok { background-color: #28a745; }
        .content { padding: 20px; }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(220,53,69,0.7); }
            70% { box-shadow: 0 0 0 10px rgba(220,53,69,0); }
            100% { box-shadow: 0 0 0 0 rgba(220,53,69,0); }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="admin.php">
        <img src="../logo1.png" width="40" height="40" class="me-2">
        <strong>Admin Dashboard</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item"><a class="nav-link active" href="admin.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="fas fa-users-cog me-1"></i> Users</a></li>
            <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-line me-1"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="logs.php"><i class="fas fa-file-alt me-1"></i> Logs</a></li>
            <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
        </ul>
    </div>
</nav>

<div class="content container">

    <h3 class="mb-4">Dashboard Overview</h3>

    <!-- SUMMARY CARDS -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-summary border-primary">
                <h6>Total Inventory Items</h6>
                <h3><?= $totalItems ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-summary border-danger">
                <h6>Low Stock Items</h6>
                <h3 class="text-danger"><?= $lowStock ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-summary border-success">
                <h6>Total Users</h6>
                <h3><?= $totalUsers ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-summary border-warning">
                <h6>Total Transactions</h6>
                <h3><?= $totalTransactions ?></h3>
            </div>
        </div>
    </div>

    <!-- RECENT INVENTORY TABLE -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-boxes"></i> Recent Inventory Records
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Added On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($itemsResult && $itemsResult->num_rows > 0) {
                        $count = 1;
                        while ($row = $itemsResult->fetch_assoc()) {
                            $isLow = $row['quantity'] < 5;
                            // Use created_at if exists, else use N/A
                            $addedOn = isset($row['created_at']) ? $row['created_at'] : 'N/A';
                            ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td><?= htmlspecialchars($row['category_name'] ?? 'N/A') ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td>
                                    <?php if ($isLow): ?>
                                        <span class="badge badge-low" style="animation: pulse 1.5s infinite; color:white;">Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge badge-ok">In Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $addedOn ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='6'>No records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>