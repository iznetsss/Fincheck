<?php
require ("includes/session_check.php");
require ("includes/sql_connect.php");

//Getting the categories of this user.
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
?>