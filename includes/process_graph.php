<?php 
require("session_check.php");
require("sql_connect.php");
$monthOffset = isset($_GET['monthOffset']) ? (int)$_GET['monthOffset'] : 0;

function financialSummary($link, $username, $monthOffset) {
    $month = strtotime($monthOffset." month");
    $monthFormat = $monthOffset > -12 ? date('F', $month) : date("F Y", $month);
    $daysInMonth = date('t', $month);
    $spendingsByDays = array_fill(1, $daysInMonth, 0);

    // Retrieve spendings by day
    $query = "SELECT DAY(spending_date) AS day, SUM(amount) AS amount FROM spendings
              WHERE username = ? AND YEAR(spending_date) = YEAR(CURDATE() + INTERVAL ? MONTH) AND
              MONTH(spending_date) = MONTH(CURDATE() + INTERVAL ? MONTH)
              GROUP BY DAY(spending_date);";
    $stmt = $link->prepare($query);
    $stmt->bind_param('sii', $username, $monthOffset, $monthOffset);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $spendingsByDays[$row['day']] += floatval($row['amount']);
    }
    $stmt->close();

    // Calculate total incomes
    $totalIncomes = 0.0;
    $query = "SELECT SUM(amount) AS amount FROM incomes
              WHERE username = ? AND YEAR(income_date) = YEAR(CURDATE() + INTERVAL ? MONTH) AND
              MONTH(income_date) = MONTH(CURDATE() + INTERVAL ? MONTH);";
    $stmt = $link->prepare($query);
    $stmt->bind_param('sii', $username, $monthOffset, $monthOffset);
    $stmt->execute();
    $stmt->bind_result($incomeAmount);
    if ($stmt->fetch()) {
        $totalIncomes = floatval($incomeAmount);
    }
    $stmt->close();

    $totalSpendings = array_sum($spendingsByDays);
    $balance = $totalIncomes - $totalSpendings;

    return [
        "balance" => number_format($balance, 2, ".", ","),
        "totalSpendings" => number_format($totalSpendings, 2, ".", ","),
        "totalIncomes" => number_format($totalIncomes, 2, ".", ","),
        "month" => $monthFormat, 
        "spendings" => $spendingsByDays
    ];
}


function carryOverBalance($link, $username, $monthOffset) {
    $totalIncomes = 0.0;
    $totalSpendings = 0.0;

    // Retrieve all-time totals of incomes up to the end of the specified month
    $query = "SELECT SUM(amount) AS totalIncomes
              FROM incomes
              WHERE username = ?
              AND income_date <= LAST_DAY(CURDATE() + INTERVAL ? MONTH);";
    $stmt = $link->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $link->error);
    }
    $stmt->bind_param('si', $username, $monthOffset);
    $stmt->execute();
    $stmt->bind_result($totalIncomes);
    if (!$stmt->fetch()) {
        $totalIncomes = 0; 
    }
    $stmt->close();

    // Retrieve all-time totals of spendings up to the end of the specified month
    $query = "SELECT SUM(amount) AS totalSpendings
              FROM spendings
              WHERE username = ?
              AND spending_date <= LAST_DAY(CURDATE() + INTERVAL ? MONTH);";
    $stmt = $link->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $link->error);
    }
    $stmt->bind_param('si', $username, $monthOffset);
    $stmt->execute();
    $stmt->bind_result($totalSpendings);
    if (!$stmt->fetch()) {
        $totalSpendings = 0; 
    }
    $stmt->close();

    $balance = $totalIncomes - $totalSpendings;

    return [
        "balance" => number_format($balance, 2, ".", ","),
        "totalSpendings" => number_format($totalSpendings, 2, ".", ","),
        "totalIncomes" => number_format($totalIncomes, 2, ".", ",")
    ];
}

function getCarryOver($link, $username) {
    $query = "SELECT carryOver FROM users WHERE username = ?";
    $stmt = $link->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $link->error);
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($carryOver);
    $stmt->fetch();
    $stmt->close();

    if ($carryOver == 1) {
        $carryOver = 1;
    } else {
        $carryOver = 0;
    }


    return $carryOver;
}

$carryOver = getCarryOver($link, $username);

if ($carryOver == 1) {
    $carryOverBalance = carryOverBalance($link, $username, $monthOffset);
    $summary = financialSummary($link, $username, $monthOffset);
    
    $result = [
        "balance" => ($carryOver == 1) ? $carryOverBalance['balance'] : $summary['balance'],
        "totalSpendings" => $summary['totalSpendings'],
        "totalIncomes" => $summary['totalIncomes'],
        "month" => $summary['month'],
        "spendings" => $summary['spendings']
    ];
} else {
    $summary = financialSummary($link, $username, $monthOffset);
    $result = [
        "balance" => $summary['balance'],
        "totalSpendings" => $summary['totalSpendings'],
        "totalIncomes" => $summary['totalIncomes'],
        "month" => $summary['month'],
        "spendings" => $summary['spendings']
    ];
}

echo json_encode($result);
?>
