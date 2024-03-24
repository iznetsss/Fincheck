<?php
// Last expenses table

$file = fopen("data/expenses.csv", "r");
$lastExpenses = array();

// Set pointer to the end of file
fseek($file, -1, SEEK_END);

$lineCount = 0;

while (($char = fgetc($file)) !== false) {
    if ($char === "\n") {
        $lineCount++;
    }
    fseek($file, -2, SEEK_CUR);
    if ($lineCount > 6) { // Last 6 expenses
        break;
    }
}

while (($data = fgetcsv($file, 1000, ";")) !== false) {
    // Check if the row has at least three elements
    if (count($data) >= 3) {
        // Rearrange the elements of each row
        $correctRowOrder = array($data[0], $data[2], $data[1]);
        $lastExpenses[] = $correctRowOrder;
    }
}

fclose($file);
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles/dashboard.css">
  <link rel="icon" href="img/icon.PNG">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="authors" content="kisevt, ikuzne">
  <title>Dashboard•FinCheck</title>
</head>

<body>
  <?php include 'includes/header.php'; ?>
  <?php include 'includes/sidebar.php'; ?>
  <div class="content">
    <div class="flex-zone flex-zone-left">
      <div class="flex-container flex-container-left">
        <!--Hide Balance-->
        <div class="balance-div">
          <h2>Balance</h2>
          <h1>788.98</h1>
        </div>
        <div class="balance-div">
          <h2><a href="#" title="New spending">Spent<i class='bx bx-minus'></i></a></h2>
          <h1>1251.86</h1>
        </div>
        <div class="balance-div">
          <h2><a href="#" title="New income">Earned<i class='bx bx-plus'></i></a></h2>
          <h1>1651.07</h1>
        </div>
      </div>
      <div class="simple">
        <span class="header2"><a href="expenses.php" title="See more">This month spendings</a></span>
        <!--
                <label class="spending-container">
                <input class="calender" type="date" id="spending-graph-start"/>
                <input class="calender" type="date" id="spending-graph-end"/>
                <button class="spending-graph-btn icon-search" aria-label="spending-graph-btn"></button>
                -->
        </label>
        <canvas id="line-chart"></canvas>
        <!--Hide recurring payments-->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>
        <script>
          new Chart(document.getElementById("line-chart"), {
            type: 'line',
            data: {
              labels: [1, 2, 3, 4, 5, 6,
                7, 8, 9, 10, 11, 12, 13,
                14, 15, 16, 17, 18, 19, 20,
                21, 22, 23, 24, 25, 26, 27],
              datasets: [
                {
                  data: [33, 6, 60, 0, 20,
                    22, 16, 0, 3.86, 600, 60,
                    183, 5, 6, 6, 3, 2, 0,
                    0, 5, 9, 15, 44, 94, 0, 55, 4],
                  label: "Spendings",
                  borderColor: "#0072ce",
                  backgroundColor: "#0072ce",
                }]
            },
            options: {
              plugins: {
                legend: {
                  display: false,
                }
              },
              aspectRatio: 2,
              title: {
                display: true,
                text: 'line graph example'
              }
            }
          });
        </script>
      </div>
    </div>
    <div class="flex-zone flex-zone-right">
      <div class="flex-container flex-container-right">
        <h2><a href="#">Latest spendings</a></h2>
        <table class="right-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Amount</th>
              <th>Category</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>27.02</td>
              <td>1.00</td>
              <td>Snacks</td>
            </tr>
            <?php

            if (isset($lastExpenses)) {
              foreach($lastExpenses as $row) {
                echo "<tr>";
                foreach($row as $cell) {
                  echo "<td>";
                  echo $cell;
                  echo "</td>";
                }
                echo "</tr>";
              }
            }

            ?>
          </tbody>
        </table>
        <span class="header2-invisible">Invisible text</span>
      </div>
      <div class="flex-container flex-container-right">
        <h2><a href="recurring.php">Upcoming payments</a></h2>
        <table class="right-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Amount</th>
              <th>Type</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>29.02</td>
              <td>5.99</td>
              <td>Telegram premium</td>
            </tr>
            <tr>
              <td>10.03</td>
              <td>600.00</td>
              <td>Rent</td>
            </tr>
            <tr>
              <td>13.03</td>
              <td>8.99</td>
              <td>Netflix</td>
            </tr>
            <tr>
              <td>17.03</td>
              <td>4.29</td>
              <td>Spotify</td>
            </tr>
          </tbody>
        </table>
        <span class="header2-invisible">Invisible text</span>
      </div>
    </div>
  </div>
  <footer>
    <span>Copyright © 2024 FinCheck OÜ. All rights reserved.</span>
  </footer>
</body>

</html>