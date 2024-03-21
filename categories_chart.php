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
  <?php include 'includes/header.php'; ?>
  <?php include 'includes/sidebar.php'; ?>
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