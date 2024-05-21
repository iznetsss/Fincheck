<?php
require ("includes/session_check.php");
require ("includes/sql_connect.php");
//Categories.

$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'categories') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no categories table found.');
}  
$spendingCategories = [];
$incomeCategories = [];
$query = ("SELECT category, income FROM categories 
           WHERE username = '$username'");
$result = mysqli_query($link, $query);
while($row = $result->fetch_assoc()) {
    if ($row['income']) {
        array_push($incomeCategories, $row['category']);
    }
    else {
        array_push($spendingCategories, $row['category']);
    }
}

$amountErrorExpenses = $typeErrorExpenses = $dateErrorExpenses = $noteErrorExpenses = $newCategoryErrorExpenses = 
$amountErrorIncome = $typeErrorIncome = $dateErrorIncome = $noteErrorIncome = $newCategoryErrorIncome = '';

function expensesErrorsOutput($amountErrorExpenses, $typeErrorExpenses, $dateErrorExpenses, $noteErrorExpenses, $newCategoryErrorExpenses) {
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
    if(isset($noteErrorExpenses)) {
        $output .= $noteErrorExpenses;
    }
    if(isset($newCategoryErrorExpenses)) {
        $output .= $newCategoryErrorExpenses;
    }
    return $output;
}

function incomeErrorsOutput($amountErrorIncome, $typeErrorIncome, $dateErrorIncome, $noteErrorIncome, $newCategoryErrorIncome) {
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
    if(isset($noteErrorIncome)) {
        $output .= $noteErrorIncome;
    }
    if(isset($newCategoryErrorIncome)) {
        $output .= $newCategoryErrorIncome;
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
        $amount = number_format(floatval($amount), 2, ".", "");

        $type = $_POST['expense-type']; // Get type
        if (!in_array($type, $spendingCategories) && $type != "new") 
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

        //Enters are spaces in note ADD VALIDATION!!!
        if(!empty($_POST['expense-note']))
        {   
            $note = str_replace("\r\n", " ", $_POST['expense-note']);
            if (!preg_match('/^[a-zA-Z0-9,:;<>()."\' -]{1,100}$/', $_POST['expense-note'])) {
                $noteErrorExpenses = "<span style='color:red'>Your note does not satisfy the required format. You can use letters, numbers and any of these symbols: ,:;<>().\"' -</span><br>";
            }
        } else {
            $note = "";
        }
        //NEW CATEGORIES
        if ($type == "new" && empty($amountErrorExpenses) && empty($typeErrorExpenses) && empty($dateErrorExpenses) && empty($noteErrorExpenses)) {
            if (empty($_POST['new_expense_category'])) {
                $newCategoryErrorExpenses = "<span style='color:red'>Please enter the name for your new category or select an existing one.<br></span>";
            } 
            if (empty($newCategoryErrorExpenses)) {
                $newCategory = str_replace("\r\n", " ", $_POST['new_expense_category']);
                if (!preg_match('/^[a-zA-Z0-9,:;()."\' -]{1,100}$/', $newCategory)) {
                    $newCategoryErrorExpenses = "<span style='color:red'>Your note does not satisfy the required format. You can use letters, numbers and any of these symbols: ,:;().\"' -</span><br>";
                }
            }
            if (empty($newCategoryErrorExpenses)) {
                $query = ("SELECT EXISTS (SELECT 1 FROM categories WHERE category = '$newCategory' and username = '$username' and income = FALSE) as category_exists;");
                $result = mysqli_query($link, $query);
                $row = mysqli_fetch_assoc($result);
                $categoryExists = $row['category_exists'];
                if ($categoryExists || $newCategory == "new") {
                    $newCategoryErrorExpenses = "<span style='color:red'>This category already exists.<br></span>";
                }
                else {
                    $link -> query("INSERT INTO categories (username, category, income) 
                                    VALUES ('$username', '$newCategory', FALSE)");
                }
            }
            $type = $newCategory;

        }
        //Writing to db
        if (empty($amountErrorExpenses) && empty($typeErrorExpenses) && empty($dateErrorExpenses) && empty($noteErrorExpenses) && empty($newCategoryErrorExpenses)) 
        {
            $link -> query("INSERT INTO spendings (username, spending_date, category, amount, spending_comment, recurring) 
                            VALUES ('$username', '$date', '$type', '$amount', '$note', FALSE);");
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
        $amount = number_format(floatval($amount), 2, ".", "");

        //TYPE
        $type = $_POST['income-type']; // Get type
        if (!in_array($type, $incomeCategories) && $type != "new") 
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
        if(!empty($_POST['income-note']))
        {   
            $note = str_replace("\r\n", " ", $_POST['income-note']);
            if (!preg_match('/^[a-zA-Z0-9,:;<>()."\' -]{1,100}$/', $_POST['income-note'])) {
                $noteErrorIncome = "<span style='color:red'>Your note does not satisfy the required format. You can use letters, numbers and any of these symbols: ,:;<>().\"' -</span><br>";
            }
        }
        //NEW CATEGORIES
        if ($type == "new" && empty($amountErrorIncome) && empty($typeErrorIncome) && empty($dateErrorIncome) && empty($noteErrorIncome)) {
            if (empty($_POST['new_income_category'])) {
                $newCategoryErrorIncome = "<span style='color:red'>Please enter the name for your new category or select an existing one.<br></span>";
            } 
            if (empty($newCategoryErrorIncome)) {
                $newCategory = str_replace("\r\n", " ", $_POST['new_income_category']);
                if (!preg_match('/^[a-zA-Z0-9,:;()."\' -]{1,100}$/', $newCategory)) {
                    $newCategoryErrorIncome = "<span style='color:red'>Your note does not satisfy the required format. You can use letters, numbers and any of these symbols: ,:;().\"' -</span><br>";
                }
            }
            if (empty($newCategoryErrorIncome)) {
                $query = ("SELECT EXISTS (SELECT 1 FROM categories WHERE category = '$newCategory' and username = '$username' and income = TRUE) as category_exists;");
                $result = mysqli_query($link, $query);
                $row = mysqli_fetch_assoc($result);
                $categoryExists = $row['category_exists'];
                if ($categoryExists || $newCategory == "new") {
                    $newCategoryErrorIncome = "<span style='color:red'>This category already exists.<br></span>";
                }
                else {
                    $link -> query("INSERT INTO categories (username, category, income) 
                                    VALUES ('$username', '$newCategory', TRUE)");
                }
            }
            $type = $newCategory;
        }
        //Writing to db.
        if (empty($amountErrorIncome) && empty($typeErrorIncome) && empty($dateErrorIncome) && empty($noteErrorIncome) && empty($newCategoryErrorIncome)) 
        {
            
            $link -> query("INSERT INTO incomes (username, income_date, category, amount, income_comment, recurring) 
                            VALUES ('$username', '$date', '$type', '$amount', '$note', FALSE);");
            $checkSumbissionIncome = TRUE;
        }
    }
}
//Graph logic.
//Labels (days on x-axis).
$daysInMonth = date("t");
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
           YEAR(spending_date) = YEAR(CURDATE()) AND
           MONTH(spending_date) = MONTH(CURDATE());");

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

//INCOMES
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'incomes') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no income table found.');
}    
$query = ("SELECT DAY(income_date), amount FROM incomes 
           WHERE username = '$username' AND 
           YEAR(income_date) = YEAR(CURDATE()) AND
           MONTH(income_date) = MONTH(CURDATE());");

$result = mysqli_query($link, $query);
if ($result->num_rows == 0) {

}
else {
    while($row = $result->fetch_assoc()) {
        $incomeDate = (int) $row['DAY(income_date)'];
        $incomeAmount = $row['amount'];
        $incomesByDays[$incomeDate] += floatval($incomeAmount);
    }
}






?>

<!DOCTYPE html>
<html lang="en">
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
      <div class="flex-zone" id="flex-zone-expenses">
        <div class="flex-container" id="expenses-input">
            <form method="POST">
                <h3>New spending</h3>
                <label for="expense-amount">Amount:</label>
                <input class="expense-amount-input" type="number" id="expense-amount" name="expense-amount" min="0" step="0.01">
                <label class="expense-type-label" for="expense-type">Category:</label>
                <select class="expense-type-select" id="expense-type" name="expense-type">
                    <?php
                    foreach ($spendingCategories as $category) {
                        echo '<option value="'.$category.'">';
                        echo $category;
                        echo "</option>";
                    }
                    ?>
                    <option value="new">New</option>
                </select> 
                <input type="hidden" class="expense-type-new" id="new_expense_category" name="new_expense_category">
                <script>
                    var selectExpenseElement = document.getElementById("expense-type");
                    var newExpenseCategory = document.getElementById("new_expense_category");

                    selectExpenseElement.addEventListener("change", function() {
                    if (this.value === "new") {
                        newExpenseCategory.type = "text";
                        newExpenseCategory.setAttribute("required", true);
                        newExpenseCategory.placeholder = "New category name";
                    } else {
                        newExpenseCategory.type = "hidden";
                        newExpenseCategory.removeAttribute("required");
                        newExpenseCategory.removeAttribute("placeholder");
                    }
                    });
                </script>
                <label class="expense-type-label" for="expense-date">Date:</label>    
                <input class="calender" type="date" id="expense-date" name="expense-date"><br><br>

                <label for="expense-note">Note:</label>    
                <input type="text" id="expense-note"  class="expense-amount-input" name="expense-note">
                <input class="btn" type="submit" id="submit-button-expense" name="submit-button-expense" value="Add spending">
                <?php
                if (!empty($amountErrorExpenses) || !empty($typeErrorExpenses) || !empty($dateErrorExpenses) || !empty($noteErrorExpenses) || !empty($newCategoryErrorExpenses)) {
                    echo expensesErrorsOutput($amountErrorExpenses, $typeErrorExpenses, $dateErrorExpenses, $noteErrorExpenses, $newCategoryErrorExpenses);
                } 
                elseif(isset($checkSumbissionExpenses))
                {
                    echo '<p>Expense was added successfully</p>';
                }
                ?>
            </form>
        </div>
        <div class="flex-container" id="expenses-table">
            <h4>Spendings</h4>
            <canvas id="line-chart-expenses"></canvas>
        </div>
      </div>
      <div class="flex-zone" id="flex-zone-income">
        <div class="flex-container" id="income-input">
            <form method="POST">
                <h3>New income</h3>
                <label for="income-amount">Amount:</label>
                <input class="income-amount-input" type="number" id="income-amount" name="income-amount" min="0" step="0.01">
                <label class="income-type-label" for="income-type">Category:</label>
                <select class="income-type-select" id="income-type" name="income-type" >
                    <?php
                        foreach ($incomeCategories as $category) {
                            echo '<option value="'.$category.'">';
                            echo $category;
                            echo "</option>";
                        }
                    ?>
                    <option value="new">New</option>
                </select> 
                <input type="hidden" class="income-type-new" id="new_income_category" name="new_income_category">
                <script>
                    var selectIncomeElement = document.getElementById("income-type");
                    var newIncomeCategory = document.getElementById("new_income_category");

                    selectIncomeElement.addEventListener("change", function() {
                    if (this.value === "new") {
                        newIncomeCategory.type = "text";
                        newIncomeCategory.setAttribute("required", true);
                        newIncomeCategory.placeholder = "New category name";
                    } else {
                        newIncomeCategory.type = "hidden";
                        newIncomeCategory.removeAttribute("required");
                        newIncomeCategory.removeAttribute("placeholder");
                    }
                    });
                </script>
                <label class="income-type-label" for="income-date">Date:</label>    
                <input class="calender" type="date" id="income-date" name="income-date"><br><br>
                <label for="income-note">Note:</label>    
                <input type="text" id="income-note"  class="income-amount-input" name="income-note">
                <input class="btn" type="submit" id="submit-button-income" name="submit-button-income" value="Add income">
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
        <div class="flex-container" id="income-table">
          <h4>Incomes</h4>
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
                        scales: {
                          x: {
                            grid: {
                              display: false
                            }
                          },
                          y: {
                            grid: {
                              display: false 
                            }
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
                        scales: {
                          x: {
                            grid: {
                              display: false
                            }
                          },
                          y: {
                            grid: {
                              display: false 
                            }
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