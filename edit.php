<?php
$valueId = isset($_GET['id']) ? $_GET['id'] : null;

//FUNCTIONS>
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
//EXPENSES.
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
            $expenseData['count'] = $valueId;
            $expenseData['date'] = $date;
            $expenseData['type'] = $type;
            $expenseData['amount'] = $amount;
            $expenseData['note'] = $note; 
            $expenseData['recurring'] = "No"; //Recurring is always "No"
            

            //Creating array of users.
            if (file_exists("data/expenses.csv")) {
                $users = [];
                $file = fopen("data/users.csv", "r");
                $found = FALSE;
                while (($user = fgetcsv($file, 1000, ";")) !== FALSE) {
                    if ($user[0] != $_POST['email']) {
                        array_push($users, $user);
                    }
                    else if ($user[0] == $_POST['email']) {
                        if (password_verify($_POST['password'], $user[1])) {
                            $found = TRUE;
                        }
                        else {
                            $found = TRUE;
                            $error = TRUE;
                            unset($users); //Deleting the arrays of users
                            $incorrectPasswordError = "Incorrect password.";
                            array_push($arrErrors, $incorrectPasswordError);

                            break;
                        }
                    }
                }
            
            
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
//CATEGORIES.
if (file_exists("data/spending_categories.csv")) {
    $file = fopen("data/spending_categories.csv", "r");
    $spendingCategories = fgetcsv($file, 1000, ";");
    fclose($file);  
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles/edit.css">
  <link rel="icon" href="img/icon.PNG">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="authors" content="kisevt, ikuzne">
  <title>Advanced•FinCheck</title>
</head>

<body>
  <?php include 'includes/header.php'; ?>
  <?php include 'includes/sidebar.php'; ?>
  <div class="content">
    <div class="flex-container">
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
  </div>
</body>