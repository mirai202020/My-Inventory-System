<?php
session_start();
include "../config.php";

// 🔐 ONLY EMPLOYEE
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 3) {
    header("Location: ../login.php");
    exit();
}

// FETCH LOW STOCK ITEMS
$low_stock = $conn->query("SELECT * FROM inventory_items WHERE quantity < 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Inventory & Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">

    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; }

        /* Navbar */
        .navbar { background-color: #1e2a25; }
        .navbar-brand, .nav-link { color: #c1d1c0 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #fff !important; }

        .content { padding: 25px; }

        /* Table */
        .table { background-color: #fff; border-radius: 12px; overflow: hidden; }
        .table thead { background-color: #1e2a25; color: #fff; font-weight: 500; }
        .table td, .table th { vertical-align: middle; }

        /* Notification */
        .notifications {
            position: fixed;
            top: 80px;
            right: 20px;
            width: 300px;
            z-index: 1050;
        }
        .notif-card {
            background-color: #ffc107;
            color: #000;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: pulse 1.5s infinite;
            font-weight: 500;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255,193,7,0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255,193,7,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,193,7,0); }
        }
        .notif-close {
            cursor: pointer;
            font-weight: bold;
        }
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
                <a class="nav-link" href="employee.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
            </li>
        </ul>
    </div>
</nav>

<!-- NOTIFICATIONS -->
<div class="notifications">
<?php if ($low_stock && $low_stock->num_rows > 0): ?>
    <?php while($item = $low_stock->fetch_assoc()): ?>
        <div class="notif-card">
            <div>
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($item['item_name']) ?> is running low! (<?= $item['quantity'] ?> left)
            </div>
            <span class="notif-close" onclick="this.parentElement.style.display='none';">&times;</span>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>

<!-- MAIN CONTENT -->
<div class="content container">
    <h3>📦 Inventory (View Only)</h3>

    <div class="table-responsive mt-3">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $result = $conn->query("SELECT i.*, c.category_name 
                FROM inventory_items i 
                LEFT JOIN categories c ON i.category_id = c.category_id");
            while($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row["item_name"]) ?></td>
                    <td><?= htmlspecialchars($row["category_name"] ?? 'N/A') ?></td>
                    <td>
                        <?= $row["quantity"] ?>
                        <?php if ($row["quantity"] < 5): ?>
                            <span class="badge badge-low">Low</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $status = strtolower(trim($row["status"]));
                        if ($status == "working") {
                            echo '<span class="badge bg-success">Working</span>';
                        } elseif ($status == "defective") {
                            echo '<span class="badge bg-danger">Defective</span>';
                        } else {
                            echo '<span class="badge bg-warning text-dark">Maintenance</span>';
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>