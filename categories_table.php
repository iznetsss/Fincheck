<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="styles/categories.css">
    <link rel="icon" href="img/icon.PNG">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <meta charset="utf-8">
    <meta name="authors" content="kisevt, ikuzne">
    <title>Categories•FinCheck</title>
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
            <a href="https://images.app.goo.gl/bXFCT34kEYP93jZf8">
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
        <h1><a href="categories_chart.php">See Chart</a></h1>
        <div class="flex-zone">
            <table class="expenses-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Budget</th>
                        <th>Actual</th>
                        <th>Left</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Transport</td>
                        <td>500</td>
                        <td>350</td>
                        <td>150</td>
                    </tr>
                    <tr>
                        <td>Groceries</td>
                        <td>250</td>
                        <td>150</td>
                        <td>100</td>
                    </tr>
                    <tr>
                        <td>Eating out</td>
                        <td>100</td>
                        <td>45</td>
                        <td>55</td>
                    </tr>
                    <tr>
                        <td>Coffee</td>
                        <td>20</td>
                        <td>5</td>
                        <td>15</td>
                    </tr>
                    <tr>
                        <td>Fuel</td>
                        <td>350</td>
                        <td>200</td>
                        <td>150</td>
                    </tr>
                    <tr>
                        <td>Health</td>
                        <td>80</td>
                        <td>50</td>
                        <td>30</td>
                    </tr>
                    <tr>
                        <td>Beauty</td>
                        <td>150</td>
                        <td>120</td>
                        <td>30</td>
                    </tr>
                    <tr>
                        <td>Clothes</td>
                        <td>200</td>
                        <td>160</td>
                        <td>400</td>
                    </tr>
                    <tr>
                        <td>Gifts</td>
                        <td>100</td>
                        <td>20</td>
                        <td>80</td>
                    </tr>
                    <tr>
                        <td>Entertainment</td>
                        <td>150</td>
                        <td>80</td>
                        <td>70</td>
                    </tr>
                    <tr>
                        <td>Other</td>
                        <td>100</td>
                        <td>50</td>
                        <td>50</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>