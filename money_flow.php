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
    <header>
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
        <li>
            <a href="https://images.app.goo.gl/bXFCT34kEYP93jZf8">
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
      <div class="flex-zone", id="flex-zone-expenses">
        <div class="flex-container", id="expenses-input">
            <h3>Expenses</h3>
            <label for="expense-amount">Amount:</label>
            <input class="expense-amount-input"type="number" id="expense-amount" name="expense-amount" min="0">
            <label class="expense-type-label"for="expense-type">Select Expense Type:</label>
            <select class="expense-type-select"id="expense-type">
                <option id="$Transport">Transport</option>
                <option id="$Groceries">Groceries</option>
                <option id="$Eating out">Eating out</option>
                <option id="$Coffee">Coffee</option>
                <option id="$Fuel">Fuel</option>
                <option id="$Health">Health</option>
                <option id="$Beauty">Beauty</option>
                <option id="$Clothes">Clothes</option>
                <option id="$Gifts">Gifts</option>
                <option id="$Entertainment">Entertainment</option>
                <option id="$Other">Other</option>
            </select> 
            <label class="expense-type-label"for="expense-date">Date:</label>    
            <input class="calender" type="date" id="expense-date">
            <input class="btn" type="submit" id="submit-button-recurring">
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
          <h3>Income</h3>
          <label for="income-amount">Amount:</label>
          <input class="income-amount-input"type="number" id="income-amount" name="income-amount" min="0">
          <label class="income-type-label"for="income-type">Select Income Type:</label>
          <select class="income-type-select"id="income-type">
              <option id="$Employment">Employment</option>
              <option id="$Entrepreneurship">Entrepreneurship </option>
              <option id="$Investment">Investment</option>
              <option id="$Savings">Savings</option>
              <option id="$Loans">Loans</option>
              <option id="$Rent">Rent</option>
              <option id="$Dividends">Dividends</option>
              <option id="$Freelancing">Freelancing</option>
              <option id="$Gifts">Gifts</option>
              <option id="$DebtReturn">Debt Return</option>
              <option id="$Other">Other</option>
          </select> 
          <label class="income-type-label"for="income-date">Date:</label>    
          <input class="calender" type="date" id="income-date">
          <input class="btn" type="submit" id="submit-button-recurring">
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