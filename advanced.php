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
//if table exists, getting all needed data
$tableRows = [];
$query = ("SELECT spending_date, category, amount, spending_comment, recurring FROM spendings 
           WHERE username = '$username';");
$result = mysqli_query($link, $query);
if ($result->num_rows == 0) {

}
else {
    while($row = $result->fetch_assoc()) {
        $spendingDate = date('d.m.Y', strtotime($row['spending_date']));
        $spendingCategory = $row['category'];
        $spendingAmount = $row['amount'];
        $spendingComment = $row['spending_comment'];
        if ($row['recurring']) {
          $spendingRecurring = 'Yes';
        }
        else {
          $spendingRecurring = 'No';
        }
        $tableRow = [$spendingDate, $spendingCategory, $spendingAmount, $spendingComment, $spendingRecurring];
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
  <div class="content">
    <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Note</th>
            <th>Recurring</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (isset($tableRows)) {
            foreach($tableRows as $row) {
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
  </div>


  <footer>
    <span>Copyright © 2024 FinCheck OÜ. All rights reserved.</span>
  </footer>
</body>

</html>