<?php
require ("includes/session_check.php");
require ("includes/sql_connect.php");

$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'spendings') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no spending table found.');
}  
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'incomes') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no income table found.');
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


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles/advanced.css">
  <link rel="icon" href="img/icon.PNG">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="authors" content="kisevt, ikuzne">
  <title>Advanced•FinCheck</title>
</head>

<body>
  <?php require 'includes/header.php'; ?>
  <?php require 'includes/sidebar.php'; ?>



<!--SPENDINGS FORM JS-->
    <div id="modal-expenses" class="modal">
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



  <div class="content" id="content">
    <div class="flex-zone">
      <a id="week">Week</a>
      <a id="month">Month</a>
      <a id="year">Year</a>
      <a id="all">All</a>
      <form id="filter-by-date">
        <input type="date" id="date-input-from" required>
        <input type="date" id="date-input-to" required>
        <input type="submit" value="Filter">
      </form>
      <script>

        var week = document.getElementById("week");
        var month = document.getElementById("month");
        var year = document.getElementById("year");
        var all = document.getElementById("all");
        var filter = document.getElementById("filter-by-date")
        filter.addEventListener("submit", function() {
          event.preventDefault();
          var dateInputFrom = document.getElementById("date-input-from");
          var dateFrom = dateInputFrom.value;
          var dateInputTo = document.getElementById("date-input-to");
          var dateTo = dateInputTo.value;
          if (dateFrom != "" && dateTo != "") {
            fetch('process_table.php?period=custom&from=' + dateFrom + "&to=" + dateTo)
              .then(response => response.json())
              .then(data => {
                loadTable(data);
            });
          }
        });
        week.addEventListener("click", function() {
          fetchData("week");
        });
        month.addEventListener("click", function() {
          fetchData("month");
        });
        year.addEventListener("click", function() {
          fetchData("year");
        });
        all.addEventListener("click", function() {
          fetchData("all");
        });
      </script>
    </div>
    <?php if (isset($noRows)) {?>
      <span>No spendings has been made yet.</span>
      <script>
        var content = document.getElementById("content");
          content.style.alignItems = "center";
      </script>
    <?php } else {?>
    <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Note</th>
            <th>Recurring</th>
          </tr>
        </thead>
        <tbody id="table">
        </tbody>
    </table>
    <script>
      function fetchData(period) {
        fetch('process_table.php?period=' + period)
          .then(response => response.json())
          .then(data => {
            loadTable(data);
        });
      }

      function loadTable(jsonTableData) {
        var table = document.getElementById("table");
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }
        jsonTableData.forEach(function(row) {
          
          var tableSize = table.rows.length;
          var newRow = table.insertRow(tableSize);
          newRow.id = row.id + row.isSpending;

          var cellDate = newRow.insertCell(0);
          var cellCategory = newRow.insertCell(1);
          var cellAmount = newRow.insertCell(2);
          if (row.isSpending == 1) {
            cellAmount.classList.add('spending-cell');
          }
          else {
            cellAmount.classList.add('income-cell');
          }
          var cellNote = newRow.insertCell(3);
          var cellRecurring = newRow.insertCell(4);

          cellDate.innerHTML = row.date;
          cellCategory.innerHTML = row.category;
          if (row.isSpending == 1) {
            cellAmount.innerHTML = "-" + row.amount;
          }
          else {
            cellAmount.innerHTML = "+" + row.amount;
          }
          cellNote.innerHTML = row.comment;
          cellRecurring.innerHTML = row.recurring;
        }); 
      }
    fetchData("all");
    table.addEventListener("click", function(event) {
      var clickedElement = event.target;

      if (clickedElement.tagName === "TD") {
        var clickedRow = clickedElement.parentNode;
        var rowId = clickedRow.id;




        if(rowId[rowId.length - 1] == 1)
        {
          // Expenses Modal Script
          var modalExpenses = document.getElementById("modal-expenses");
          var closeExpenses = document.querySelector(".close-expenses");
          modalExpenses.style.display = "block";
          modalExpenses.style.position = "absolute";
        
          closeExpenses.onclick = function() {
            modalExpenses.style.display = "none";
          };
          

        } 
        else if(rowId[rowId.length - 1] == 0) 
        {
          //Incomes Modal Script
          var modalIncomes = document.getElementById("modal-incomes");
          var closeIncomes = document.querySelector(".close-incomes");
        
          modalIncomes.style.display = "block";
          modalIncomes.style.position = "absolute";
        
          closeIncomes.onclick = function() {
            modalIncomes.style.display = "none";
          };
        
        }

      }
    });
    </script>
    <?php } ?>
  </div>

  <footer>
    <span>Copyright © 2024 FinCheck OÜ. All rights reserved.</span>
  </footer>
</body>

</html>