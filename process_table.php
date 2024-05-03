<?php 
require ("includes/session_check.php");
require ("includes/sql_connect.php");
$period = isset($_GET['period']) ? $_GET['period'] : "all"; 
$tableRows = [];
if ($period == "all") {
    $query = ("SELECT ID, spending_date AS any_date, category, amount, spending_comment AS any_comment, recurring, true AS is_spending FROM spendings 
           WHERE username = '$username'
           UNION ALL
           SELECT ID, income_date AS any_date, category, amount, income_comment AS any_comment, recurring, false AS is_spending FROM incomes 
           WHERE username = '$username'
           ORDER BY any_date DESC;");
}
else if ($period == "week") {
    $query = ("SELECT ID, spending_date AS any_date, category, amount, spending_comment AS any_comment, recurring, true AS is_spending FROM spendings 
           WHERE username = '$username' 
           AND spending_date >= CURDATE() - INTERVAL (DAYOFWEEK(CURDATE()) - 2) DAY
           AND spending_date <= CURDATE() + INTERVAL (1 - DAYOFWEEK(CURDATE())) DAY + INTERVAL 7 DAY
           UNION ALL
           SELECT ID, income_date AS any_date, category, amount, income_comment AS any_comment, recurring, false AS is_spending FROM incomes 
           WHERE username = '$username'
           AND income_date >= CURDATE() - INTERVAL (DAYOFWEEK(CURDATE()) - 2) DAY
           AND income_date <= CURDATE() + INTERVAL (1 - DAYOFWEEK(CURDATE())) DAY + INTERVAL 7 DAY
           ORDER BY any_date DESC;");
}
else if ($period == "month") {
    $query = ("SELECT ID, spending_date AS any_date, category, amount, spending_comment AS any_comment, recurring, true AS is_spending FROM spendings 
           WHERE username = '$username' 
           AND YEAR(spending_date) = YEAR(CURDATE())
           AND MONTH(spending_date) = MONTH(CURDATE())
           UNION ALL
           SELECT ID, income_date AS any_date, category, amount, income_comment AS any_comment, recurring, false AS is_spending FROM incomes 
           WHERE username = '$username'
           AND YEAR(income_date) = YEAR(CURDATE())
           AND MONTH(income_date) = MONTH(CURDATE())
           ORDER BY any_date DESC;");
}
else if ($period == "year") {
    $query = ("SELECT ID, spending_date AS any_date, category, amount, spending_comment AS any_comment, recurring, true AS is_spending FROM spendings 
           WHERE username = '$username' 
           AND YEAR(spending_date) = YEAR(CURDATE())
           UNION ALL
           SELECT ID, income_date AS any_date, category, amount, income_comment AS any_comment, recurring, false AS is_spending FROM incomes 
           WHERE username = '$username'
           AND YEAR(income_date) = YEAR(CURDATE())
           ORDER BY any_date DESC;");
}

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
echo json_encode($tableRows);
?>