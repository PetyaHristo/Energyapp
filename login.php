<?php
require 'init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Грешно потребителско име или парола.";
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e51b9c, #d0bdf4);
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
            border: 2px solid #e51b9c;
        }
        button {
            width: 100%;
            background: #e51b9c;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 16px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #e51b9c;
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
        <h2 style="color: #e51b9c">Вход</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Потребителско име" required>
            <input type="password" name="password" placeholder="Парола" required>
            <button type="submit" >Влез</button>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        </form>
        <div class="link">
            <a href="register.php" style="color: #e51b9c">Нямаш акаунт?</a>
        </div>
    </div>
</body>
</html>
