<?php
require ("includes/session_check.php");

  if (file_exists("data/expenses.csv")) {
      $rows = [];
      $file = fopen("data/expenses.csv", "r");
      while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {
        $row[1] = date('d.m', strtotime($row[1]));
        array_push($rows, $row);
      }
      usort($rows, function($a, $b) {
        return strtotime($b[1]) - strtotime($a[1]);
      });
    
    fclose($file);
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
  <?php include 'includes/header.php'; ?>
  <?php include 'includes/sidebar.php'; ?>
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
          if (isset($rows)) {
            foreach($rows as $row) {
              $noNumber = array_slice($row, 1, -1);
              echo "<tr>";
              foreach($noNumber as $cell) {
                echo "<td>";
                echo $cell;
                echo "</td>";
              }
              echo "<td>";
              echo '<span class="cell-left">'.end($row).'</span>';
              echo '<span class="cell-right"><a href="edit.php?id='.$row[0].'">Edit</a></span>';
              echo "</td>";
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