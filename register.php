<?php
require 'init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $error = "Потребителското име вече съществува.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        $_SESSION['user'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #9df9ef, #51e2f5);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #fff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            width: 350px;
        }
        h2 {
            text-align: center;
            margin-bottom: 12px;
        }
        input[type="text"], input[type="password"] {
            width: 94%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 16px;
            border: 2px solid #51e2f5;
        }
        button {
            width: 100%;
            background: #51e2f5;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 16px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #00c6ff;
        }
        .error {
            color: red;
            text-align: center;
        }
        .link {
            text-align: center;
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="color: #006a78">Регистрация</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Потребителско име" required>
            <input type="password" name="password" placeholder="Парола" required>
            <button type="submit">Регистрирай се</button>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        </form>
        <div class="link">
            <a href="login.php" style="color: #006a78">Вече имаш профил?</a>
        </div>
    </div>
</body>
</html>
