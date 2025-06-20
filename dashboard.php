<?php

require 'init.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user'];
$username = $_SESSION['username'];

$year = isset($_POST['year']) ? (int)$_POST['year'] : (int)date('Y');
$month = isset($_POST['month']) ? (int)$_POST['month'] : (int)date('n');
$currentDay = (int)date('j');
$currentMonth = (int)date('n');
$currentYear = (int)date('Y');

if (isset($_POST['save'])) {
    if (isset($_POST['energy']) && is_array($_POST['energy'])) {
        foreach ($_POST['energy'] as $day => $energy) {
            $day = (int)$day;
            $energy = is_numeric($energy) ? (float)$energy : null;
            if ($energy !== null && $day >= 1 && $day <= 31) {
                $stmt = $pdo->prepare("REPLACE INTO energy_data (user_id, year, month, day, energy) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $year, $month, $day, $energy]);
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT day, energy FROM energy_data WHERE user_id = ? AND month = ? AND year = ?");
$stmt->execute([$user_id, $month, $year]);
$data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$total = 0;
$chartData = [];
foreach ($data as $day => $energy) {
    if ($year < $currentYear || ($year == $currentYear && ($month < $currentMonth || ($month == $currentMonth && $day <= $currentDay)))) {
        $chartData[$day] = round($energy, 2);
        $total += $energy;
    }
}


// Съвети
$tips = [];
if ($total > 100) {
    $tips[] = "💡 Използвайте LED осветление.";
}
if ($total > 150) {
    $tips[] = "🛑 Проверете уреди, които работят непрекъснато.";
}
if (empty($tips)) {
    $tips[] = "✅ Продължавайте в същия дух!";
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8" />
    <title>Енергиен контрол</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6ecf0;
            margin: 0; padding: 0;
        }
        .wrapper {
            width: 960px;
            margin: 40px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h1, h2, h3 {
            text-align: center;
            color: #333;
        }
        form {
            margin: 20px 0;
            text-align: center;
        }
        select {
            padding: 6px 12px;
            font-size: 16px;
            margin-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: center;
        }
        th {
            background-color: #f0f8ff;
        }
        input[type="number"] {
            width: 80px;
            padding: 5px;
        }
        button {
            padding: 10px 20px;
            margin: 10px 5px;
            background-color: #007acc;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover {
            background-color: #005fa3;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            background: #f9f9f9;
            padding: 10px 15px;
            border-left: 5px solid #007acc;
            margin-bottom: 8px;
        }
        .logout {
            display: block;
            text-align: center;
            margin-top: 30px;
            text-decoration: none;
            color: #444;
        }
        .logout:hover {
            color: red;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Здравей, <?= htmlspecialchars($username) ?>!</h1>

        <form method="POST">
            <label>Година:
        <select name="year" onchange="this.form.submit()">
            <?php
                $startYear = 2020; 
                $endYear = (int)date('Y') + 1;
                for ($y = $startYear; $y <= $endYear; $y++): ?>
                    <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </label>

    <label>Месец:
        <select name="month" onchange="this.form.submit()">
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i ?>" <?= $i == $month ? 'selected' : '' ?>>
                    <?= date("F", mktime(0, 0, 0, $i, 1)) ?>
                </option>
            <?php endfor; ?>
        </select>
    </label>
</form>


        <form method="POST">
            <input type="hidden" name="month" value="<?= $month ?>">
            <table>
                <tr>
                    <th>Ден</th>
                    <th>Консумация (kWh)</th>
                </tr>
                <?php for ($day = 1; $day <= 31; $day++): ?>
                    <tr>
                        <td><?= $day ?></td>
                        <td>
                            <?php if ($month < $currentMonth || ($month == $currentMonth && $day <= $currentDay)): ?>
                                <input type="number" step="0.01" name="energy[<?= $day ?>]" value="<?= isset($data[$day]) ? $data[$day] : '' ?>">
                            <?php else: ?>
                                <em>-</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endfor; ?>
            </table>

            <button name="save" type="submit">💾 Запази</button>
        </form>

        <h2>Обща консумация: <?= round($total, 2) ?> kWh</h2>

        <h3>🔋 Съвети за пестене:</h3>
        <ul>
            <?php foreach ($tips as $tip): ?>
                <li><?= $tip ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>📈 Графика на дневната консумация</h3>
        <canvas id="energyChart" width="900" height="400"></canvas>

        <a href="logout.php" class="logout">⬅️ Изход</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = <?= json_encode(array_keys($chartData)) ?>;
        const dataValues = <?= json_encode(array_values($chartData)) ?>;

        const ctx = document.getElementById('energyChart').getContext('2d');
        const energyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Консумация (kWh)',
                    data: dataValues,
                    fill: true,
                    borderColor: '#007acc',
                    backgroundColor: 'rgba(0, 122, 204, 0.3)',
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#005fa3',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'kWh'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Ден'
                        }
                    }
                }
            }
        });

        setTimeout(() => location.reload(), 60000);
    </script>
</body>
</html>
