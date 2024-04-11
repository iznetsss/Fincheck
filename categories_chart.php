<?php
require ("includes/session_check.php");

$currentDate = new DateTime();
$currentYear = $currentDate->format('Y');
$currentMonth = $currentDate->format('m');
//Categories.
if (file_exists("data/spending_categories.csv")) {
    $file = fopen("data/spending_categories.csv", "r");
    $categories = fgetcsv($file, 1000, ";");
    fclose($file);  
}
$spendingsByCategories = [];
foreach ($categories as $category) {
    $spendingsByCategories[$category] = 0;
}

if (file_exists("data/expenses.csv")) {
    $spendings = [];
    $file = fopen("data/expenses.csv", "r");
    while (($spending = fgetcsv($file, 1000, ";")) !== FALSE) {
        $spendingDate = new DateTime($spending[1]);
        $spendingYear = $spendingDate->format('Y');
        $spendingMonth = $spendingDate->format('m');
        if ($currentYear === $spendingYear && $currentMonth === $spendingMonth) {
        array_push($spendings, [$spending[2], $spending[3]]);
        }
    }
    fclose($file);
    
    foreach ($spendings as $spending) {
        $category = $spending[0];
        $spendingsByCategories[$category] += floatval($spending[1]);
    }
}

?>
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
            labels: <?php echo json_encode(array_keys($spendingsByCategories)); ?>,
            datasets: [{
              borderColor: ["#6fb7a0", "#746fb7", "#B76f70", "#A7b76f", "#6fb7b6", "#B7956f", "#D45950", "#c7c7c7"],
              backgroundColor: ["#6fb7a0", "#746fb7", "#B76f70", "#A7b76f", "#6fb7b6", "#B7956f", "#D45950", "#c7c7c7"],
              data: <?php echo json_encode(array_values($spendingsByCategories)); ?>, 
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