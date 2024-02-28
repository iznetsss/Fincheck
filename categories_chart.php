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
            <a href="https://www.instagram.com/">
            <i class='bx bxs-user'></i>
            <span>Profile</span>
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
    <h1><a href="categories_table.php">See Table</a></h1>
    <div class="flex-zone" id="bublik">
      <canvas id="pie-chart"></canvas>
      <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>
      <script>
        new Chart(document.getElementById('pie-chart'), {
          type: 'doughnut',
          data: {
            labels: ["Groceries", "Rent", "Utilities", "Car", "Clothes", "Health", "Eating out", "Other"],
            datasets: [{
              borderColor: ["#6fb7a0", "#746fb7", "#B76f70", "#A7b76f", "#6fb7b6", "#B7956f", "#D45950", "#c7c7c7"],
              backgroundColor: ["#6fb7a0", "#746fb7", "#B76f70", "#A7b76f", "#6fb7b6", "#B7956f", "#D45950", "#c7c7c7"],
              data: [103, 600, 113, 105, 55, 0, 20, 7]
            }]
          },
          options: {
            title: {
              display: true,
              text: 'pie chart example'
            },
            responsive: true
          }
        });
      </script>
    </div>
  </div>

</body>

</html>