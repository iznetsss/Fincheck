# FinCheck

## Overview
FinCheck is a web-based personal financial tracking solution designed to simplify budgeting, manage expenses, and provide detailed insights into personal finances. It aims to address the complexities of managing finances by offering an intuitive and user-friendly interface for tracking spending, categorizing transactions, and generating visual reports. This project was developed as part of a university course in Web Technologies, created by a team of two students. The primary goal was to gain hands-on experience in both front-end and back-end development, utilizing technologies such as HTML, CSS, PHP, JavaScript, and MySQL. Additionally, the project provided an opportunity to explore and implement security measures to prevent common vulnerabilities like Cross-Site Scripting (XSS) and SQL Injection, ensuring a secure and robust application. Through this project, we aimed to deepen our understanding of full-stack development while creating a practical tool for personal financial management.

## Features
- User registration and login.
- Dashboard summarizing financial activity.
- Management of income, expenses, and recurring transactions.
- Visualization of spending through charts and tables.
- Customizable settings for financial preferences.

## Project Structure

### Root Files
- **`index.php`**: Landing page and login functionality.
- **`dashboard.php`**: Main dashboard for user financial summaries.
- **`advanced.php`**: Advanced financial analytics and reporting.
- **`categories_chart.php`**: Displays categorized spending in chart format.
- **`money_flow.php`**: Handles cash flow tracking.
- **`recurring.php`**: Manages recurring transactions.
- **`registration.php`**: User registration functionality.
- **`settings.php`**: User-specific settings.

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
- **Frontend**: HTML, CSS (with custom styles for each page), JavaScript for interactive and dynamic functionalities.
- **Backend**: PHP for server-side logic and MySQL for database operations.
- **Database**: MySQL database.
