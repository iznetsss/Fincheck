<?php
require ("includes/session_check.php");
require ("includes/sql_connect.php");


//Categories.
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'categories') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no categories table found.');
}  
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'spendings') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no spending table found.');
} 

$categories = [];
$query = ("SELECT category FROM categories 
           WHERE username = '$username' AND income = FALSE");
$result = mysqli_query($link, $query);
while($row = $result->fetch_assoc()) {
    array_push($categories, $row['category']);
}
$spendingsByCategories = [];
foreach ($categories as $category) {
    $spendingsByCategories[$category] = 0;
}

$query = ("SELECT category, amount FROM spendings 
           WHERE username = '$username' AND 
           YEAR(spending_date) = YEAR(CURDATE()) AND
           MONTH(spending_date) = MONTH(CURDATE());");
$result = mysqli_query($link, $query);
while($row = $result->fetch_assoc()) {
  $spendingsByCategories[$row['category']] += floatval($row['amount']);
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
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    var toggleButton = document.getElementById('toggle');
    var chart = document.getElementById('categories_chart');
    var table = document.getElementById('categories_table');

    toggleButton.onclick = function() {
      if (chart.style.display === 'none') {
        chart.style.display = 'block';
        chart.style.position = 'static';
        table.style.display = 'none';
        table.style.position = 'fixed';
      } else {
        chart.style.display = 'none';
        table.style.display = 'block';
        chart.style.position = 'fixed';
        table.style.position = 'static';
      }
    };
  });
</script>

</head>

<body>
  <?php include 'includes/header.php'; ?>
  <?php include 'includes/sidebar.php'; ?>
  <div class="content">
    <h1><a href="#" id="toggle">Change view</a></h1>
    <div class="flex-zone" id="categories_chart">
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
    <div class="content">
        <div class="flex-zone" id="categories_table">
            <table class="expenses-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Spendings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($spendingsByCategories)) {
                        foreach ($spendingsByCategories as $cat => $sum) {
                            echo "<tr>";
                            echo "<td>";
                            echo $cat;
                            echo "</td>";
                            echo "<td>";
                            echo $sum;
                            echo "</td>";
                            echo "</tr>";

                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
      </div>


</body>

</html>