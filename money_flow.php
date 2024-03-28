<?php
$amountErrorExpenses = $typeErrorExpenses = $dateErrorExpenses = $amountErrorIncome = $typeErrorIncome = $dateErrorIncome = '';

function expensesErrorsOutput($amountErrorExpenses, $typeErrorExpenses, $dateErrorExpenses) {
    $output = '<br>';
    if(isset($amountErrorExpenses)) {
        $output .= $amountErrorExpenses;
    }
    if(isset($typeErrorExpenses)) {
        $output .= $typeErrorExpenses;
    }
    if(isset($dateErrorExpenses)) {
        $output .= $dateErrorExpenses;
    }
    return $output;
}

function incomeErrorsOutput($amountErrorIncome, $typeErrorIncome, $dateErrorIncome) {
    $output = '<br>';
    if(isset($amountErrorIncome)) {
        $output .= $amountErrorIncome;
    }
    if(isset($typeErrorIncome)) {
        $output .= $typeErrorIncome;
    }
    if(isset($dateErrorIncome)) {
        $output .= $dateErrorIncome;
    }
    return $output;
}
//EXPENSES
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-button-expense'])) 
{
   
    if (isset($_POST['expense-amount']) && isset($_POST['expense-type']) && isset($_POST['expense-date'])) 
    {
        $amount = $_POST['expense-amount']; // Get amount
        if(isset($amount) && preg_match('/^\d+(\.\d{1,2})?$/', $amount)) 
        {
            if(!ctype_digit($amount)) // Check if it is not a whole number...
            {
                if (strpos($amount, ',') !== false && strpos($amount, '.') === false) // If there is a comma and no period, change the comma to a period
                {
                    $amount = str_replace(',', '.', $amount);
                }
                elseif (strpos($amount, '.') === false) // If there is a period
                {
                    // Do nothing
                }
            }

            if(!($amount >= 0.01 && $amount <= 10000000)) // Amount must be between 0.01 and 10000000
            {
                $amountErrorExpenses = "<span style='color:red'>Amount must be between 0.01 and 10000000.</span><br>"; // Amount error message
            }
        }
        elseif(empty($amount))
        {
            $amountErrorExpenses = "<span style='color:red'>Wrong number input</span><br>"; // Amount error message
        }
        //Converting $amount to float, rounding, and converting back to string
        $amount = number_format(floatval($amount), 2);

        $type = $_POST['expense-type']; // Get type
        if (file_exists("data/spending_categories.csv")) {
            $file = fopen("data/spending_categories.csv", "r");
            $allowedTypes = fgetcsv($file, 1000, ";");
            fclose($file);  
        }
        else {
            $allowedTypes = array('Transport', 'Groceries', 'Eating out', 'Coffee', 'Fuel', 'Health & Beauty', 'Clothes', 'Gifts', 'Entertainment', 'Other');
        }
        

        if (!in_array($type, $allowedTypes)) 
        {
            $typeErrorExpenses = "<span style='color:red'>Invalid expense type.<br></span>";
        }

        // DATE
        $date = $_POST['expense-date'];
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    
        if ($dateObj && $dateObj->format('Y-m-d') === $date) 
        {
            // Valid date
            $year = $dateObj->format('Y');
            $month = $dateObj->format('m');
            $day = $dateObj->format('d');
        } else {
            // Invalid date 
            $dateErrorExpenses = "<span style='color:red'>Incorrect date format.</span><br>";
        }

        //Enters are spaces in note
        if(isset($_POST['expense-note']))
        {
            $note = str_replace("\r\n", " ", $_POST['expense-note']);
        }

        if (empty($amountErrorExpenses) && empty($typeErrorExpenses) && empty($dateErrorExpenses)) 
        {
            $csv_file_path = 'data/expenses.csv';
            $expenseData = array(); 
            // Set data into array
            $expenseData['count'] = count(file($csv_file_path)) + 1;
            $expenseData['date'] = $date;
            $expenseData['type'] = $type;
            $expenseData['amount'] = $amount;
            $expenseData['note'] = $note; 
            $expenseData['recurring'] = "No"; //Recurring is always "No"
            

            
            
            if (!file_exists($csv_file_path)) {
                touch($csv_file_path);
                chmod($csv_file_path, 0777); 
            }
            $csv_file = fopen($csv_file_path, 'a');
            fputcsv($csv_file, $expenseData, ';');
            fclose($csv_file);

            $checkSumbissionExpenses = TRUE;
        }
    }
}

//INCOME
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-button-income'])) 
{
    if (isset($_POST['income-amount']) && isset($_POST['income-type']) && isset($_POST['income-date'])) 
    {
        $amount = $_POST['income-amount']; // Get amount
        if(isset($amount) && preg_match("/^\d+(\.\d{2})?$/", $amount)) 
        {
            if(!ctype_digit($amount)) // Check if it is not a whole number...
            {
                if (strpos($amount, ',') !== false && strpos($amount, '.') === false) // If there is a comma and no period, change the comma to a period
                {
                    $amount = str_replace(',', '.', $amount);
                }
                elseif (strpos($amount, '.') === false) // If there is a period
                {
                    // Do nothing
                    //THEN WHY EVEN HAVE THIS STATEMENT???
                }
            }

            if(!($amount >= 0.01 && $amount <= 10000000)) // Amount must be between 0.01 and 10000000
            {
                $amountErrorIncome = "<span style='color:red'>Amount must be between 0.01 and 10000000</span><br>"; // Amount error message
            }
        }
        else
        {
            $amountErrorIncome = "<span style='color:red'>Wrong number input</span><br>"; // Amount error message
        }
        //Converting $amount to float, rounding, and converting back to string
        $amount = number_format(floatval($amount), 2);

        $type = $_POST['income-type']; // Get type
        if (file_exists("data/income_categories.csv")) {
            $file = fopen("data/income_categories.csv", "r");
            $allowedTypes = fgetcsv($file, 1000, ";");
            fclose($file);  
        }
        else {
            $allowedTypes = array('Employment', 'Entrepreneurship', 'Investment', 'Savings', 'Loans', 'Rent', 'Dividends', 'Freelancing', 'Gifts', 'Debt Return', 'Other');
        }
        if (!in_array($type, $allowedTypes)) 
        {
            $typeErrorIncome = "<span style='color:red'>Invalid income type</span><br>";
        }

        // DATE
        $date = $_POST['income-date'];
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    
        if ($dateObj && $dateObj->format('Y-m-d') === $date) 
        {
            // Valid date
            $year = $dateObj->format('Y');
            $month = $dateObj->format('m');
            $day = $dateObj->format('d');
        } else {
            // Invalid date 
            $dateErrorIncome = "<span style='color:red'>Incorrect date format.</span><br>";
        }

        //Enters are spaces in note
        if(isset($_POST['income-note']))
        {
            $note = str_replace("\r\n", " ", $_POST['income-note']);
        }

         
        if (empty($amountErrorIncome) && empty($typeErrorIncome) && empty($dateErrorIncome)) 
        {
            $incomeData = array(); 
            // Set data into array
            $incomeData['count'] = count(file('data/incomes.csv')) + 1;
            $incomeData['date'] = $date;
            $incomeData['type'] = $type;
            $incomeData['amount'] = $amount;
            $incomeData['note'] = $note; 
            $incomeData['recurring'] = "No"; //Recurring is always "No"
            
            if (!file_exists('data/incomes.csv')) {
                touch('data/incomes.csv');
                chmod('data/incomes.csv', 0777); 
            }
    
            $csv_file = fopen('data/incomes.csv', 'a');
            fputcsv($csv_file, $incomeData, ';');
            fclose($csv_file);

            $checkSumbissionIncome = TRUE;
        }
    }
}

//Graph logic.
//Labels (days on x-axis).
$currentDate = new DateTime();
$currentYear = $currentDate->format('Y');
$currentMonth = $currentDate->format('m');
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
        $spendingDate = new DateTime($spending[0]);
        $spendingYear = $spendingDate->format('Y');
        $spendingMonth = $spendingDate->format('m');
        if ($currentYear === $spendingYear && $currentMonth === $spendingMonth) {
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
        $incomeDate = new DateTime($income[0]);
        $incomeYear = $incomeDate->format('Y');
        $incomeMonth = $incomeDate->format('m');
        if ($currentYear === $incomeYear && $currentMonth === $incomeMonth) {
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
}
//Categories.
if (file_exists("data/spending_categories.csv")) {
    $file = fopen("data/spending_categories.csv", "r");
    $spendingCategories = fgetcsv($file, 1000, ";");
    fclose($file);  
}
if (file_exists("data/income_categories.csv")) {
    $file = fopen("data/income_categories.csv", "r");
    $incomeCategories = fgetcsv($file, 1000, ";");
    fclose($file);  
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles/money-flow.css">
    <link rel="icon" href="img/icon.PNG">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <meta charset="utf-8">
    <meta name="authors" content="kisevt, ikuzne">
    <title>Cashflow•FinCheck</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
      <div class="flex-zone", id="flex-zone-expenses">
        <div class="flex-container", id="expenses-input">
            <form method="POST" action="">
                <h3>Expenses</h3>
                <label for="expense-amount">Amount:</label>
                <input class="expense-amount-input" type="number" id="expense-amount" name="expense-amount" min="0" step="0.01" pattern="\d+(\.\d{2})?">
                <label class="expense-type-label" for="expense-type">Select Expense Type:</label>
                <select class="expense-type-select" id="expense-type" name="expense-type">
                    <?php
                    foreach ($spendingCategories as $category) {
                        echo "<option>";
                        echo $category;
                        echo "</option>";
                    }
                    ?>
                </select> 
                <label class="expense-type-label" for="expense-date">Date:</label>    
                <input class="calender" type="date" id="expense-date" name="expense-date"><br><br>

                <label for="expense-note">Note:</label>    
                <input type="text" id="expense-note"  class="expense-amount-input" name="expense-note">
                <input type="hidden" id="recurring" value="No">

                <input class="btn" type="submit" id="submit-button-expense" name="submit-button-expense" value="Submit Expense">
                <?php
                if (!empty($amountErrorExpenses) || !empty($typeErrorExpenses) || !empty($dateErrorExpenses)) {
                    echo expensesErrorsOutput($amountErrorExpenses, $typeErrorExpenses, $dateErrorExpenses);
                } 
                elseif(isset($checkSumbissionExpenses))
                {
                    echo '<p>Expense was added successfully</p>';
                }
                ?>
            </form>
        </div>
        <div class="flex-container", id="expenses-table">
            <h4>The Chart of Expenses</h4>
            <label class="spending-container">
            <input class="calender" type="date" id="spending-graph-start"/>
            <input class="calender" type="date" id="spending-graph-end"/>
            <button class="graph-btn icon-search" aria-label="graph-btn"></button>
            </label>
            <canvas id="line-chart-expenses"></canvas>
        </div>
      </div>
      <div class="flex-zone", id="flex-zone-income">
        <div class="flex-container", id="income-input">
            <form method="POST" action="">
                <h3>Income</h3>
                <label for="income-amount">Amount:</label>
                <input class="income-amount-input" type="number" id="income-amount" name="income-amount" min="0" step="0.01" pattern="\d+(\.\d{2})?">
                <label class="income-type-label" for="income-type">Select Income Type:</label>
                <select class="income-type-select" id="income-type" name="income-type" >
                    <?php
                        foreach ($incomeCategories as $category) {
                            echo '<option value="'.$category.'">';
                            echo $category;
                            echo "</option>";
                        }
                    ?>
                </select> 
                <label class="income-type-label" for="income-date">Date:</label>    
                <input class="calender" type="date" id="income-date" name="income-date"><br><br>
                <label for="income-note">Note:</label>    
                <input type="text" id="income-note"  class="income-amount-input" name="income-note">
                <input type="hidden" id="recurring" value="No">
                <input class="btn" type="submit" id="submit-button-income" name="submit-button-income" value="Submit Income">
                <?php 
                if (!empty($amountErrorIncome) || !empty($typeErrorIncome) || !empty($dateErrorIncome)) {
                    echo expensesErrorsOutput($amountErrorIncome, $typeErrorIncome, $dateErrorIncome);
                } 
                elseif(isset($checkSumbissionIncome))
                {
                    echo '<p>Income was added successfully</p>';
                }
                ?>
            </form>
        </div>
        <div class="flex-container", id="income-table">
          <h4>The Chart of Incomes</h4>
          <label class="income-container">
            <input class="calender" type="date" id="income-graph-start"/>
            <input class="calender" type="date" id="income-graph-end"/>
            <button class="graph-btn icon-search" aria-label="graph-btn"></button>
            </label>
            <canvas id="line-chart-income"></canvas>
            

            <!--JS SCRIPT-->
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>

            <script>
                new Chart(document.getElementById("line-chart-expenses"), {
                    type : 'line',
                    data : {
                        
                        datasets : [{
                            data : <?php echo json_encode($spendingsByDays); ?>,
                            label : "Spendings",
                            borderColor : "#F01F51",
                            backgroundColor : "#F01F51",
                        }]
                    },
                    options : {
                        plugins : {
                            legend : {
                                display: false,
                            }
                        },
                        aspectRatio: 2,
                        maintainAspectRatio: true,
                        title : {
                            display : true,
                            text : 'Expenses Over Time'
                        }
                    }
                });
            
                new Chart(document.getElementById("line-chart-income"), {
                    type : 'line',
                    data : {
                         //Always last day in today day
                        datasets : [{
                            data : <?php echo json_encode($incomesByDays); ?>,
                            label : "Income",
                            borderColor : "#0072ce",
                            backgroundColor : "#0072ce",
                        }]
                    },
                    options : {
                        plugins : {
                            legend : {
                                display: false,
                            }
                        },
                        aspectRatio: 2,
                        maintainAspectRatio: true,
                        title : {
                            display : true,
                            text : 'Income Over Time'
                        }
                    }
                });
            </script>
        </div>
      </div>
    </div>
</body>
</html>