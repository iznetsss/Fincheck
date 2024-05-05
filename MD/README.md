# SPECIFICATION FOR FINCHECK
[https://enos.itcollege.ee/~ikuzne/fincheck](url)
## Overview

### General summary

#### Problem
Managing personal finances is often complex and time-consuming, with existing tools lacking user-friendly interfaces. Our web project addresses this by offering an intuitive, efficient, and holistic financial tracking solution, making budgeting and goal-setting easier for users.

#### Target Audience
Our financial tracker is created for individuals of all backgrounds, including young professionals, families, elders, and freelancers. The user-friendly interface caters to both beginners and experienced users, providing a comprehensive solution for effective financial management.

## Team

- 1. Ivan Kuznetsov: 233767IVSB
- 1. Kirill Sevtsov: 233735IVSB


## Goals, Objectives and Phases

### Objecive

We are striving to create a user-friendly but at the same time powerful too for managing personal finances. Considering the disadvantages of tools that we have been using it the past, we are trying to create a solution that will be convinient for everyone.

### Goals

1. Security-First Design
Goal: Implement robust security measures at every layer of the application.
Objectives: 
- Use secure coding practices to prevent common vulnerabilities such as SQL injection, cross-site scripting (XSS) and etc.
- Implement proper authentication and authorization checks.

2. User-Centric Features
Goal: Design the application with a strong focus on user experience and functionality.
Objectives:
- Develop a clean, intuitive user interface that is responsive and accessible on various devices.
- Allow users to track expenses, income, investments, and savings.
- Offer customizable categories for budgeting.
- Provide visual reports (charts, graphs) to help users analyze their financial data.

3. Data Integrity and Privacy
Goal: Ensure data integrity and maintain user privacy.
Objectives:
- Ensure the protection of user privacy and data accuracy by securing sensitive data when stored and during transmission.

4. Testing and Validation
Goal: Thoroughly test the application to ensure reliability and security.
Objectives:
- Conduct unit and integration testing to identify and fix bugs early in the development process.
- Perform security testing, including penetration testing and vulnerability assessments, to identify and mitigate risks.


### Phases

1. HTML/CSS
2. PHP
3. JS/SQL

## Content structure

### Site map
```
HOME (index.php - login)
  +--REGISTRATION (registration.php)
  |
  +--DASHBOARD (dashboard.php)
       +--CATEGORIES CHART (categories_chart.php)
       +--RECURRING (recurring.php)
       +--MONEY FLOW (money_flow.php)
       +--ADVANCED (advanced.php)
       +--SETTINGS (settings.php)

```
### Content types
**Forms**
Purpose: Gather and update financial data in a structured way.
Aims:
- Data Entry: Allow input of transactions and financial goals efficiently.
- Customization: Enable users to tailor categories and budget plans.
- Search and Filter: Offer tools to filter financial data easily.


**Tables**
Purpose: Organize detailed data for easy review and analysis.
Aims:
- Itemization: Display transactions and budgets for easy viewing and editing.
- Comparison: Compare spending across categories or time periods.


**Charts**
Purpose: Visualize data to uncover trends and relationships.
Aims:
- Trend Analysis: Show financial growth or spending patterns over time.
- Distribution: Illustrate how expenses split across different categories.


## Design
Simplicity: The interface has been designed to be clear and straightforward, with easy navigation and interaction facilitated for users of all levels.

## Functionality
Consistency: Consistent design elements are maintained throughout the application, aiding users in quickly adapting and feeling comfortable with our layout and functionality.

## Browser support
Supporting all browsers ensures that users can access "Fincheck" regardless of their preferred technology, whether they're using mainstream browsers like Chrome, Firefox, and Safari, or less common ones.

## Hosting
**ENOS.**
