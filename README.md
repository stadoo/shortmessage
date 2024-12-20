# Symfony Shortmessage Readme

## Introduction

Welcome to my Symfony Shortmessage Project! This project is built using Symfony, a high-performance PHP web framework. The application allows users to create posts under self-created categories. These posts can be liked or disliked, and their status updates automatically in a live view. Additionally, each post can be commented on by users, enabling interactive discussions. As an admin, you can delete posts and manage users. As a user, you can create a profile with links to your social media accounts, which are automatically recognized and displayed with appropriate icons.

This project serves two main purposes:

1.  To demonstrate my programming skills for job applications.
2.  To explore and test various functionalities of the Symfony framework.

## Installation

Follow these steps to set up and run the Symfony Shortmessage Project:

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/stadoo/shortmessage.git
   ```

2. **Navigate to the Project Directory:**

   ```bash
   cd shortmessage
   ```

3. **Install Dependencies:**

   ```bash
   composer install
   ```

4. **Database Configuration:**

   - Configure your database connection in the `.env` file.
   - Create the database and execute migrations:

     ```bash
     php bin/console doctrine:database:create
     php bin/console doctrine:migrations:migrate
     ```

5. **Database installation**

   - execute instalation of the database:

   ```bash
   php bin/console doctrine:schema:update --force
   ```

6. **install Fixtures**

   - Config Fixtures
     Edit config/services.yaml and set the appropriate true/false values to fill the database automatically.
     IMPORTANT: Note that post fixtures can only be executed if the corresponding categories exist!
   - Install Fixtures:

   ```bash
   php bin/console doctrine:fixtures:load --append
   ```

7. **Run the Symfony Development Server:**

   ```bash
   symfony server:start
   ```

8. **Access the Application:**
   Open your browser and navigate to [http://localhost:8000](http://localhost:8000) to access the Symfony Pagination Project.

## Features

### 1. User Management System

User Signup and Login with password recovery (Users receive an email with a link and token).

![login](/github-assets/login.png?raw=true "Login")
![password-request](/github-assets/password-request.png?raw=true "password-request")

### 2. Pagination

The application uses Symfony's built-in pagination capabilities to efficiently manage and display a large number of posts and categories, ensuring a smooth browsing experience.

![Pagination](/github-assets/pagination.png?raw=true "Pagination")

### 3. Create New Post

Users can easily create new posts. The application handles the creation process and ensures proper storage in the database.

![Create new Posts](/github-assets/create-new-post.png?raw=true "Create new posts")

### 4. Create New Category

Admin can create new categories to organize posts effectively. The application manages the relationship between posts and categories using entity relations.

![Create New Category](/github-assets/create-new-category.png?raw=true "Create New Category")

### 5. Post Thumbsup or down

Users can give posts a thumbs up or down. The feedback is automatically updated and displayed in real-time.

### 6. Commenting on Posts

Users can comment on each post, enabling interactive discussions.

![Commenting on Posts](/github-assets/create-new-comment.png?raw=true "Commenting on Posts")

### 7. Search engine

Users can search for posts by title, text, and user email.

![Search engine](/github-assets/search.png?raw=true "Search engine")

### 8. Sorting function

Sort posts by date, ID, thumbs up, or thumbs down.

![Sorting function](/github-assets/sorting.png?raw=true "Sorting function")

### 9. User Profile + Social links

Users can create a profile with links to their social media accounts. The links are automatically recognized and validated, and the corresponding icons are displayed on the profile.

![Add Social Link](/github-assets/add-sociallinks.png?raw=true "Add Social Link")

![User Profile + Social links](/github-assets/userprofile-social.png?raw=true "User Profile + Social links")

### 10. Fill DB with Test datas

Fixtures are created to populate the database with random data for testing purposes.

### 11. Admin User Controlling

Admins can manage user roles, passwords, and emails through the admin console. Admins can also delete users, which will also delete all of their posts and comments.

![Admin Userlist](/github-assets/admin-userlist.png?raw=true "Admin Userlist")

![Admin](/github-assets/edit-user-roles.png?raw=true "Admin")

## Entity Relations

Relationships exist between the posts and categories; post author to user;

## Issues and Bugs

If you encounter any issues or find bugs, please open an issue on the GitHub repository. Provide detailed information about the problem, including steps to reproduce it.
