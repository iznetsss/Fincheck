# FinCheck

## Overview
FinCheck is a web-based tool designed to make personal finance management easier by simplifying budgeting, tracking expenses, and providing clear insights into financial habits. It helps users monitor their spending, categorize transactions, and view detailed visual reports.

This project was part of a university Web Technologies course and was developed by a team of two students. The goal was to gain practical experience in front-end and back-end development using HTML, CSS, PHP, JavaScript, and MySQL. The project also focused on implementing security features to protect against common vulnerabilities like Cross-Site Scripting (XSS) and SQL Injection, ensuring the application is both secure and reliable. Overall, it was an opportunity to enhance our skills in full-stack development while creating a functional financial management tool.


## Features
- User registration and login.
- Dashboard summarizing financial activity.
- Management of income, expenses, and recurring transactions.
- Visualization of spending through charts and tables.
- Customizable settings for financial preferences.

## Project Structure

### Root Files
- **`dashboard.php`**: Main dashboard for user financial summaries.
![image](https://github.com/user-attachments/assets/f2bfeb42-b253-4f70-b4fc-29121b0f71e1)

- **`categories_chart.php`**: Displays categorized spending in table or chart format.
![image](https://github.com/user-attachments/assets/fbbd10c5-a7b6-4eee-8e5d-558aadcde7ca)

- **`money_flow.php`**: Handles cash flow tracking.
![image](https://github.com/user-attachments/assets/486aa8dc-c82b-4108-a338-8279221fcf96)

- **`recurring.php`**: Manages recurring transactions.
![image](https://github.com/user-attachments/assets/a8fd6364-0334-4777-a08d-abecfb713b95)
  
- **`advanced.php`**: Advanced financial analytics and reporting.
![image](https://github.com/user-attachments/assets/c91925c2-4e15-41aa-b345-10492fdbf75e)

- **`settings.php`**: User-specific settings.
![image](https://github.com/user-attachments/assets/4bfa8ec4-847f-4abf-9636-c038f851b989)

- **`index.php`**: Landing page and login functionality.
- **`registration.php`**: User registration functionality.


### `includes/`
- **`header.php`**: Reusable header component with branding.
- **`logout.php`**: Handles user logout.
- **`process_graph.php`**: Processes data for graphical representation.
- **`process_table.php`**: Processes and generates transaction tables.
- **`process_transactions.php`**: Handles transaction data operations.
- **`session_check.php`**: Validates active user sessions.
- **`sidebar.php`**: Reusable sidebar component.
- **`sql_connect.php`**: Database connection script.

### `styles/`
- **CSS Files**: Includes individual stylesheets for different pages (e.g., `dashboard.css`, `money-flow.css`, `settings.css`).
- **`main.css`**: Global styles and font imports.

### `img/`
- **Assets**: Includes images for branding (`logo.png`) and icons (`icon.PNG`).

### `fonts/`
- **Rubik Font**: Used for consistent typography across the application.


## Technologies Used
- **Frontend**: HTML, CSS (with custom styles for each page), JavaScript for interactive and dynamic functionalities. We also utilized the Chart.js library to create visually appealing and interactive graphs for financial data visualization.
- **Backend**: PHP for server-side logic and MySQL for database operations.
- **Database**: MySQL database.

## FinCheck

For more details, visit the project live at [FinCheck](https://enos.itcollege.ee/~ikuzne/fincheck/)
