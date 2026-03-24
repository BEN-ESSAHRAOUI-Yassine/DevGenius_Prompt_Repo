# DevGenius: Prompt Repository System

## Overview

DevGenius is a web-based platform to manage, categorize, and share programming and AI prompts. It provides a role-based system with **Admin** and **Developer** access levels. Admins can manage users, categories, and prompts, while developers can create and manage their own prompts.

---

## Features

- **User Management**
  - Admins can add, edit, and delete users.
  - Role-based access: Admin or Developer.
  
- **Category Management**
  - Admins can add, edit, and delete categories.
  - Assign prompts to categories.

- **Prompt Management**
  - Create, edit, and delete prompts.
  - Assign prompts to categories.
  - Status management: Approved, Rejected, Deployed.
  - Search, sort, and filter prompts.

- **Dashboard**
  - Overview of total prompts, filtered results, and user-specific prompts.
  - Quick links for admins to manage users and categories.

---

## Installation

1. Clone the repository:
```bash
git clone <repo-url>
```
2. Import the database:
```bash
mysql -u root -p < Database/schema.sql
```
3. Configure database connection in auth/db.php.

4. Start your PHP server:
```bash
php -S localhost:8000
```
5. Navigate to http://localhost:8000 in your browser.

## Technologies Used

*   PHP 8+
*   MySQL
*   PDO for database access
*   HTML, CSS
*   Role-based authentication system

## Directory Structure

```
📁 DevGenius_Prompt_Repo
└── 📁admin                             # Admin management pages (users, categories)
    ├── add_category.php        
    ├── add_user.php
    ├── categories.php
    ├── delete_category.php
    ├── delete_user.php
    ├── edit_category.php
    ├── edit_user.php
    └── users.php
└── 📁Assets                            # CSS and dynamic style generation
    └── 📁css
        ├── style.css
        ├── style.php
    └── 📁imgs
└── 📁auth                              # Authentication & database connection
    ├── auth.php
    ├── db.php
    └── role.php
└── 📁Database                          # Database schema & initial data
    └── schema.sql
└── 📁devgest                           # Developer prompt management
    ├── add_prompt.php
    ├── delete_prompt.php
    └── update_prompt.php
└── index.php                           # Dashboard & main page
└── login.php                           # Login logic
└── logout.php                          # Logout logic
└── newlogin.php                        # create new account logic
└── README.md

```

## UML Diagrams

1. Use Case Diagram

*   Actors: Admin, Developer
*   Use Cases: Login, Create Prompt, Edit Prompt, Delete Prompt, Manage Users, Manage Categories, View Dashboard

2. Class Diagram

*   Classes:

    *   User (id, username, email, password, role)
    *   Category (id, name)
    *   Prompt (id, title, content, status, category_id, user_id)
    *   Auth (login, logout)
    *   Role (permission checks)
    *   Database (connection)

3. Activity Diagram

Adding a new prompt:
User logs in → selects "Add Prompt" → fills form → submits → validation → saved to database → redirected to dashboard.

4. Sequence Diagram

Create Prompt Sequence:
Developer → Add Prompt Page → validates form → Prompt Model → Database → store → redirect → Dashboard.
Security Measures
Password hashing with password_hash().
Role-based access control.
Prepared statements to prevent SQL injection.
Input validation and sanitization.
Session-based authentication.

## Notes
Admin role is required for user and category management.
Developers can only manage prompts they own.
Dynamic CSS classes are generated based on categories for consistent UI colors.