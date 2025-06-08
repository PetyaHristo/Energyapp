<?php
session_start();
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Енергийно приложение</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #00b4db, #0083b0);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px);
        }

        h1 {
            margin-bottom: 20px;
            font-size: 36px;
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        a.button {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            background-color: #fff;
            color: #0083b0;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }

        a.button:hover {
            background-color: #0083b0;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['user'])): ?>
            <h1>Здравей, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
            <p>Готов ли си да въведеш нови данни за енергийна консумация?</p>
            <a href="dashboard.php" class="button">Контролен панел</a>
            <a href="logout.php" class="button">Изход</a>
        <?php else: ?>
            <h1>Добре дошли в Енергийното Приложение ⚡</h1>
            <p>Следете, управлявайте и оптимизирайте разхода си на енергия!</p>
            <a href="login.php" class="button">Вход</a>
            <a href="register.php" class="button">Регистрация</a>
        <?php endif; ?>
    </div>
</body>
</html>
