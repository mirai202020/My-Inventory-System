<?php
session_start();
include "../config.php";

// 🔐 ONLY STAFF OR ADMIN
if (!isset($_SESSION["user_id"]) || 
    ($_SESSION["role_id"] != 1 && $_SESSION["role_id"] != 2)) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Item not found.");
}

$item_id = intval($_GET['id']);

// Delete item
$stmt = $conn->prepare("DELETE FROM inventory_items WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();

header("Location: inventory.php");
exit();