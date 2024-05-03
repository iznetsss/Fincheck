<?php 
require ("includes/session_check.php");
require ("includes/sql_connect.php");
$monthOffset = isset($_GET['monthOffset']) ? (int) $_GET['monthOffset'] : 0; 
$month = strtotime("-".$monthOffset." month");
$monthFormat = $monthOffset > -12 ? date('F', $month) : date("F Y", $month);
$daysInMonth = date('t', $month);
$spendingsByDays = [];

//Array with all days in current month as keys. Values are set to 0 by default.
for ($i = 1; $i <= $daysInMonth; $i++) {
    $spendingsByDays[$i] = 0;
    $incomesByDays[$i] = 0;
}    
//if table exists, getting spendings by days
$query = ("SELECT DAY(spending_date), amount FROM spendings 
           WHERE username = '$username' AND 
           YEAR(spending_date) = YEAR(CURDATE() + INTERVAL '$monthOffset' MONTH) AND
          MONTH(spending_date) = MONTH(CURDATE() + INTERVAL '$monthOffset' MONTH);");

$result = mysqli_query($link, $query);
if ($result->num_rows == 0) {

}
else {
    while($row = $result->fetch_assoc()) {
        $spendingDate = (int) $row['DAY(spending_date)'];
        $spendingAmount = $row['amount'];
        $spendingsByDays[$spendingDate] += floatval($spendingAmount);
    }
}
   
$totalIncomes = 0.0;
$query = ("SELECT amount FROM incomes 
           WHERE username = '$username' AND 
           YEAR(income_date) = YEAR(CURDATE() + INTERVAL '$monthOffset' MONTH) AND
           MONTH(income_date) = MONTH(CURDATE() + INTERVAL '$monthOffset' MONTH);");

$result = mysqli_query($link, $query);
while($row = $result->fetch_assoc()) {
    $incomeAmount = $row['amount'];
    $totalIncomes += floatval($incomeAmount);
}
$totalSpendings = 0.00;
foreach ($spendingsByDays as $spending) {
  $totalSpendings += floatval($spending);
}
$balance = $totalIncomes - $totalSpendings;
$result = ["balance"=>number_format($balance, 2, ".", ","),
           "totalSpendings"=>number_format($totalSpendings, 2, ".", ","),
           "totalIncomes"=>number_format($totalIncomes, 2, ".", ","),
           "month"=>$monthFormat, 
           "spendings"=>$spendingsByDays];
echo json_encode($result);
?>