<?php
session_start();
include "../config.php";

// 🔐 ONLY STAFF OR ADMIN
if (!isset($_SESSION["user_id"]) || 
    ($_SESSION["role_id"] != 1 && $_SESSION["role_id"] != 2)) {
    header("Location: ../login.php");
    exit();
}

// Check if ID is passed
if (!isset($_GET['id']) || empty($_GET['id'])) die("Item not found.");

$item_id = intval($_GET['id']);

// Fetch item
$stmt = $conn->prepare("SELECT * FROM inventory_items WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("Item not found.");

$item = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['item_name'];
    $category = $_POST['category_id'];
    $quantity = $_POST['quantity'];
    $status = $_POST['status'];

    $update = $conn->prepare("UPDATE inventory_items SET item_name=?, category_id=?, quantity=?, status=? WHERE item_id=?");
    $update->bind_param("siisi", $name, $category, $quantity, $status, $item_id);
    $update->execute();

    header("Location: inventory.php");
    exit();
}

// Fetch categories
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../logo1.png">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1e2a25; }
        .navbar-brand, .nav-link { color: #c1d1c0 !important; }
        .navbar-brand:hover, .nav-link:hover { color: #fff !important; }
        .content { padding: 25px; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(196,26,26,0.08); border: none; }
        .btn-custom { border-radius: 6px; }
        .status-preview { display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 500; color: #fff; }
        .Working { background-color: #198754; }
        .Defective { background-color: #dc3545; }
        .Maintenance { background-color: #ffc107; color: #212529; }
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
            <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-line me-1"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
        </ul>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="content container">
    <div class="card p-4 mx-auto" style="max-width: 600px;">
        <h3 class="mb-4"><i class="fas fa-edit me-2"></i>Edit Item</h3>
        
        <form method="POST" class="row g-3">
            <div class="col-12">
                <label class="form-label">Item Name</label>
                <input type="text" name="item_name" class="form-control" value="<?= htmlspecialchars($item['item_name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= $item['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" value="<?= $item['quantity'] ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" onchange="updateStatusPreview(this.value)" required>
                    <option value="Working" <?= $item['status']=='Working'?'selected':'' ?>>Working</option>
                    <option value="Defective" <?= $item['status']=='Defective'?'selected':'' ?>>Defective</option>
                    <option value="Maintenance" <?= $item['status']=='Maintenance'?'selected':'' ?>>Maintenance</option>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <span class="status-preview <?= $item['status'] ?> ms-2" id="statusPreview"><?= $item['status'] ?></span>
            </div>
            <div class="col-12 d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-dark btn-custom"><i class="fas fa-save me-1"></i> Update</button>
                <a href="inventory.php" class="btn btn-secondary btn-custom"><i class="fas fa-times me-1"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateStatusPreview(value) {
    const preview = document.getElementById('statusPreview');
    preview.textContent = value;
    preview.className = 'status-preview ' + value;
}
</script>
</body>
</html>