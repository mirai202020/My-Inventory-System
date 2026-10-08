<?php
session_start();
include "../config.php";

//ADMIN ONLY
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    header("Location: ../login.php");
    exit();
}

$totalItems = $conn->query("SELECT COUNT(*) AS total FROM inventory_items")->fetch_assoc()['total'];
$lowStock = $conn->query("SELECT COUNT(*) AS total FROM inventory_items WHERE quantity < 5")->fetch_assoc()['total'];
$totalTransactions = $conn->query("SELECT COUNT(*) AS total FROM transactions")->fetch_assoc()['total'];

$transactions = $conn->query("
    SELECT t.*, i.item_name, u.full_name 
    FROM transactions t
    JOIN inventory_items i ON t.item_id = i.item_id
    JOIN users u ON t.user_id = u.user_id
    ORDER BY t.transaction_date DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1e2a25; }
        .navbar-brand, .nav-link { color: #c1d1c0 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #ffffff !important; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card-summary { text-align: center; }
        .card-summary h3 { font-weight: 700; }
        .table thead { background-color: #1e2a25; color: #c1d1c0; }
        .table tbody tr:hover { background-color: #e8f0e8; }
        .badge-low { background-color: #dc3545; }
        .badge-ok { background-color: #28a745; }
        .content { padding: 20px; }
    </style>
</head>
<body>

<!-- TOP NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="admin.php">
        <img src="../logo1.png" width="40" height="40" class="me-2">
        <strong>Reports</strong>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" href="admin.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_users.php"><i class="fas fa-users-cog me-1"></i> Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="reports.php"><i class="fas fa-chart-line me-1"></i> Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logs.php"><i class="fas fa-file-alt me-1"></i> Logs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
            </li>
        </ul>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="content container">

    <h3 class="mb-4">Reports Overview</h3>

    <!-- SUMMARY CARDS -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-summary border-primary p-3">
                <h6>Total Inventory Items</h6>
                <h3><?= $totalItems ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-summary border-danger p-3">
                <h6>Low Stock Items</h6>
                <h3 class="text-danger"><?= $lowStock ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-summary border-warning p-3">
                <h6>Total Transactions</h6>
                <h3><?= $totalTransactions ?></h3>
            </div>
        </div>
    </div>

    <!-- RECENT TRANSACTIONS TABLE -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-list"></i> Recent Transactions
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($transactions && $transactions->num_rows > 0){
                        $count = 1;
                        while($row = $transactions->fetch_assoc()):
                            $type = htmlspecialchars($row['transaction_type']);
                            $badgeClass = ($type == 'out') ? 'badge-low' : 'badge-ok';
                    ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($type) ?></span></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= $row['transaction_date'] ?></td>
                    </tr>
                    <?php
                        endwhile;
                    } else {
                        echo "<tr><td colspan='6'>No transactions found.</td></tr>";
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