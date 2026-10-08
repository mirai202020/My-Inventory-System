<?php
include "../config.php";

$logs = $conn->query("SELECT * FROM audit_log ORDER BY timestamp DESC");

echo "<h2>Audit Logs</h2>";

while($l = $logs->fetch_assoc()){
    echo "<p>User ".$l["user_id"].": ".$l["action"]." (".$l["timestamp"].")</p>";
}
?>