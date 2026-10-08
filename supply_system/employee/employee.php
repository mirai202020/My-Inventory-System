<?php
session_start();
include "../config.php";

// 🔐 ONLY EMPLOYEE
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 3) {
    header("Location: ../login.php");
    exit();
}

// FETCH LOW STOCK ITEMS FOR NOTIFICATIONS
$notif_result = $conn->query("SELECT * FROM inventory_items WHERE quantity < 5");

// OPTIONAL: LOG VIEW ACTION (audit trail)
$user_id = $_SESSION["user_id"];
$conn->query("INSERT INTO audit_log (user_id, action, timestamp) 
              VALUES ($user_id, 'Employee accessed dashboard', NOW())");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">

    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; }
        .navbar { background-color: #1e2a25; }
        .navbar-brand, .nav-link { color: #c1d1c0 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #ffffff !important; }
        .content { padding: 25px; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.2s; }
        .card:hover { transform: translateY(-3px); }
        .card-header { background-color: #1e2a25; color: #c1d1c0; font-weight: 600; }
        .card-body a { text-decoration: none; display: flex; align-items: center; padding: 10px 0; font-weight: 500; color: #1e2a25; }
        .card-body a i { margin-right: 10px; }
        .card-body a:hover { color: #28a745; }
        h3 { font-weight: 600; margin-bottom: 20px; }

        /* Notifications popup */
        .notifications { position: fixed; top: 20px; right: 20px; width: 300px; z-index: 1000; }
        .notif-card {
            background: linear-gradient(135deg, #ffc107, #ffdd57);
            color: #000;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            padding: 15px 18px;
            margin-bottom: 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: pulse 2s infinite;
            position: relative;
            transition: transform 0.2s, opacity 0.2s;
        }
        .notif-card:hover { transform: translateY(-3px); }
        .notif-icon { font-size: 22px; margin-right: 12px; animation: iconPulse 1.5s infinite; }
        .notif-text { flex: 1; font-size: 14px; }
        .notif-title { font-weight: 700; font-size: 15px; }
        .notif-close { color: #000; cursor: pointer; font-size: 16px; margin-left: 10px; }
        .notif-close:hover { color: #444; }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255,193,7,0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255,193,7,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,193,7,0); }
        }
        @keyframes iconPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="employee.php">
        <img src="../logo1.png" width="40" height="40" class="me-2">
        <strong>Employee Panel</strong>
    </a>
    <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Notifications Popup -->
<div class="notifications">
    <?php 
    if ($notif_result && $notif_result->num_rows > 0): 
        while($row = $notif_result->fetch_assoc()): ?>
            <div class="notif-card">
                <i class="fas fa-exclamation-triangle notif-icon"></i>
                <div class="notif-text">
                    <div class="notif-title"><?= htmlspecialchars($row['item_name']) ?></div>
                    Low Stock: <?= $row['quantity'] ?> remaining
                </div>
                <span class="notif-close" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
        <?php endwhile; 
    else: ?>
        <div class="notif-card" style="background:#198754;color:#fff;">
            <i class="fas fa-check-circle notif-icon"></i>
            <div class="notif-text">
                <div class="notif-title">All Good!</div>
                No low stock items.
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- MAIN CONTENT -->
<div class="content container">
    <h3>Welcome, <?= htmlspecialchars($_SESSION["name"]); ?> 👤</h3>
    <p class="text-muted mb-4">Employee Panel (View Only)</p>

    <div class="row g-4">
        <!-- INVENTORY -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-boxes-stacked"></i> Inventory</div>
                <div class="card-body">
                    <a href="inventory.php"><i class="fas fa-eye"></i> View Inventory</a>
                </div>
            </div>
        </div>

        <!-- REQUEST ITEM -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-hand-holding"></i> Request Item</div>
                <div class="card-body">
                    <a href="request.php"><i class="fas fa-plus-circle"></i> Request / Borrow Item</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>