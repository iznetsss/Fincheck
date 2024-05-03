<?php 
require ("includes/session_check.php");
require ("includes/sql_connect.php");
$monthOffset = isset($_GET['monthOffset']) ? (int) $_GET['monthOffset'] : 0;
$month = strtotime("-".$monthOffset." month");
$daysInMonth = date('t', $month);
$spendingsByDays = [];

//Array with all days in current month as keys. Values are set to 0 by default.
for ($i = 1; $i <= $daysInMonth; $i++) {
    $spendingsByDays[$i] = 0;
    $incomesByDays[$i] = 0;
}
//Checking if the table exists.
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'spendings') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no spending table found.');
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
$result = ["month"=>date('F', $month), "spendings"=>$spendingsByDays];
echo json_encode($result);
?>