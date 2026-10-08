<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 2) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard</title>
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
        .content { padding: 20px; }
        .card-header { background-color: #1e2a25; color: #c1d1c0; }
        .card-body a { text-decoration: none; display: block; margin: 10px 0; font-weight: 500; color: #1e2a25; }
        .card-body a:hover { color: #28a745; }
    </style>
</head>
<body>

<!-- TOP NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="staff.php">
        <img src="../logo1.png" width="40" height="40" class="me-2">
        <strong>Staff Dashboard</strong>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link active" href="staff.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="inventory.php"><i class="fas fa-boxes-stacked me-1"></i> Inventory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="transactions.php"><i class="fas fa-exchange-alt me-1"></i> Transactions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reports.php"><i class="fas fa-chart-line me-1"></i> Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="notifications.php"><i class="fas fa-bell me-1"></i> Notifications</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
            </li>
        </ul>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="content container">

    <h3 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?> (Staff)</h3>

    <div class="row g-4">
        <!-- Inventory Card -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-header"><i class="fas fa-boxes-stacked"></i> Inventory</div>
                <div class="card-body">
                    <a href="inventory.php"><i class="fas fa-caret-right me-1"></i> Manage Inventory</a>
                </div>
            </div>
        </div>

        <!-- Transactions Card -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-header"><i class="fas fa-exchange-alt"></i> Transactions</div>
                <div class="card-body">
                    <a href="transactions.php"><i class="fas fa-caret-right me-1"></i> Record Transaction</a>
                </div>
            </div>
        </div>

        <!-- Notifications Card -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-header"><i class="fas fa-bell"></i> Notifications</div>
                <div class="card-body">
                    <a href="notifications.php"><i class="fas fa-caret-right me-1"></i> View Alerts</a>
                </div>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-header"><i class="fas fa-chart-line"></i> Reports</div>
                <div class="card-body">
                    <a href="reports.php"><i class="fas fa-caret-right me-1"></i> View Reports</a>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>