<?php
include "../config.php";
session_start();

// 🔐 ONLY STAFF OR ADMIN
if (!isset($_SESSION["user_id"]) || 
    ($_SESSION["role_id"] != 1 && $_SESSION["role_id"] != 2)) {
    header("Location: ../login.php");
    exit();
}

// Fetch items for the dropdown
$items = $conn->query("SELECT * FROM inventory_items ORDER BY item_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1e2a25; }
        .navbar-brand, .nav-link { color: #c1d1c0 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #ffffff !important; }
        .content { padding: 25px; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(196,26,26,0.08); border: none; }
        .btn-custom { border-radius: 6px; }
        .badge-type { font-weight: 500; padding: 6px 10px; font-size: 13px; }
        .in { background-color: #198754; color: #fff; }
        .out { background-color: #dc3545; color: #fff; }
        .borrow { background-color: #ffc107; color: #212529; }
        .return { background-color: #0d6efd; color: #fff; }
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
            <li class="nav-item"><a class="nav-link active" href="transactions.php"><i class="fas fa-exchange-alt me-1"></i> Transactions</a></li>
            <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-line me-1"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
        </ul>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="content container">
    <div class="card p-4">
        <h3 class="mb-4"><i class="fas fa-exchange-alt me-2"></i>Record Transaction</h3>

        <form method="POST" action="add_transaction.php" class="row g-3 align-items-end">

            <div class="col-md-4">
                <label class="form-label">Item</label>
                <select name="item_id" class="form-select" required>
                    <option value="">-- Select Item --</option>
                    <?php while($i = $items->fetch_assoc()): ?>
                        <option value="<?= $i['item_id'] ?>"><?= htmlspecialchars($i['item_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Transaction Type</label>
                <select name="type" class="form-select" required>
                    <option value="in">Stock IN</option>
                    <option value="out">Stock OUT</option>
                    <option value="borrow">Borrow</option>
                    <option value="return">Return</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 btn-custom"><i class="fas fa-paper-plane me-1"></i> Submit</button>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>