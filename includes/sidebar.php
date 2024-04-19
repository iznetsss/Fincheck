<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('logoutLink').addEventListener('click', function() {
      var confirmLogout = confirm("Are you sure you want to logout?");
      if (confirmLogout) {
        fetch('includes/logout.php') 
          .then(response => window.location.href = 'index.php')  // Redirect after the fetch completes
      }
    });
  });
</script>


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
            <a href="settings.php">
            <i class='bx bxs-cog'></i>
            <span>Settings</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0);" id="logoutLink">
            <i class='bx bx-run'></i>
            <span>Logout</span>
            </a>
        </li>
        </ul>
  </div>

  