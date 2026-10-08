<?php
session_start();
include "../config.php";

// 🔐 ONLY STAFF OR ADMIN
if (!isset($_SESSION["user_id"]) || 
    ($_SESSION["role_id"] != 1 && $_SESSION["role_id"] != 2)) {
    header("Location: ../login.php");
    exit();
}

// FETCH ITEMS + CATEGORY
$result = $conn->query("
    SELECT i.*, c.category_name 
    FROM inventory_items i
    LEFT JOIN categories c ON i.category_id = c.category_id
    ORDER BY i.item_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">

    <style>
        body { 
            background-color: #f0f2f5; 
            font-family: 'Segoe UI', sans-serif; 
        }

        .navbar { background-color: #1e2a25; }

        .navbar-brand, .nav-link { 
            color: #c1d1c0 !important; 
        }

        .navbar-brand:hover, .nav-link:hover { 
            color: #ffffff !important; 
        }

        .content { padding: 25px; }

        .card { 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(196, 26, 26, 0.08); 
            border: none;
        }

        .table thead {
            background-color: #1e2a25;
            color: #ffffff;
            font-weight: 500;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-custom {
            border-radius: 6px;
        }

        .badge {
            font-weight: 500;
            padding: 6px 10px;
            font-size: 13px;
        }

        /* Pulsing red effect for low stock */
.badge-low {
    background-color: #dc3545 !important;
    color: #fff !important;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}
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
            <li class="nav-item">
                <a class="nav-link" href="staff.php">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="inventory.php">
                    <i class="fas fa-boxes-stacked me-1"></i> Inventory
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="transactions.php">
                    <i class="fas fa-exchange-alt me-1"></i> Transactions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reports.php">
                    <i class="fas fa-chart-line me-1"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="content container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            <i class="fas fa-boxes-stacked me-2"></i>Inventory Management
        </h3>

        <a href="add_item.php" class="btn btn-dark btn-custom">
            <i class="fas fa-plus me-1"></i> Add Item
        </a>
    </div>

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
                        <th width="160">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row["item_id"] ?></td>
                                <td><?= htmlspecialchars($row["item_name"]) ?></td>
                                <td><?= htmlspecialchars($row["category_name"] ?? 'N/A') ?></td>
                                <td>
                                    <?= $row["quantity"] ?>
                                    <?php if ($row["quantity"] < 5): ?>
                                        <span class="badge bg-light text-dark border ms-1">
                                            Low
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- ✅ FIXED STATUS COLUMN -->
                                <td>
<?php
    $status = strtolower(trim($row["status"]));

    if ($status == "working") {
        echo '<span class="badge bg-success">Working</span>';
    } elseif ($status == "defective") {
        echo '<span class="badge bg-danger">Defective</span>';
    } elseif ($status == "maintenance") {
        echo '<span class="badge bg-warning text-dark">Maintenance</span>';
    } else {
        echo '<span class="badge bg-secondary">Unknown</span>';
    }
?>
</td>

                                <td>
                                    <a href="edit_item.php?id=<?= $row["item_id"] ?>" 
                                       class="btn btn-outline-dark btn-sm btn-custom">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="delete_item.php?id=<?= $row["item_id"] ?>" 
                                       class="btn btn-outline-secondary btn-sm btn-custom"
                                       onclick="return confirm('Delete item?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>