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
  <header id=>
  <img src="img/logo.png" width="200px">
  </header>
  <div class="sidebar">
        <ul>
        <li>
            <a href="dashboard.php">
            <i class='bx bxs-home'></i>
            <span>Home</span>
            </a>
        </li>
        <li>
            <a href="categories_chart.php">
            <i class='bx bxs-category'></i>
            <span>Categories</span>
            </a>
        </li>
        <li>
            <a href="recurring.php">
            <i class='bx bxs-calendar'></i>
            <span>Recurring</span>
            </a>
        </li>
        <li>
            <a href="money_flow.php">
            <i class='bx bx-line-chart'></i>
            <span>Cash flow</span>
            </a>
        </li>
        <li>
            <a href="advanced.php">
            <i class='bx bx-table'></i>
            <span>Advanced</span>
            </a>
        </li>
        <li class="dropdown">
            <a class="dropdown-toggle"> <!--JS SCRIPT DROP-DOWN MENU FOR CURRENCY-->
                <i class='bx bx-euro', id='selected-currency'></i>
                <span>Currency</span>
            </a>
            <ul class="dropdown-menu" id="currency-dropdown">
                <li><a href="#"><i class='bx bx-euro' ></i></i> EUR</a></li>
                <li><a href="#"><i class='bx bx-dollar' ></i> USD</a></li>
                <li><a href="#"><i class='bx bx-pound' ></i> GBR</a></li>
                <li><a href="#"><i class="bx bx-lira"></i> TRY</a></li>
                <li><a href="#"><i class='bx bx-yen' ></i> CNY</a></li>
                <li><a href="#"><i class='bx bx-bitcoin' ></i> BTC</a></li>
            </ul>
        </li>
        <li>
            <a href="settings.php">
            <i class='bx bxs-cog'></i>
            <span>Settings</span>
            </a>
        </li>
        <li>
            <a href="index.php">
            <i class='bx bx-run'></i>
            <span>Logout</span>
            </a>
        </li>
        </ul>
  </div>

  <div class="content">
    <div class="flex-container" id="flex-input-bill">
      <span class="header2">New recurring payment</span>
      <label for="bill-name">Name:</label>
      <input class="bill-name-input" type="text" id="bill-amount" name="bill-amount">
      <label for="bill-due">Due:</label>
      <input type="date" class="bill-due-select" id="day">
      <label for="bill-actual">Amount:</label>
      <input class="bill-actual-input" type="number" id="bill-actual" name="bill-actual" min="0">
      <label for="bill-due">Repeat:</label>
      <select class="bill-due-select" id="day">
        <option value="monthly">Every Month</option>
        <option value="annualy">Every Year</option>
        <option value="Weekly">Every Week</option>
        <option value="2weekly">Every 2 Weeks</option>
        <option value="daily">Every Day</option>
      </select>
      <input class="btn" type="submit" id="submit-button-recurring">
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