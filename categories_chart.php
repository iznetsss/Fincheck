<?php
require("includes/session_check.php");
require("includes/sql_connect.php");

// Check for table existence
function checkTableExists($link, $tableName) {
    $query = "SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '$tableName') as table_exists;";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($result);
    if (!$row['table_exists']) {
        die("Database error: no $tableName table found.");
    }
}

checkTableExists($link, 'categories');
checkTableExists($link, 'spendings');

// Retrieve categories
$categories = [];
$query = "SELECT category FROM categories WHERE username = '$username' AND income = FALSE";
$result = mysqli_query($link, $query);
while ($row = $result->fetch_assoc()) {
    array_push($categories, $row['category']);
}
$spendingsByCategories = array_fill_keys($categories, 0);

// Fetch spendings by category
$query = "SELECT category, amount FROM spendings 
          WHERE username = '$username' AND 
          YEAR(spending_date) = YEAR(CURDATE()) AND
          MONTH(spending_date) = MONTH(CURDATE());";
$result = mysqli_query($link, $query);
if ($result->num_rows == 0) {
    $noSpendingRows = TRUE;
} else {
    while ($row = $result->fetch_assoc()) {
        $spendingsByCategories[$row['category']] += floatval($row['amount']);
    }
}

// Fetch total spendings
$query = "SELECT SUM(amount) AS total_spent FROM spendings 
          WHERE username = '$username' AND 
          YEAR(spending_date) = YEAR(CURDATE()) AND
          MONTH(spending_date) = MONTH(CURDATE());";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$total_spent = $row['total_spent'] ?? 0;

// Calculate percentages
$spendingsPercentages = [];
foreach ($spendingsByCategories as $category => $amount) {
    $percent = ($total_spent > 0) ? ($amount / $total_spent * 100) : 0;
    $spendingsPercentages[$category] = $percent;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles/categories.css">
    <link rel="icon" href="img/icon.PNG">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <meta charset="utf-8">
    <meta name="authors" content="kisevt, ikuzne">
    <title>Categories•FinCheck</title>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggleButton = document.getElementById('toggle');
        var chart = document.getElementById('categories_chart');
        var table = document.getElementById('categories_table');

        toggleButton.onclick = function() {
            if (chart.style.display === 'none') {
                chart.style.display = 'flex';
                table.style.display = 'none';
            } else {
                chart.style.display = 'none';
                table.style.display = 'flex';
            }
        };
    });
    </script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class="content">
        <h1><a id="toggle">Change view</a></h1>
        <div class="flex-zone" id="categories_chart">
            <canvas id="pie-chart"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>
            <script>
            new Chart(document.getElementById('pie-chart'), {
                type: 'doughnut',
                data: {
                    labels: <?php 
                        $labels = [];
                        foreach ($spendingsByCategories as $category => $amount) {
                            $percent = number_format($spendingsPercentages[$category], 2);
                            $formattedAmount = number_format($amount, 2);
                            $labels[] = "$category: $percent%";
                        }
                        echo json_encode($labels);
                    ?>,
                    datasets: [{
                        borderColor: ["#FAFAFA"],
                        backgroundColor: ["#922B21", "#1E8449", "#B03A2E", "#239B56", "#76448A",
                                          "#B7950B", "#6C3483", "#B9770E", "#1F618D", "#AF601A",
                                          "#148F77", "#A04000", "#117A65", "#283747", "#5DADE2"],
                        data: <?php echo json_encode(array_values($spendingsByCategories)); ?>,
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Spending Distribution'
                    },
                    responsive: true
                }
            });
            </script>

        </div>
        <div class="flex-zone" id="categories_table">
            <table class="expenses-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Spendings</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($total_spent > 0) {
                        foreach ($spendingsByCategories as $cat => $sum) {
                            echo "<tr><td>$cat</td><td>" . number_format($sum, 2) . "</td><td>" . number_format($spendingsPercentages[$cat], 2) . "%</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No spending records found for this month.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
