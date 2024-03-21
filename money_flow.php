<?php
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
                $amountError = "<span class='error'>Amount must be between 0.01 and 10000000</span>"; // Amount error message
                echo $amountError;
            }
        }
        elseif(empty($amount))
        {
            $amountError = "<span class='error'>Wrong number input</span>"; // Amount error message
            echo $amountError;
        }

        $type = $_POST['expense-type']; // Get type
        $allowed_types = array('Transport', 'Groceries', 'Eating out', 'Coffee', 'Fuel', 'Health', 'Beauty', 'Clothes', 'Gifts', 'Entertainment', 'Other');

        if (!in_array($type, $allowed_types)) 
        {
            $typeError = "<span class='error'>Invalid expense type</span>";
            echo $typeError;
        }

        $date = $_POST['expense-date'];
        if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) // Date regex
        {
            if (strtotime($date) === false) 
            {
                $dateError = "<span style='color:red'>Wrong date input.</span><br>";
                echo $dateError;
            } else 
            {
                list($year, $month, $day) = explode('-', $date);
            } 
        } 
        else 
        {
            $dateError = "<span style='color:red'>Incorrect date format.</span><br>";
            echo $dateError;
        }

        $expenseData = array(); 

        # Set data into array
        $expenseData['amount'] = $amount;
        $expenseData['type'] = $type;
        $expenseData['date'] = $date;
        echo 'Expense data added';
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
                $amountError = "<span class='error'>Amount must be between 0.01 and 10000000</span>"; // Amount error message
                echo $amountError;
            }
        }
        elseif(empty($amount))
        {
            $amountError = "<span class='error'>Wrong number input</span>"; // Amount error message
            echo $amountError;
        }

        $type = $_POST['income-type']; // Get type
        $allowed_types = array('Employment', 'Entrepreneurship', 'Investment', 'Savings', 'Loans', 'Rent', 'Dividends', 'Freelancing', 'Gifts', 'DebtReturn', 'Other');

        if (!in_array($type, $allowed_types)) 
        {
            $typeError = "<span class='error'>Invalid income type</span>";
            echo $typeError;
        }

        $date = $_POST['income-date'];
        if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) // Date regex
        {
            if (strtotime($date) === false) 
            {
                $dateError = "<span style='color:red'>Wrong date input.</span><br>";
                echo $dateError;
            } else 
            {
                list($year, $month, $day) = explode('-', $date);
            } 
        } 
        else 
        {
            $dateError = "<span style='color:red'>Incorrect date format.</span><br>";
            echo $dateError;
        }

        $incomeData = array(); 

        # Set data into array
        $incomeData['amount'] = $amount;
        $incomeData['type'] = $type;
        $incomeData['date'] = $date;
        echo 'Income data added';
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
                <input class="expense-amount-input" type="number" id="expense-amount" name="expense-amount" min="0">
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
                <input class="calender" type="date" id="expense-date" name="expense-date">
                <input class="btn" type="submit" id="submit-button-expense" name="submit-button-expense">
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
                <input class="income-amount-input" type="number" id="income-amount" name="income-amount" min="0">
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
                <input class="calender" type="date" id="income-date" name="income-date">
                <input class="btn" type="submit" id="submit-button-income" name="submit-button-income">
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
                        labels : [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27 ],
                        datasets : [{
                            data : [ 33, 6, 60, 0, 20, 22, 16, 0, 3.86, 600, 60, 183, 5, 6, 6, 3, 2, 0, 0, 5, 9, 15, 44, 94, 0, 55, 4 ],
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
                        labels : [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27 ],
                        datasets : [{
                            data : [ 60, 0, 0, 75, 2350, 350, 0, 10, 0, 0, 0, 35, 0, 0, 0, 750, 0, 0, 0, 0, 0, 55, 0, 0, 0, 0, 250 ],
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