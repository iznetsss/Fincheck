<?php
require ("includes/session_check.php");
require ("includes/sql_connect.php");

$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'spendings') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no spending table found.');
}  
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'incomes') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no income table found.');
}   
//if table exists, getting all needed data
$tableRows = [];
$query = ("SELECT spending_date AS any_date, category, amount, spending_comment AS any_comment, recurring, true AS is_spending FROM spendings 
           WHERE username = '$username'
           UNION ALL
           SELECT income_date AS any_date, category, amount, income_comment AS any_comment, recurring, false AS is_spending FROM incomes 
           WHERE username = '$username'
           ORDER BY any_date DESC;");
$result = mysqli_query($link, $query);
if ($result->num_rows == 0) {
    $noRows = TRUE;
}
else {
    while($row = $result->fetch_assoc()) {
        $date = date('d.m.Y', strtotime($row['any_date']));
        $category = $row['category'];
        $amount = number_format($row['amount'], 2, ".", ",");
        $comment = $row['any_comment'];
        if ($row['recurring']) {
          $recurring = 'Yes';
        }
        else {
          $recurring = 'No';
        }
        $isSpending = $row['is_spending'];

        $tableRow = [$date, $category, $amount, $comment, $recurring, $isSpending];
        array_push($tableRows, $tableRow);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles/advanced.css">
  <link rel="icon" href="img/icon.PNG">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="authors" content="kisevt, ikuzne">
  <title>Advanced•FinCheck</title>
</head>

<body>
  <?php require 'includes/header.php'; ?>
  <?php require 'includes/sidebar.php'; ?>
  <div class="content" id="content">
    <?php if (isset($noRows)) {?>
      <span>No spendings has been made yet.</span>
      <script>
        var content = document.getElementById("content");
          content.style.alignItems = "center";
      </script>
    <?php } else {?>
    <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Note</th>
            <th>Recurring</th>
            <th>Delete Transaction</th>
          </tr>
        </thead>
        <tbody>
          <script>
            function displayRowNumber(rowId) {
                var rowIndex = rowId.split('-')[1];
                alert("You clicked on row " + (parseInt(rowIndex) + 1));
            }
          </script>
            <?php
              if (isset($tableRows)) {
                  foreach ($tableRows as $index => $row) {
                      echo "<tr id='row-$index' onclick='displayRowNumber(this.id)'>";
                      foreach (array_slice($row, 0, -1) as $key => $cell) {
                          echo "<td>";
                          echo $cell;
                          echo "</td>";
                      }
                      // Only add the button if is_spending is defined //without smth not working
                      if (isset($row[5])) {
                          echo '<td><button>Delete</button></td>';
                      }
                      echo "</tr>";
                  }
              }
            ?>
        </tbody>
    </table>
    <?php } ?>
  </div>

  <footer>
    <span>Copyright © 2024 FinCheck OÜ. All rights reserved.</span>
  </footer>
</body>

</html>