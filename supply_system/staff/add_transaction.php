<?php
session_start();
include "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $item_id = $_POST["item_id"];
    $type = $_POST["type"];
    $qty = $_POST["quantity"];
    $user_id = $_SESSION["user_id"];

    // GET CURRENT STOCK
    $res = $conn->query("SELECT quantity FROM inventory_items WHERE item_id=$item_id");
    $row = $res->fetch_assoc();
    $current = $row["quantity"];

    if ($type == "in" || $type == "return") {
        $new_qty = $current + $qty;
    } else {
        $new_qty = $current - $qty;
    }

    // UPDATE INVENTORY
    $conn->query("UPDATE inventory_items SET quantity=$new_qty WHERE item_id=$item_id");

    // INSERT TRANSACTION
    $stmt = $conn->prepare("INSERT INTO transactions (item_id, user_id, transaction_type, quantity, transaction_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisi", $item_id, $user_id, $type, $qty);
    $stmt->execute();

    // AUDIT LOG
    $log = $conn->prepare("INSERT INTO audit_log (user_id, action, timestamp) VALUES (?, ?, NOW())");
    $action = "Transaction: $type (Item ID: $item_id)";
    $log->bind_param("is", $user_id, $action);
    $log->execute();

    header("Location: transactions.php");
}
?>