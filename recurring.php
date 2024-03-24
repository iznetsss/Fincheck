<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-button-recurring'])) 
{

  $amountError = $typeError = $dateError = '';

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
  


  if(isset($_POST['bill-amount']) && isset($_POST['day']) && isset($_POST['bill-actual']) && isset($_POST['repeat']))
  {
    //NAME INPUT
    if(isset($_POST['expense-note'])) 
    {
      $maxNameLength = 30; //30 charachters is limit
      $name = trim($_POST['expense-note']);
      $name = str_replace("\r\n", " ", $name); //Delete enters
      $name = substr($note, 0, $maxNameLength);
    }
  

    //DATE
    $date = $_POST['day'];
    if (preg_match("/^\d+(\.\d{2})?$/", $date)) // Date regex
    {
        if (strtotime($date) === false) 
        {
            $dateError = "<span style='color:red'>Wrong date input.</span><br>";
        } else 
        {
            list($year, $month, $day) = explode('-', $date);
        } 
    } 
    else 
    {
        $dateError = "<span style='color:red'>Incorrect date format.</span><br>";
    }

    //AMOUNT
    $amount = $_POST['bill-actual']; // Get amount
    if(isset($amount) && preg_match("/^\d+(\.\d{2})?$/", $amount)) 
    {
        if(!($amount >= 0.01 && $amount <= 10000000)) // Amount must be between 0.01 and 10000000
        {
            $amountError = "<span class='error'>Amount must be between 0.01 and 10000000</span><br>"; // Amount error message
        }
    }
    elseif(empty($amount))
    {
        $amountError = "<span class='error'>Wrong number input</span><br>"; // Amount error message
    }
    //Converting $amount to float, rounding, and converting back to string
    $amount = number_format(floatval($amount), 2);

    //REPEATS
    $repeat = $_POST['repeat'];
    $allowedTypes = array('monthly', 'annualy', 'weekly', '2weekly', 'daily');

    if (!in_array($type, $allowedTypes)) 
    {
        $repeatError = "<span class='error'>Invalid income type</span><br>";
    }

    //If life is good - we move forward
    if (empty($dateError) && empty($amountError) && empty($repeatError)) 
    {
      $recurringData = array();
      $recurringData['name'] = $name;
      $recurringData['day'] = $date;
      $recurringData['amount'] = $amount;
      $recurringData['repeating'] = $repeat;
    }
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
        <label for="bill-name">Name:</label>
        <input class="bill-name-input" type="text" id="bill-amount" name="bill-amount" maxlength="30">
        <label for="bill-due">Due:</label>
        <input type="date" class="bill-due-select" id="day">
        <label for="bill-actual">Amount:</label>
        <input class="bill-actual-input" type="number" id="bill-actual" name="bill-actual" min="0" step="0.01" pattern="\d+(\.\d{2})?">
        <label for="bill-due">Repeat:</label>
        <select class="bill-due-select" id="repeat">
          <option value="monthly">Every Month</option>
          <option value="annualy">Every Year</option>
          <option value="weekly">Every Week</option>
          <option value="2weekly">Every 2 Weeks</option>
          <option value="daily">Every Day</option>
        </select>
        <input class="btn" type="submit" id="submit-button-recurring" name="submit-button-recurring" value="Sumbit">

        <?php
        //Error expense message
        if(isset($dateError, $amountError, $repeatError))
        {
            echo expensesErrorsOutput($dateError, $amountError, $repeatError); 
        }
        //Data was added message
        if (!empty($dateError) || !empty($amountError) || !empty($repeatError)) {
            echo outputErrors($dateError, $amountError, $repeatError);
        } 
        elseif(isset($checkSumbission))
        {
            echo '<p>RECURRING was added successfully</p>';
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
            <th>Budget</th>
          </tr>
        </thead>
        <tbody>
          <!--Sort by due-->
          <tr>
            <td>Electricity</td>
            <td>10</td>
            <td>600</td>
            <td>500</td>
          </tr>
          <tr>
            <td>Internet</td>
            <td>2</td>
            <td>50</td>
            <td>50</td>
          </tr>
          <tr>
            <td>Credit</td>
            <td>5</td>
            <td>350</td>
            <td>350</td>
          </tr>
          <tr>
            <td>Gas</td>
            <td>6</td>
            <td>330</td>
            <td>350</td>
          </tr>
          <tr>
            <td>Netflix</td>
            <td>1</td>
            <td>10</td>
            <td>10</td>
          </tr>
          <tr>
            <td>Spotify</td>
            <td>7</td>
            <td>10</td>
            <td>10</td>
          </tr>
          <tr>
            <td>Phone</td>
            <td>13</td>
            <td>50</td>
            <td>50</td>
          </tr>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</body>

</html>