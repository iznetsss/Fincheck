<?php
//Graph logic.
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
//- - - - -Balance logic.- - - - -

$total_spendings = 0.00;
foreach ($spendingsByDays as $spending) {
  $total_spendings += floatval($spending);
}
$total_incomes = 0.00;
foreach ($incomesByDays as $income) {
  $total_incomes += floatval($income);
}
$balance = $total_incomes - $total_spendings;

// Last expenses table.

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
        $correctRowOrder = array(date('d.m', strtotime($data[1])), $data[3], $data[2]);
        $lastExpenses[] = $correctRowOrder;
    }
}
fclose($file);

//Sort data by date (from latest to earliest)
usort($lastExpenses, function($a, $b) {
  $dateA = DateTime::createFromFormat('d.m', $a[0]);
  $dateB = DateTime::createFromFormat('d.m', $b[0]);
  return $dateB <=> $dateA;
});



// Upcoming payments table
//Recurring table.
$file = fopen("data/recurring.csv", "r");
$recurringTable = array();

while (($data = fgetcsv($file, 1000, ";")) !== false) {
    // Check if the row has at least four elements
    if (count($data) >= 4) {
        $name = $data[0];
        // Extract day and month from date
        $date = date('d.m', strtotime($data[1]));
        $amount = $data[2];
        
        // Add formatted data to recurring table
        $recurringTable[] = array($date, $amount, $name);


    }

}
fclose($file);

usort($recurringTable, function($a, $b) {
  $dateA = DateTime::createFromFormat('d.m', $a[0]);
  $dateB = DateTime::createFromFormat('d.m', $b[0]);
  return $dateA <=> $dateB;
});
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
          <h2><a href="#" title="New spending">Spent<i class='bx bx-minus'></i></a></h2>
          <h1><?php echo number_format(floatval($total_spendings), 2); ?></h1>
        </div>
        <div class="balance-div">
          <h2><a href="#" title="New income">Earned<i class='bx bx-plus'></i></a></h2>
          <h1><?php echo number_format(floatval($total_incomes), 2); ?></h1>
        </div>
      </div>
      <div class="simple">
        <span class="header2"><a href="expenses.php" title="See more">This month spendings</a></span>
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