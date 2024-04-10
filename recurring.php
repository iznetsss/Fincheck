<?php
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
  


  if(isset($_POST['bill-name']) && isset($_POST['day']) && isset($_POST['bill-actual']) && isset($_POST['repeat']))
  {
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
    $amount = number_format(floatval($amount), 2);

    //REPEATS
    $repeat = $_POST['repeat'];
    $allowedTypes = array('monthly', 'annualy', 'weekly', '2weekly', 'daily');

    if (!in_array($repeat, $allowedTypes)) 
    {
        $repeatError = "<span style='color:red'>Invalid payment repeating type</span><br>";
    }

    //If life is good - we move forward
    if (empty($dateError) && empty($amountError) && empty($repeatError)) 
    {
      $recurringData = array();
      $recurringData['name'] = $name;
      $recurringData['day'] = $date;
      $recurringData['amount'] = $amount;
      $recurringData['repeating'] = $repeat;
            
        if (!file_exists('data/recurring.csv')) {
            touch('data/recurring.csv');
            chmod('data/recurring.csv', 0777); 
        }

      $csv_file = fopen('data/recurring.csv', 'a');
      fputcsv($csv_file, $recurringData, ';');
      fclose($csv_file);

      $checkSumbission = TRUE;
    }
  }
}
/*
$file = fopen("data/recurring.csv", "r");
$arrRecurring = [];
while (($data = fgetcsv($file, 1000, ";")) !== false) {
  array_push($arrRecurring, $data);
fclose($file);
foreach ($arrRecurring as $payment) {
  if (strtotime($payment[1]) <= strtotime('today')) {
    $arrPayment = [
      ['Date'] = 
    ]
    $paymentDate = $payment[1];
    $pamentName = $payment[0];
    $paymentAmount = $payment[2];
    $paymentRegular = $payment[3];
    
  }
}
    if ($data[3] == "monthly") {
        $date = $data[1];
        $name = $data[0];
        $amount = $data[2];

        $expenseDate = new DateTime($date);
        $today = new DateTime();

        if ($expenseDate < $today) {
            // Add to file if its less than today
            $expensesFile = fopen("data/expenses.csv", "a");
            fputcsv($expensesFile, array('', $date, $name, $amount, '', 'Yes'), ';');
            fclose($expensesFile);
        }
    }
}
*/



//Recurring table.
$file = fopen("data/recurring.csv", "r");
$recurringTable = array();

while (($data = fgetcsv($file, 1000, ";")) !== false) {
    // Check if the row has at least four elements (name, day, amount, repeating)
    if (count($data) >= 4) {
        $name = $data[0];
        $day = date('d', strtotime($data[1])); // Extract day from date
        $amount = $data[2];
        
        // Add formatted data to recurring table
        $recurringTable[] = array($name, $day, $amount);
    }

}
fclose($file);

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
        <input class="btn" type="submit" id="submit-button-recurring" name="submit-button-recurring" value="Sumbit">

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
    
    <table class="bills-table">
      <span class="bills-table-name header2">Recurring payments</span>
      <thead>
          <tr>
            <th>Name</th>
            <th>Due</th>
            <th>Actual</th>
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
    </div>

  </div>
</body>

</html>