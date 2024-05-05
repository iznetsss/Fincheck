<?php
require ("includes/session_check.php");
require ("includes/sql_connect.php");
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-button-recurring'])) 
{

  $dateError = $amountError = $repeatError = '';
  function errorsOutput($amountError, $typeError, $dateError) {
      $output = '<br>';
      if(isset($amountError)) {
          $output .= $amountError;
      }
      if(isset($typeError)) {
          $output .= $typeError;
      }
      if(isset($dateError)) {
          $output .= $dateError;
      }
      return $output;
  }
  


  if(isset($_POST['bill-name']) && isset($_POST['day']) && isset($_POST['bill-actual']) && isset($_POST['repeat']) && isset($_POST['expense-type'])) 
  {
    //CATEGORY 
    $type = $_POST['expense-type']; // Get type
    if (!in_array($type, $spendingCategories)) 
    {
        $typeErrorExpenses = "<span style='color:red'>Invalid expense type.<br></span>";
    }

    //NAME INPUT
    if(isset($_POST['bill-name'])) 
    {
      $maxNameLength = 30; //30 charachters is limit
      $name = trim($_POST['bill-name']);
      $name = str_replace("\r\n", " ", $name); //Delete enters
      $name = substr($name, 0, $maxNameLength);
    }
  

    //DATE
    $date = $_POST['day'];
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);

    if ($dateObj && $dateObj->format('Y-m-d') === $date) 
    {
        // Valid date
        $year = $dateObj->format('Y');
        $month = $dateObj->format('m');
        $day = $dateObj->format('d');
    } else {
        // Invalid date
        $dateError = "<span style='color:red'>Incorrect date format.</span><br>";
    }

    //AMOUNT
    $amount = $_POST['bill-actual']; // Get amount
    if(isset($amount) && preg_match("/^\d+(\.\d{2})?$/", $amount)) 
    {
        if(!($amount >= 0.01 && $amount <= 10000000)) // Amount must be between 0.01 and 10000000
        {
            $amountError = "<span style='color:red'>Amount must be between 0.01 and 10000000</span><br>"; // Amount error message
        }
    } else {
        $amountError = "<span style='color:red'>Wrong number input</span><br>"; // Amount error message
    }
    //Converting $amount to float, rounding, and converting back to string
    //$amount = number_format(floatval($amount), 2);

    //REPEATS
    $repeat = $_POST['repeat'];
    $allowedTypes = array('monthly', 'annualy', 'weekly', '2weekly', 'daily');

    if (!in_array($repeat, $allowedTypes)) 
    {
        $repeatError = "<span style='color:red'>Invalid payment repeating type</span><br>";
    }

    //NEW CATEGORIES
    if ($type == "new" && empty($amountError) && empty($typeError) && empty($dateError) && empty($noteErrorIncome)) 
    {
      if (empty($_POST['new_recurring_category'])) {
        $newCategoryError = "<span style='color:red'>Please enter the name for your new category or select an existing one.<br></span>";
      } 
      if (empty($newCategoryError)) {
        $newCategory = str_replace("\r\n", " ", $_POST['new_recurring_category']);
        if (!preg_match('/^[a-zA-Z0-9,:;()."\' -]{1,100}$/', $newCategory)) {
            $newCategoryError = "<span style='color:red'>Your note does not satisfy the required format. You can use letters, numbers and any of these symbols: ,:;().\"' -</span><br>";
        }
      
      if (empty($newCategoryError)) {
        $query = ("SELECT EXISTS (SELECT 1 FROM categories WHERE category = '$newCategory' and username = '$username' and income = TRUE) as category_exists;");
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_assoc($result);
        $categoryExists = $row['category_exists'];
        if ($categoryExists || $newCategory == "new") {
            $newCategoryError = "<span style='color:red'>This category already exists.<br></span>";
        }
        else {
            $link -> query("INSERT INTO categories (username, category, income) 
                            VALUES ('$username', '$newCategory', false)");
        }
      }
      $type = $newCategory;
    }
  }

    //If life is good - we move forward
    if (empty($dateError) && empty($amountError) && empty($repeatError)) 
    {
        
      $query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'recurring') as table_exists;");
      $result = mysqli_query($link, $query);
      $row = mysqli_fetch_assoc($result);
      $tableExists = $row['table_exists'];
      if (!$tableExists) {
          die('Database error: no recurring table found.');
      }  
      $link -> query("INSERT INTO recurring (username, recurring_date, category, recurring_name, amount, periodicity)
                      VALUES ('$username', '$date', '$type', '$name', '$amount', '$repeat')");         
      $checkSumbission = TRUE;
    }

  
}
}


//Recurring table.
$query = ("SELECT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'recurring') as table_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$tableExists = $row['table_exists'];
if (!$tableExists) {
    die('Database error: no recurring table found.');
}   
$recurringTable = [];
$query = ("SELECT recurring_date, amount, recurring_name, periodicity FROM recurring WHERE username = '$username'
          ORDER BY recurring_date ASC;");
$result = mysqli_query($link, $query);
if ($result->num_rows == 0) {
    $noRecurringRows = TRUE;
}
else {
    while($row = $result->fetch_assoc()) {
        $recurringDate = date('d.m.Y', strtotime($row['recurring_date']));
        $recurringName = $row['recurring_name'];
        $recurringAmount = number_format($row['amount'], 2, ".", ",");
        if ($row['periodicity'] == "annualy") {
          $periodicity = "Every year";
        }
        else if ($row['periodicity'] == "monthly") {
          $periodicity = "Every month";
        }
        else if ($row['periodicity'] == "2weekly") {
          $periodicity = "Every 2 weeks";
        }
        else if ($row['periodicity'] == "weekly") {
          $periodicity = "Every week";
        }
        else if ($row['periodicity'] == "daily") {
          $periodicity = "Every day";
        }
        
        $recurring = [$recurringName, $recurringDate, $recurringAmount, $periodicity];
        array_push($recurringTable, $recurring);
    }
}

?>

<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" href="styles/recurring.css">
  <link rel="icon" href="img/icon.PNG">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <meta charset="utf-8">
  <meta name="authors" content="kisevt, ikuzne">
  <title>Recurring•FinCheck</title>
</head>

<body>
  <?php include 'includes/header.php'; ?>
  <?php include 'includes/sidebar.php'; ?>

  <div class="content">
    <div class="flex-container" id="flex-input-bill">
      <form method="POST" action="">
        <span class="header2">New recurring payment</span><br><br>
        <label for="expense-type">Category:</label>
        <select class="expense-type-select" id="expense-type" name="expense-type">
            <?php
            foreach ($spendingCategories as $category) {
                echo '<option value="'.$category.'">';
                echo $category;
                echo "</option>";
            }
            ?>
          <option value="new">New</option>
          <input type="hidden" class="expense-type-new" id="new_recurring_category" name="new_recurring_category" placeholder="New category name">
        </select> 
        <label for="bill-name">Name:</label>
        <input class="bill-name-input" type="text" id="bill-name" name="bill-name" maxlength="30">
        <label for="bill-due">Due:</label>
        <input type="date" class="bill-due-select" id="day" name="day">
        <label for="bill-actual">Amount:</label>
        <input class="bill-actual-input" type="number" id="bill-actual" name="bill-actual" min="0" step="0.01" pattern="\d+(\.\d{2})?">
        <label for="bill-due">Repeat:</label>
        <select class="bill-due-select" id="repeat" name="repeat">
          <option value="monthly">Every Month</option>
          <option value="annualy">Every Year</option>
          <option value="weekly">Every Week</option>
          <option value="2weekly">Every 2 Weeks</option>
          <option value="daily">Every Day</option>
        </select>
        <script>
            var selectExpenseElement = document.getElementById("expense-type");
            var newExpenseCategory = document.getElementById("new_recurring_category");
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
        <input class="btn" type="submit" id="submit-button-recurring" name="submit-button-recurring" value="Add payment">

        <?php
        //Error expense message
        if (!empty($dateError) || !empty($amountError) || !empty($repeatError)) {
            echo errorsOutput($dateError, $amountError, $repeatError);
        } 
        //Recurring was added
        elseif(isset($checkSumbission))
        {
            echo '<p>Recurring payments was added successfully</p>';
        }
        ?>

      </form>
    </div>
    <div class="flex-container" id="flex-table-bills">
      <span class="bills-table-name header2">Recurring payments</span>
      <?php if (isset($noRecurringRows)) {?>
        <span>No recurring payments has been added yet.</span>
        <script>
          var FlexContainer = document.getElementById("flex-table-bills");
          FlexContainer.style.justifyContent = "center";
        </script>
      <?php } else {?>
        <table class="bills-table">
          <thead>
              <tr>
                <th>Name</th>
                <th>Due</th>
                <th>Amount</th>
                <th>Repeat</th>
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
</body>

</html>