<?php
include "../config.php";
session_start();

// 🔐 ONLY STAFF OR ADMIN
if (!isset($_SESSION["user_id"]) || 
    ($_SESSION["role_id"] != 1 && $_SESSION["role_id"] != 2)) {
    header("Location: ../login.php");
    exit();
}

// Fetch summary data
$total = $conn->query("SELECT COUNT(*) as total FROM inventory_items")->fetch_assoc();
$low = $conn->query("SELECT COUNT(*) as low FROM inventory_items WHERE quantity < 5")->fetch_assoc();
$trans = $conn->query("SELECT COUNT(*) as t FROM transactions")->fetch_assoc();

// Fetch low stock items
$low_items = $conn->query("SELECT i.*, c.category_name 
    FROM inventory_items i
    LEFT JOIN categories c ON i.category_id = c.category_id
    WHERE i.quantity < 5
    ORDER BY i.quantity ASC
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
        .navbar-brand:hover, .nav-link:hover { color: #fff !important; }
        .content { padding: 25px; }

        /* Cards */
        .card-report { border-radius: 12px; box-shadow: 0 4px 12px rgba(196,26,26,0.08); border: none; text-align: center; padding: 20px; transition: transform 0.2s; }
        .card-report:hover { transform: translateY(-5px); }
        .icon-circle { width: 50px; height: 50px; display: flex; justify-content: center; align-items: center; border-radius: 50%; margin: 0 auto 10px; color: #fff; font-size: 24px; }
        .total { background-color: #198754; }
        .low { background-color: #dc3545; }
        .trans { background-color: #0d6efd; }
        h5 { margin-top: 10px; font-weight: 600; }

        /* Low stock table animation */
        .row-low {
            animation: rowPulse 1.5s infinite;
            background-color: #ffe5e5;
        }
        @keyframes rowPulse {
            0% { background-color: #ffe5e5; }
            50% { background-color: #ffcccc; }
            100% { background-color: #ffe5e5; }
        }

        /* Table styling */
        .table thead { background-color: #1e2a25; color: #fff; font-weight: 500; }
        .table tbody tr:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="staff.php">
        <img src="../logo1.png" width="40" height="40" class="me-2">
        <strong>Inventory System</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item"><a class="nav-link" href="staff.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="inventory.php"><i class="fas fa-boxes-stacked me-1"></i> Inventory</a></li>
            <li class="nav-item"><a class="nav-link" href="transactions.php"><i class="fas fa-exchange-alt me-1"></i> Transactions</a></li>
            <li class="nav-item"><a class="nav-link active" href="reports.php"><i class="fas fa-chart-line me-1"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
        </ul>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="content container">
    <h3 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Reports Dashboard</h3>
    
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card-report">
                <div class="icon-circle total"><i class="fas fa-boxes-stacked"></i></div>
                <h5>Total Items</h5>
                <p class="fs-4"><?= $total['total'] ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-report">
                <div class="icon-circle low"><i class="fas fa-exclamation-triangle"></i></div>
                <h5>Low Stock</h5>
                <p class="fs-4"><?= $low['low'] ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-report">
                <div class="icon-circle trans"><i class="fas fa-exchange-alt"></i></div>
                <h5>Total Transactions</h5>
                <p class="fs-4"><?= $trans['t'] ?></p>
            </div>
        </div>
    </div>

    <!-- Low Stock Items Table -->
    <h4 class="mb-3"><i class="fas fa-exclamation-circle me-2"></i>Low Stock Items</h4>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($low_items && $low_items->num_rows > 0): ?>
                        <?php while($row = $low_items->fetch_assoc()): ?>
                            <tr class="row-low">
                                <td><?= $row['item_id'] ?></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td><?= htmlspecialchars($row['category_name'] ?? 'N/A') ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No low stock items.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>