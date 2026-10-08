<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logging Out...</title>
    <meta http-equiv="refresh" content="2;url=login.php">

    <style>
        body {
            margin: 0;
            background: #1e2a25;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            color: white;
        }

        .container {
            text-align: center;
        }

        .loader {
            margin: 20px auto 0;
            border: 6px solid #ccc;
            border-top: 6px solid #4a6741;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Logging out...</h2>
    <p>Please wait</p>
    <div class="loader"></div>
</div>

</body>
</html>
