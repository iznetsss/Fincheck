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
$query = ("SELECT ID, spending_date AS any_date, category, amount, spending_comment AS any_comment, recurring, true AS is_spending FROM spendings 
           WHERE username = '$username'
           UNION ALL
           SELECT ID, income_date AS any_date, category, amount, income_comment AS any_comment, recurring, false AS is_spending FROM incomes 
           WHERE username = '$username'
           ORDER BY any_date DESC;");
$result = mysqli_query($link, $query);
if ($result->num_rows == 0) {
    $noRows = TRUE;
}
else {
    while($row = $result->fetch_assoc()) {
        $id = $row['ID'];
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

        $tableRow = ["id"=>$id,
                     "date"=>$date, 
                     "category"=>$category, 
                     "amount"=>$amount, 
                     "comment"=>$comment, 
                     "recurring"=>$recurring, 
                     "isSpending"=>$isSpending];

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
    <div class="flex-zone">
      <a id="week">Week</a>
      <a id="month">Month</a>
      <a id="year">Year</a>
      <a id="all">All</a>
      <form id="filter-by-date">
        <input type="date">
        <input type="date">
        <input type="submit" value="Filter">
      </form>
      <script>
        var week = document.getElementById("week");
        var month = document.getElementById("month");
        var year = document.getElementById("year");
        var all = document.getElementById("all");
        week.addEventListener("click", function() {
          fetchData("week");
        });
        month.addEventListener("click", function() {
          fetchData("month");
        });
        year.addEventListener("click", function() {
          fetchData("year");
        });
        all.addEventListener("click", function() {
          fetchData("all");
        });
      </script>
    </div>
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
          </tr>
        </thead>
        <tbody id="table">
        </tbody>
    </table>
    <script>
      function fetchData(period) {
        fetch('process_table.php?period=' + period)
          .then(response => response.json())
          .then(data => {
            loadTable(data);
        });
      }

      function loadTable(jsonTableData) {
        var table = document.getElementById("table");
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }
        jsonTableData.forEach(function(row) {
          
          var tableSize = table.rows.length;
          var newRow = table.insertRow(tableSize);
          newRow.id = row.id + row.isSpending;

          var cellDate = newRow.insertCell(0);
          var cellCategory = newRow.insertCell(1);
          var cellAmount = newRow.insertCell(2);
          if (row.isSpending == 1) {
            cellAmount.classList.add('spending-cell');
          }
          else {
            cellAmount.classList.add('income-cell');
          }
          var cellNote = newRow.insertCell(3);
          var cellRecurring = newRow.insertCell(4);

          cellDate.innerHTML = row.date;
          cellCategory.innerHTML = row.category;
          if (row.isSpending == 1) {
            cellAmount.innerHTML = "-" + row.amount;
          }
          else {
            cellAmount.innerHTML = "+" + row.amount;
          }
          cellNote.innerHTML = row.comment;
          cellRecurring.innerHTML = row.recurring;
        }); 
      }
    loadTable(<?php echo json_encode($tableRows); ?>);
    table.addEventListener("click", function(event) {
      var clickedElement = event.target;

      if (clickedElement.tagName === "TD") {
        var clickedRow = clickedElement.parentNode;
        var rowId = clickedRow.id;
        alert("You clicked on row: " + rowId);
      }
    });
    </script>
    <?php } ?>
  </div>

  <footer>
    <span>Copyright © 2024 FinCheck OÜ. All rights reserved.</span>
  </footer>
</body>

</html>