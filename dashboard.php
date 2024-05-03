<?php
// report all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
require ("includes/session_check.php");
require ("includes/sql_connect.php");
// JS FORMS
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

// EXPENSES JS
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
            header("Location: dashboard.php");
            exit;
        }
    }
}
// INCOME JS
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
            $checkSumbissionIncomes = TRUE;
            header("Location: dashboard.php");
            exit;
        }
    }
}

//---Recurring update---//
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'recurring') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no recurring table found.');
}
$query = ("SELECT * FROM recurring WHERE username = '$username' AND 
           YEAR(recurring_date) <= YEAR(CURDATE()) AND
           MONTH(recurring_date) <= MONTH(CURDATE()) AND
           DAY(recurring_date) <= DAY(CURDATE());");
while (mysqli_query($link, $query)->num_rows != 0) {
  $result = mysqli_query($link, $query);
  while($row = $result->fetch_assoc()) {
      $id = $row['ID'];
      $recurringDate = $row['recurring_date'];
      $cat = $row['category'];
      $name = $row['recurring_name'];
      $amount = number_format($row['amount'], 2, ".", ",");

      $link -> query("INSERT INTO spendings (username, spending_date, category, amount, spending_comment, recurring)
                      VALUES ('$username', '$recurringDate', '$cat', '$amount', '$name', TRUE)");

      if ($row['periodicity'] == "daily") {
        $link -> query("UPDATE recurring 
                        SET recurring_date = DATE_ADD(recurring_date, INTERVAL 1 DAY) 
                        WHERE ID = '$id';");
      }
      else if ($row['periodicity'] == "weekly") {
        $link -> query("UPDATE recurring 
                        SET recurring_date = DATE_ADD(recurring_date, INTERVAL 1 WEEK) 
                        WHERE ID = '$id';");
      }
      else if ($row['periodicity'] == "2weekly") {
        $link -> query("UPDATE recurring 
                        SET recurring_date = DATE_ADD(recurring_date, INTERVAL 2 WEEK) 
                        WHERE ID = '$id';");
      }
      else if ($row['periodicity'] == "monthly") {
        $link -> query("UPDATE recurring 
                        SET recurring_date = DATE_ADD(recurring_date, INTERVAL 1 MONTH) 
                        WHERE ID = '$id';");
      }
      else if ($row['periodicity'] == "annualy") {
        $link -> query("UPDATE recurring 
                        SET recurring_date = DATE_ADD(recurring_date, INTERVAL 1 YEAR) 
                        WHERE ID = '$id';");
      }
  }
}

//- - - - -Graph logic.- - - - -
$daysInMonth = date("t");
$currentMonth = date("F");
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
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'incomes') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no income table found.');
}    
$totalIncomes = 0.0;
$query = ("SELECT amount FROM incomes 
           WHERE username = '$username' AND 
           YEAR(income_date) = YEAR(CURDATE()) AND
           MONTH(income_date) = MONTH(CURDATE());");

$result = mysqli_query($link, $query);
if ($result->num_rows == 0) {

}
else {
    while($row = $result->fetch_assoc()) {
        $incomeAmount = $row['amount'];
        $totalIncomes += floatval($incomeAmount);
    }
}

//- - - - -BALANCE LOGIC.- - - - -

$totalSpendings = 0.00;
foreach ($spendingsByDays as $spending) {
  $totalSpendings += floatval($spending);
}

$balance = $totalIncomes - $totalSpendings;
//- - - - -LATEST EXPENSES TABLE.- - - - -
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'spendings') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no spending table found.');
}   
$lastExpenses = [];
$query = ("SELECT spending_date, amount, category FROM spendings WHERE username = '$username'
          ORDER BY spending_date DESC LIMIT 5;");
$result = mysqli_query($link, $query);
if ($result->num_rows == 0) {
    $noRows = TRUE;
}
else {
    while($row = $result->fetch_assoc()) {
        $spendingDate = date('d.m', strtotime($row['spending_date']));
        $spendingCategory = $row['category'];
        $spendingAmount = $row['amount'];
        
        $expense = [$spendingDate, $spendingAmount, $spendingCategory];
        array_push($lastExpenses, $expense);
    }
} 

// Upcoming payments table
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'recurring') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no recurring table found.');
}   
$recurringTable = [];
$query = ("SELECT recurring_date, amount, recurring_name FROM recurring WHERE username = '$username'
          ORDER BY recurring_date ASC LIMIT 5;");
$result = mysqli_query($link, $query);
if ($result->num_rows == 0) {
    $noRows = TRUE;
}
else {
    while($row = $result->fetch_assoc()) {
        $recurringDate = date('d.m', strtotime($row['recurring_date']));
        $recurringName = $row['recurring_name'];
        $recurringAmount = $row['amount'];
        
        $recurring = [$recurringDate, $recurringAmount, $recurringName];
        array_push($recurringTable, $recurring);
    }
} 


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
 
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles/dashboard.css">
  <link rel="icon" href="img/icon.PNG">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="authors" content="kisevt, ikuzne">
  <title>Dashboard•FinCheck</title>
</head>

<body>
  <?php include 'includes/header.php'; ?>
  <?php include 'includes/sidebar.php'; ?>
  
  <!--SPENDINGS FORM JS-->
  <<div id="modal-expenses" class="modal">
  <div class="modal-content">
    <span class="close close-expenses">&times;</span>
    <form method="POST" action="" id="expenses-form">
                <h3>Expenses</h3>
                <label for="expense-amount">Amount:</label><br>
                <input class="expense-amount-input" type="number" id="expense-amount" name="expense-amount" min="0" step="0.01" pattern="\d+(\.\d{2})?"><br>
                <label class="expense-type-label" for="expense-type">Select Expense Type:</label> <br>
                <select class="expense-type-select" id="expense-type" name="expense-type"> <br>
                    <?php
                    foreach ($spendingCategories as $category) {
                        echo '<option value="'.$category.'">';
                        echo $category;
                        echo "</option>";
                    }
                    ?>
                    <option value="new">New</option><br>
                </select><br>
                <input type="hidden" class="expense-type-new" id="new_expense_category" name="new_expense_category" placeholder="New category name">
                <script>
                    var selectExpenseElement = document.getElementById("expense-type");
                    var newExpenseCategory = document.getElementById("new_expense_category");

                    selectExpenseElement.addEventListener("change", function() {
                    if (this.value === "new") {
                        newExpenseCategory.type = "text";
                        newExpenseCategory.setAttribute("required", true);
                    } else {
                        newExpenseCategory.type = "hidden";
                        newExpenseCategory.removeAttribute("required");
                    }
                    });
                </script>
                <label class="expense-type-label" for="expense-date">Date:</label><br><br>
                <input class="calender" type="date" id="expense-date" name="expense-date"><br><br>

                <label for="expense-note">Note:</label> <br>
                <input type="text" id="expense-note"  class="expense-amount-input" name="expense-note"><br>
                <input type="hidden" id="recurring" value="No">

                <input class="btn" type="submit" id="submit-button-expense" name="submit-button-expense" value="Submit Expense">
            
            </form>
  </div>
</div>

<!--INCOMES JS FORM-->
<div id="modal-incomes" class="modal">
  <div class="modal-content">
    <span class="close close-incomes">&times;</span>
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
                    <option value="new">New</option>
                </select> 
                <input type="hidden" class="income-type-new" id="new_income_category" name="new_income_category" placeholder="New category name">
                <script>
                    var selectIncomeElement = document.getElementById("income-type");
                    var newIncomeCategory = document.getElementById("new_income_category");

                    selectIncomeElement.addEventListener("change", function() {
                    if (this.value === "new") {
                        newIncomeCategory.type = "text";
                        newIncomeCategory.setAttribute("required", true);
                    } else {
                        newIncomeCategory.type = "hidden";
                        newIncomeCategory.removeAttribute("required");
                    }
                    });
                </script>
                <label class="income-type-label" for="income-date">Date:</label>    
                <input class="calender" type="date" id="income-date" name="income-date"><br><br>
                <label for="income-note">Note:</label>    
                <input type="text" id="income-note"  class="income-amount-input" name="income-note">
                <input type="hidden" id="recurring" value="No">
                <input class="btn" type="submit" id="submit-button-income" name="submit-button-income" value="Submit Income">
                
      </form>
  </div>
</div>




  <div class="content">
    <div class="flex-zone flex-zone-left">
      <div class="flex-container flex-container-left">
        <!--Hide Balance-->
        <div class="balance-div">
          <h2>Balance</h2>
          <h1 id="balance"><?php echo number_format(floatval($balance), 2); ?></h1>
        </div>
        <div class="balance-div">
          <h2><a title="New spending" id="new-spending-btn">Spent<i class='bx bx-minus'></i></a></h2>
          <h1 id="total-spendings"><?php echo number_format(floatval($totalSpendings), 2); ?></h1>
        </div>
<script>
// Expenses Modal Script
document.addEventListener('DOMContentLoaded', function () {
  var modalExpenses = document.getElementById("modal-expenses");
  var newSpendingBtn = document.getElementById("new-spending-btn");
  var closeExpenses = document.querySelector(".close-expenses");
  var expensesForm = document.getElementById("expenses-form");

  var messageDiv = document.getElementById("messageJsDiv");

  newSpendingBtn.onclick = function() {
    modalExpenses.style.display = "block";
    modalExpenses.style.position = "absolute";
  };

  closeExpenses.onclick = function() {
    modalExpenses.style.display = "none";
    messageDiv.style.display = 'block'; 
  };

});
</script>

        <div class="balance-div">
          <h2><a title="New income" id="new-income-btn">Earned<i class='bx bx-plus'></i></a></h2>
          <h1 id="total-incomes"><?php echo number_format(floatval($totalIncomes), 2); ?></h1>
        </div>
      </div>
      <?php
        if (!empty($amountErrorExpenses) || !empty($typeErrorExpenses) || !empty($dateErrorExpenses)) {
          echo "<div class='messageJsDiv' id='messageJsDiv'>";
          echo expensesErrorsOutput($amountErrorExpenses, $typeErrorExpenses, $dateErrorExpenses);
          echo "</div>";
        } 
        ?><script>
            setTimeout(function() {
                var element = document.getElementById('messageJsDiv');
                if (element) {
                    element.parentNode.removeChild(element);
                }
            }, 4000); // 4 sec
        </script><?php
        if(isset($checkSumbissionExpenses))
        {
          echo "<div class='messageJsDiv' id='messageJsDiv'>";
          echo '<p>Expense was added successfully</p>';
          echo "</div>";
        }
        ?><script>
            setTimeout(function() {
                var element = document.getElementById('messageJsDiv');
                if (element) {
                    element.parentNode.removeChild(element);
                }
            }, 4000); // 4 sec
        </script><?php
        if (!empty($amountErrorIncome) || !empty($typeErrorIncome) || !empty($dateErrorIncome)) {
          echo "<div class='messageJsDiv' id='messageJsDiv'>";
          echo expensesErrorsOutput($amountErrorIncome, $typeErrorIncome, $dateErrorIncome);
          echo "</div>";
        } 
        ?><script>
            setTimeout(function() {
                var element = document.getElementById('messageJsDiv');
                if (element) {
                    element.parentNode.removeChild(element);
                }
            }, 4000); // 4 sec
        </script><?php
        if(isset($checkSumbissionIncome))
        {
          echo "<div class='messageJsDiv' id='messageJsDiv'>";
          echo '<p>Income was added successfully</p>';
          echo "</div>";
        }
        ?><script>
            setTimeout(function() {
                var element = document.getElementById('messageJsDiv');
                if (element) {
                    element.parentNode.removeChild(element);
                }
            }, 4000); // 4 sec
        </script>
<script>
// Incomes Modal Script
document.addEventListener('DOMContentLoaded', function () {
  var modalIncomes = document.getElementById("modal-incomes");
  var newIncomeBtn = document.getElementById("new-income-btn");
  var closeIncomes = document.querySelector(".close-incomes");
  var incomesForm = document.getElementById("incomes-form");

  var messageDiv = document.getElementById("messageJsDiv");


  newIncomeBtn.onclick = function() {
    modalIncomes.style.display = "block";
    modalIncomes.style.position = "absolute";
  };

  closeIncomes.onclick = function() {
    modalIncomes.style.display = "none";
    messageDiv.style.display = 'block';
  };

});
</script>
      <div class="simple">
        <div class="month-navigation">
      
          <script>
            var numberClicks = 0; // Declare numberClicks outside any function
            var jsonGraphData;
            function getData(monthOffset) {
              fetch('process_graph.php?monthOffset=' + monthOffset)
                .then(response => response.json())
                .then(data => {
                  updateChart(data.spendings);
                  changeData(data.month, data.balance, data.totalIncomes, data.totalSpendings);
                });
            };

            function changeData(month, newBalance, newIncomes, newSpendings) {
              var graphTitle = document.getElementById("graph-title");
              var balance = document.getElementById("balance");
              var incomes = document.getElementById("total-incomes");
              var spendings = document.getElementById("total-spendings");
              graphTitle.textContent = month + " spendings";
              balance.textContent = newBalance;
              incomes.textContent = newIncomes;
              spendings.textContent = newSpendings;

            };

            function increaseClicks() {
              numberClicks += 1;
              getData(numberClicks);
              // Show the button if numberClicks is less than 0
              if (numberClicks < 0) {
                document.getElementById("next-month-button").style.display = "block";
              } else {
                document.getElementById("next-month-button").style.display = "none";
              }
            };
          
            function decreaseClicks() {
              numberClicks -= 1;
              getData(numberClicks);
              // Show the button if numberClicks is less than 0
              if (numberClicks < 0) {
                document.getElementById("next-month-button").style.display = "block";
              } else {
                document.getElementById("next-month-button").style.display = "none";
              }
            };

            function updateChart(data) {
              lineGraph.data = {
                  datasets: [
                    {
                      data: data,
                      label: "Spendings",
                      borderColor: "#0072ce",
                      backgroundColor: "#0072ce",
                    }]
                };
              lineGraph.update();
            };

            
          </script>
          <a class="month-button" id="previous-month-button" onclick="decreaseClicks()">&#60;Previous month</a>
          <a class="month-button" id="next-month-button" onclick="increaseClicks()" style="display:none">Next month&#62;</a>
      </div>
        <span class="header2" id="graph-title"><a href="advanced.php" title="See more"></a><?php echo $currentMonth ?> spendings</span>
        </label>
        <canvas id="line-chart"></canvas>
        <!--Hide recurring payments-->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>
        <script>
          
          
          var lineGraph = new Chart(document.getElementById("line-chart"), {
              type: 'line',
              data: {
                datasets: [
                  {
                    data: <?php echo json_encode($spendingsByDays); ?>,
                    label: "Spendings",
                    borderColor: "#0072ce",
                    backgroundColor: "#0072ce",
                  }]
              },
              options: {
                plugins: {
                  legend: {
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
                aspectRatio: 1.65,
                title: {
                  display: true,
                  text: 'line graph'
                }
              }
            });
        </script>
        </div>
      </div>
      <div class="flex-zone flex-zone-right">
        <div class="flex-container flex-container-right" id="latestSpendings">
          <h2><a href="advanced.php">Latest spendings</a></h2>
          <?php if (isset($noSpendingRows)) {?>
          <span>No spendings has been made yet.</span>
          <script>
            var FlexContainerRight = document.getElementById("latestSpendings");
            FlexContainerRight.style.justifyContent = "center";
          </script>
          <?php } else {?>
          <table class="right-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Category</th>
              </tr>
            </thead>
            <tbody>
              <?php

              if (isset($lastExpenses)) {
                foreach($lastExpenses as $row) {
                  echo "<tr>";
                  foreach($row as $cell) {
                    echo "<td>";
                    echo $cell;
                    echo "</td>";
                  }
                  echo "</tr>";
                }
              }

            ?>
          </tbody>
        </table>
        <?php } ?>
      </div>
      <div class="flex-container flex-container-right" id="upcomingPayments">
        <h2><a href="recurring.php">Upcoming payments</a></h2>
        <?php if (isset($noRecurringRows)) {?>
        <span>No recurring payments has been added yet.</span>
        <script>
          var FlexContainerRight = document.getElementById("upcomingPayments");
          FlexContainerRight.style.justifyContent = "center";
        </script>
        <?php } else {?>
        <table class="right-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Amount</th>
              <th>Type</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (isset($recurringTable)) {
              foreach($recurringTable as $row) {
                echo "<tr>";
                foreach($row as $cell) {
                  echo "<td>";
                  echo $cell;
                  echo "</td>";
                }
                echo "</tr>";
              }
            }

            ?>
          </tbody>
        </table>
        <?php } ?>
      </div>
    </div>
  </div>
  <footer>
    <span>Copyright © 2024 FinCheck OÜ. All rights reserved.</span>
  </footer>
</body>

</html>