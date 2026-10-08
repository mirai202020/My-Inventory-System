<?php
session_start();
include "../config.php";

// 🔐 ONLY EMPLOYEE
if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 3) {
    header("Location: ../login.php");
    exit();
}

$message = "";

// FETCH ITEMS
$items = $conn->query("SELECT * FROM inventory_items WHERE quantity > 0");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST["item_id"];
    $quantity = $_POST["quantity"];
    $user_id = $_SESSION["user_id"];

    if (!empty($item_id) && !empty($quantity)) {
        // INSERT REQUEST (PENDING)
        $stmt = $conn->prepare("
            INSERT INTO transactions (item_id, user_id, transaction_type, quantity, status, transaction_date)
            VALUES (?, ?, 'borrow', ?, 'pending', NOW())
        ");
        $stmt->bind_param("iii", $item_id, $user_id, $quantity);
        $stmt->execute();

        // ADD NOTIFICATION
        $notif = "New item request submitted by employee.";
        $conn->query("INSERT INTO notifications (message, status, created_at)
                      VALUES ('$notif', 'unread', NOW())");

        // AUDIT LOG
        $conn->query("INSERT INTO audit_log (user_id, action, timestamp)
                      VALUES ($user_id, 'Requested an item', NOW())");

        $message = "Request submitted successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">

    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; }

        /* Navbar */
        .navbar { background-color: #1e2a25; }
        .navbar-brand, .nav-link { color: #c1d1c0 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #fff !important; }

        /* Content */
        .content { padding: 25px; }
        h3 { margin-bottom: 25px; }

        /* Form */
        .form-control, .btn { border-radius: 8px; }
        .btn-primary { background-color: #1e2a25; border-color: #1e2a25; }
        .btn-primary:hover { background-color: #28a745; border-color: #28a745; }

        /* Alert */
        .alert { border-radius: 8px; }

        /* Footer link */
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #1e2a25; }
        .back-link:hover { color: #28a745; }
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

<!-- MAIN CONTENT -->
<div class="content container">
    <h3>📦 Request / Borrow Item</h3>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label class="form-label">Select Item</label>
            <select name="item_id" class="form-control" required>
                <option value="">-- Select Item --</option>
                <?php while($row = $items->fetch_assoc()): ?>
                    <option value="<?= $row['item_id'] ?>">
                        <?= $row['item_name'] ?> (Stock: <?= $row['quantity'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" min="1" required>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Submit Request</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>