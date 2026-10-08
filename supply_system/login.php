<?php
session_start();
include "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($password)) {

        $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND status='active'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                // session lng to
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["role_id"] = $user["role_id"];
                $_SESSION["name"] = $user["full_name"];

                $log = $conn->prepare("INSERT INTO audit_log (user_id, action, timestamp) VALUES (?, ?, NOW())");
                $action = "User logged in";
                $log->bind_param("is", $user["user_id"], $action);
                $log->execute();

                if ($user["role_id"] == 1) {
                    header("Location: dashboard/admin.php");
                } elseif ($user["role_id"] == 2) {
                    header("Location: staff/staff.php");
                } else {
                    header("Location: employee/employee.php");
                }

                exit();

            } else {
                $message = "Incorrect password.";
            }

        } else {
            $message = "User not found or inactive.";
        }

    } else {
        $message = "Please enter your username and password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Supply and Facility Inventory Management System</title>
  <link rel="icon" type="image/png" href="logo1.png" />

  <style>
    :root {
      --accent: #4a6741;
      --error: #dc2626;
      --success: #16a34a;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body { height: 100%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

    body.login-bg {
      display: grid;
      place-items: center;
      height: 100vh;
      background: none; /* remove solid background to show video */
      color: #fff;
    }

    /* Video background */
    #bg-video {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      object-fit: cover;
      z-index: -1;
      pointer-events: none;
    }

    /* Glassmorphic login card */
    .card {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      width: 100%;
      max-width: 400px;
      padding: 36px 32px;
      border-radius: 18px;
      box-shadow: 0 16px 40px rgba(0,0,0,0.3);
      animation: fadeIn 0.6s ease;
      text-align: center;
      color: #fff;
    }

    h1 { font-size: 28px; font-weight: 700; margin-bottom: 6px; }
    p.lead { color: #e0e0e0; margin-bottom: 20px; }

    form { text-align: left; }
    label { display: block; margin-top: 12px; font-size: 14px; font-weight: 600; }
    
    input {
      width: 100%;
      padding: 12px 16px;
      margin-top: 6px;
      border: 1px solid rgba(255,255,255,0.4);
      border-radius: 10px;
      font-size: 15px;
      background: rgba(255,255,255,0.1);
      color: #fff;
      transition: border-color 0.3s ease;
    }

    input::placeholder { color: rgba(255,255,255,0.7); }

    input:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(74,103,65,0.25);
      outline: none;
    }

    .actions { margin-top: 20px; text-align: center; }

    button {
      width: 100%;
      padding: 14px;
      font-size: 16px;
      font-weight: 600;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      background: rgba(74,103,65,0.8);
      color: #fff;
      transition: transform 0.2s ease, opacity 0.2s ease;
    }

    button:hover { transform: scale(1.03); opacity: 0.9; }

    .msg { margin-top: 12px; font-size: 14px; min-height: 20px; }
    .error { color: var(--error); }
    .success { color: var(--success); }

    .small { font-size: 13px; margin-top: 18px; }
    .small a { color: #fff; text-decoration: none; }
    .small a:hover { text-decoration: underline; }

    .logo img { width: 190px; height: auto; margin: 0 auto 15px; display: block; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body class="login-bg">

  <video autoplay muted loop playsinline id="bg-video">
    <source src="bg.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>

  <main class="card">

    <div class="logo">
      <img src="logo1.png" alt="System Logo">
    </div>

    <h1>Supply and Facility Inventory System</h1>
    <p class="lead">Manage IT equipment, track inventory status, and monitor facility resources efficiently.</p>

    <form method="POST">

      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Enter your username" required
        value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required>

      <div class="actions">
        <button type="submit">Login</button>
      </div>

    </form>

    <?php if (!empty($message)): ?>
      <p class="msg error"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

  </main>

</body>
</html>