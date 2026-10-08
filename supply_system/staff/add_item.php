<?php
session_start();
include "../config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 2) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["item_name"]);
    $category = $_POST["category_id"];
    $qty = $_POST["quantity"];
    $status = $_POST["status"];

    $stmt = $conn->prepare("INSERT INTO inventory_items 
        (item_name, category_id, quantity, status, date_added) 
        VALUES (?, ?, ?, ?, NOW())");

    $stmt->bind_param("siis", $name, $category, $qty, $status);

    if ($stmt->execute()) {

        // Audit Log
        $log = $conn->prepare("INSERT INTO audit_log (user_id, action, timestamp) VALUES (?, ?, NOW())");
        $action = "Added item: " . $name;
        $log->bind_param("is", $_SESSION["user_id"], $action);
        $log->execute();

        // Notification
        $notif = $conn->prepare("INSERT INTO notifications (message, created_at) VALUES (?, NOW())");
        $message = $_SESSION["name"] . " added new item: " . $name;
        $notif->bind_param("s", $message);
        $notif->execute();

        header("Location: inventory.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">

    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1e2a25; }
        .navbar-brand, .nav-link { color: #c1d1c0 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #ffffff !important; }
        .content { padding: 30px; }

        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            padding: 30px;
        }

        .form-title {
            font-weight: 600;
            color: #1e2a25;
        }

        .input-group-text {
            background-color: #1e2a25;
            color: #c1d1c0;
            border: none;
        }

        .form-control, .form-select {
            border-radius: 8px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #1e2a25;
            box-shadow: 0 0 0 0.2rem rgba(30,42,37,0.15);
        }

        .btn-save {
            background-color: #1e2a25;
            color: white;
            border-radius: 8px;
        }

        .btn-save:hover {
            background-color: #28a745;
        }
    </style>
</head>
<body>

<!-- ✅ YOUR EXACT NAVBAR -->
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
                <a class="nav-link" href="staff.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="inventory.php"><i class="fas fa-boxes-stacked me-1"></i> Inventory</a>
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

    <div class="form-card col-lg-7 mx-auto">

        <h4 class="form-title mb-4">
            <i class="fas fa-plus-circle me-2"></i>Add New Item
        </h4>

        <form method="POST">

            <!-- Item Name -->
            <div class="mb-3">
                <label class="form-label">Item Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-box"></i></span>
                    <input type="text" name="item_name" class="form-control" required>
                </div>
            </div>

            <!-- Quantity -->
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                    <input type="number" name="quantity" class="form-control" required>
                </div>
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label class="form-label">Category</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                    <select name="category_id" class="form-select" required>
                        <?php
                        $cat = $conn->query("SELECT * FROM categories");
                        while($c = $cat->fetch_assoc()){
                            echo "<option value='".$c["category_id"]."'>".$c["category_name"]."</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label">Status</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                    <select name="status" class="form-select" required>
                        <option value="working">Working</option>
                        <option value="defective">Defective</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-save w-100">
                <i class="fas fa-save me-2"></i>Save Item
            </button>

        </form>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>