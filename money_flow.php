<?php
//EXPENSES
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-button-expense'])) 
{
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
        $allowedTypes = array('Transport', 'Groceries', 'Eating out', 'Coffee', 'Fuel', 'Health', 'Beauty', 'Clothes', 'Gifts', 'Entertainment', 'Other');

        if (!in_array($type, $allowedTypes)) 
        {
            $typeErrorExpenses = "<span style='color:red'>Invalid expense type.<br></span>";
        }

        $date = $_POST['expense-date'];
        if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) // Date regex
        {
            if (strtotime($date) === false) 
            {
                $dateErrorExpenses = "<span style='color:red'>Wrong date input.<br></span>";
            } else 
            {
                list($year, $month, $day) = explode('-', $date);
            } 
        } 
        else 
        {
            $dateErrorExpenses = "<span style='color:red'>Incorrect date format.<br></span>";
        }

        //Enters are spaces in note
        if(isset($_POST['expense-note']))
        {
            $note = str_replace("\r\n", " ", $_POST['expense-note']);
        }

        if (empty($amountErrorExpenses) && empty($typeErrorExpenses) && empty($dateErrorExpenses)) 
        {
            $expenseData = array(); 
            // Set data into array
            $expenseData['date'] = $date;
            $expenseData['type'] = $type;
            $expenseData['amount'] = $amount;
            $expenseData['note'] = $note; 
            $expenseData['recurring'] = "No"; //Recurring is always "No"


            $csv_file_path = 'data/expenses.csv';
            
            if (!file_exists($csv_file_path)) {
                touch($csv_file_path);
                chmod($csv_file_path, 0777); 
            }
    
            $csv_file = fopen($csv_file_path, 'a');
            fputcsv($csv_file, $expenseData, ';');
            fclose($csv_file);

            $checkSumbission = TRUE;
        }
    }
}


//INCOME
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-button-income'])) 
{
    if (isset($_POST['income-amount']) && isset($_POST['income-type']) && isset($_POST['income-date'])) 
    {
        $amount = $_POST['income-amount']; // Get amount
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
                $amountErrorIncome = "<span class='error'>Amount must be between 0.01 and 10000000</span><br>"; // Amount error message
            }
        }
        elseif(empty($amount))
        {
            $amountErrorIncome = "<span class='error'>Wrong number input</span><br>"; // Amount error message
        }
        //Converting $amount to float, rounding, and converting back to string
        $amount = number_format(floatval($amount), 2);

        $type = $_POST['income-type']; // Get type
        $allowedTypes = array('Employment', 'Entrepreneurship', 'Investment', 'Savings', 'Loans', 'Rent', 'Dividends', 'Freelancing', 'Gifts', 'DebtReturn', 'Other');

        if (!in_array($type, $allowedTypes)) 
        {
            $typeErrorIncome = "<span class='error'>Invalid income type</span><br>";
        }

        $date = $_POST['income-date'];
        if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) // Date regex
        {
            if (strtotime($date) === false) 
            {
                $dateErrorIncome = "<span style='color:red'>Wrong date input.</span><br>";
            } else 
            {
                list($year, $month, $day) = explode('-', $date);
            } 
        } 
        else 
        {
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
            $incomeData['date'] = $date;
            $incomeData['type'] = $type;
            $incomeData['amount'] = $amount;
            $incomeData['note'] = $note; 
            $incomeData['recurring'] = "No"; //Recurring is always "No"

            $csv_file_path = 'data/incomes.csv';
            
            if (!file_exists($csv_file_path)) {
                touch($csv_file_path);
                chmod($csv_file_path, 0777); 
            }
    
            $csv_file = fopen($csv_file_path, 'a');
            fputcsv($csv_file, $incomeData, ';');
            fclose($csv_file);

            $checkSumbission = TRUE;
        }
    }
}

//Graph logic.
//Labels (days on x-axis).
$currentDay = date("j");
$daysInMonth = date("t");

if (file_exists("data/expenses.csv")) {
    $spendings = [];
    $file = fopen("data/expenses.csv", "r");
    while (($spending = fgetcsv($file, 1000, ";")) !== FALSE) {
        array_push($spendings, [$spending[0], $spending[2]]);
    }
    fclose($file);
    $spendingsByDays = [];
    for ($i = 1; $i <= $currentDay; $i++) {
        $spendingsByDays[$i] = 0;
    }
    foreach ($spendings as $spending) {
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

if (file_exists("data/incomes.csv")) {
    $incomes = [];
    $file = fopen("data/incomes.csv", "r");
    while (($income = fgetcsv($file, 1000, ";")) !== FALSE) {
        array_push($incomes, [$income[0], $income[2]]);
    }
    fclose($file);
    $incomesByDays = [];
    for ($i = 1; $i <= $currentDay; $i++) {
        $incomesByDays[$i] = 0;
    }
    foreach ($incomes as $income) {
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
                <input class="expense-amount-input" type="number" id="expense-amount" name="expense-amount" min="0" step="0.01">
                <label class="expense-type-label" for="expense-type">Select Expense Type:</label>
                <select class="expense-type-select" id="expense-type" name="expense-type">
                    <option value="Transport">Transport</option>
                    <option value="Groceries">Groceries</option>
                    <option value="Eating out">Eating out</option>
                    <option value="Coffee">Coffee</option>
                    <option value="Fuel">Fuel</option>
                    <option value="Health">Health</option>
                    <option value="Beauty">Beauty</option>
                    <option value="Clothes">Clothes</option>
                    <option value="Gifts">Gifts</option>
                    <option value="Entertainment">Entertainment</option>
                    <option value="Other">Other</option>
                </select> 
                <label class="expense-type-label" for="expense-date">Date:</label>    
                <input class="calender" type="date" id="expense-date" name="expense-date"><br><br>

                <label for="expense-note">Note:</label>    
                <input type="text" id="expense-note"  class="expense-amount-input" name="expense-note">
                <input type="hidden" id="recurring" value="No">

                <input class="btn" type="submit" id="submit-button-expense" name="submit-button-expense" value="Submit Expense">
                <?php
                //Error expense message
                if(isset($amountErrorExpenses, $typeErrorExpenses, $dateErrorExpenses))
                {
                    echo expensesErrorsOutput($amountErrorExpenses, $typeErrorExpenses, $dateErrorExpenses); 
                }
                //Data was added message
                if (!empty($amountErrorExpenses) || !empty($typeErrorExpenses) || !empty($dateErrorExpenses)) {
                    echo outputErrors($amountErrorExpenses, $typeErrorExpenses, $dateErrorExpenses);
                } 
                elseif(isset($checkSumbission))
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
                <input class="income-amount-input" type="number" id="income-amount" name="income-amount" min="0" step="0.01">
                <label class="income-type-label" for="income-type">Select Income Type:</label>
                <select class="income-type-select" id="income-type" name="income-type" >
                    <option value="Employment">Employment</option>
                    <option value="Entrepreneurship">Entrepreneurship</option>
                    <option value="Investment">Investment</option>
                    <option value="Savings">Savings</option>
                    <option value="Loans">Loans</option>
                    <option value="Rent">Rent</option>
                    <option value="Dividends">Dividends</option>
                    <option value="Freelancing">Freelancing</option>
                    <option value="Gifts">Gifts</option>
                    <option value="DebtReturn">Debt Return</option>
                    <option value="Other">Other</option>
                </select> 
                <label class="income-type-label" for="income-date">Date:</label>    
                <input class="calender" type="date" id="income-date" name="income-date"><br><br>
                <label for="income-note">Note:</label>    
                <input type="text" id="income-note"  class="income-amount-input" name="income-note">
                <input type="hidden" id="recurring" value="No">
                <input class="btn" type="submit" id="submit-button-income" name="submit-button-income" value="Submit Income">
                <?php 
                //Error income message
                if(isset($amountErrorIncome, $typeErrorIncome, $dateErrorIncome))
                {
                    echo expensesErrorsOutput($amountErrorIncome, $typeErrorIncome, $dateErrorIncome); 
                } 
                elseif(isset($checkSumbission))
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