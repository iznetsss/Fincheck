<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit; 
}



//- - - - -Graph logic.- - - - -
$currentDate = new DateTime();
$currentYear = $currentDate->format('Y');
$currentMonth = $currentDate->format('m');
$currentDay = date("j");
$daysInMonth = date("t");

if (file_exists("data/expenses.csv")) {
    $spendings = [];
    $file = fopen("data/expenses.csv", "r");
    while (($spending = fgetcsv($file, 1000, ";")) !== FALSE) {
        array_push($spendings, [$spending[1], $spending[3]]);
    }
    fclose($file);
    $spendingsByDays = [];
    for ($i = 1; $i <= $currentDay; $i++) {
        $spendingsByDays[$i] = 0;
    }
    foreach ($spendings as $spending) {
      $spendingDate = new DateTime($spending[0]);
      $spendingYear = $spendingDate->format('Y');
      $spendingMonth = $spendingDate->format('m');
      if ($currentYear === $spendingYear && $currentMonth === $spendingMonth) {
          if (substr($spending[0], 8, 2)[0] != "0") {
              $day = substr($spending[0], 8, 2);
          }
          else {
              $day = substr($spending[0], 9, 1);
          }
          if ($day <= $currentDay) {
              $spendingsByDays[$day] += floatval($spending[1]);
          }
      }
   }
}
if (file_exists("data/incomes.csv")) {
  $incomes = [];
  $file = fopen("data/incomes.csv", "r");
  while (($income = fgetcsv($file, 1000, ";")) !== FALSE) {
      array_push($incomes, [$income[1], $income[3]]);
  }
  fclose($file);
  $incomesByDays = [];
  for ($i = 1; $i <= $currentDay; $i++) {
      $incomesByDays[$i] = 0;
  }
  foreach ($incomes as $income) {
      $incomeDate = new DateTime($income[0]);
      $incomeYear = $incomeDate->format('Y');
      $incomeMonth = $incomeDate->format('m');
      if ($currentYear === $incomeYear && $currentMonth === $incomeMonth) {
          if (substr($income[0], 8, 2)[0] != "0") {
              $day = substr($income[0], 8, 2);
          }
          else {
              $day = substr($income[0], 9, 1);
          }
          if ($day <= $currentDay) {
              $incomesByDays[$day] += floatval($income[1]);
          }
      }
  }
}

//- - - - -BALANCE LOGIC.- - - - -
$total_spendings = 0.00;
foreach ($spendingsByDays as $spending) {
  $total_spendings += floatval($spending);
}
$total_incomes = 0.00;
foreach ($incomesByDays as $income) {
  $total_incomes += floatval($income);
}
$balance = $total_incomes - $total_spendings;

//- - - - -LATEST EXPENSES TABLE.- - - - -
$file = fopen("data/expenses.csv", "r");
$lastExpenses = array();

while (($data = fgetcsv($file, 1000, ";")) !== false) {
  $name = $data[2];
  $date = date('d.m', strtotime($data[1])); // Extract day and month from date
  $amount = $data[3];
  
  // Add formatted data to recurring table
  $lastExpenses[] = array($date, $amount, $name);
}
fclose($file);

//Sort data by date (from latest to earliest)
usort($lastExpenses, function($a, $b) {
  $dateA = DateTime::createFromFormat('d.m', $a[0]);
  $dateB = DateTime::createFromFormat('d.m', $b[0]);
  return $dateB <=> $dateA;
});
$lastExpenses = array_slice($lastExpenses, 0, 8); //8 latest spendinds


// Upcoming payments table
//Recurring table.
$file = fopen("data/recurring.csv", "r");
$recurringTable = array();

while (($data = fgetcsv($file, 1000, ";")) !== false) {
  $name = $data[0];
  $date = date('d.m', strtotime($data[1])); // Extract day and month from date
  $amount = $data[2];
  
  // Add formatted data to recurring table
  $recurringTable[] = array($date, $amount, $name);
}
fclose($file);
//Sort data by date (from latest to earliest)
usort($recurringTable, function($a, $b) 
{
  $dateA = DateTime::createFromFormat('d.m', $a[0]);
  $dateB = DateTime::createFromFormat('d.m', $b[0]);
  return $dateA <=> $dateB;
});
$recurringTable = array_slice($recurringTable, 0, 6) //7 upcoming payments

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
          <h1><?php echo number_format(floatval($balance), 2); ?></h1>
        </div>
        <div class="balance-div">
          <h2><a href="money_flow.php" title="New spending">Spent<i class='bx bx-minus'></i></a></h2>
          <h1><?php echo number_format(floatval($total_spendings), 2); ?></h1>
        </div>
        <div class="balance-div">
          <h2><a href="money_flow.php" title="New income">Earned<i class='bx bx-plus'></i></a></h2>
          <h1><?php echo number_format(floatval($total_incomes), 2); ?></h1>
        </div>
      </div>
      <div class="simple">
        <span class="header2"><a href="advanced.php" title="See more">This month spendings</a></span>
        </label>
        <canvas id="line-chart"></canvas>
        <!--Hide recurring payments-->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>
        <script>
          new Chart(document.getElementById("line-chart"), {
            type: 'line',
            data: {
              datasets: [
                {
                  data: <?php echo json_encode($spendingsByDays); ?>,
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
        <h2><a href="advanced.php">Latest spendings</a></h2>
        <table class="right-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Amount</th>
              <th>Category</th>
            </tr>
          </thead>
          <tbody>
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
            <?php

            if (isset($recurringTable)) {
              foreach($recurringTable as $row) {
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
    </div>
  </div>
  <footer>
    <span>Copyright © 2024 FinCheck OÜ. All rights reserved.</span>
  </footer>
</body>

</html>