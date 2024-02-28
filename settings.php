<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="icon" href="img/icon.PNG">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="authors" content="kisevt, ikuzne">
    <title>Settings•FinCheck</title>
</head>

<body>
    <header>
        <img src="img/logo.png" width="200px">
    </header>
    <div class="sidebar">
        <ul>
        <li>
            <a href="dashboard.php">
            <i class='bx bxs-home'></i>
            <span>Home</span>
            </a>
        </li>
        <li>
            <a href="categories_chart.php">
            <i class='bx bxs-category'></i>
            <span>Categories</span>
            </a>
        </li>
        <li>
            <a href="recurring.php">
            <i class='bx bxs-calendar'></i>
            <span>Recurring</span>
            </a>
        </li>
        <li>
            <a href="money_flow.php">
            <i class='bx bx-line-chart'></i>
            <span>Cash flow</span>
            </a>
        </li>
        <li>
            <a href="advanced.php">
            <i class='bx bx-table'></i>
            <span>Advanced</span>
            </a>
        </li>
        <li>
            <a href="settings.php">
            <i class='bx bxs-cog'></i>
            <span>Settings</span>
            </a>
        </li>
        <li>
            <a href="index.php">
            <i class='bx bx-run'></i>
            <span>Logout</span>
            </a>
        </li>
        </ul>
    </div>
    <div class="content">
        <input id="r1" type="radio" name="radio" value="1">
        <label for="r1">Include recurring payments into montly spending graph</label>
    </div>
    <footer>
        <span>Copyright © 2024 FinCheck OÜ. All rights reserved.</span>
    </footer>
</body>

</html>