# Symfony Shortmessage Readme

## Introduction

Welcome to the Symfony Shortmessage Project! This project is built using Symfony, a high-performance PHP web framework, and incorporates pagination for managing posts and categories with entity relations. The application allows users to create new posts and categories while efficiently handling pagination for a seamless user experience.

## Installation

Follow these steps to set up and run the Symfony Pagination Project:

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

5. **Run the Symfony Development Server:**

   ```bash
   symfony server:start
   ```

6. **Access the Application:**
   Open your browser and navigate to [http://localhost:8000](http://localhost:8000) to access the Symfony Pagination Project.

## Features

### 1. Pagination

The application uses Symfony's built-in pagination capabilities to efficiently manage and display a large number of posts and categories. Pagination ensures a smooth browsing experience for users.

### 2. Create New Post

Users can create new posts with ease. The application handles the creation process and ensures proper storage in the database.

### 3. Create New Category

Similarly, users can create new categories to organize posts effectively. The application manages the relationship between posts and categories using entity relations.

## Entity Relations

The project utilizes Symfony's Doctrine ORM to establish relationships between entities. The relationships between posts and categories are defined in the corresponding entity classes.

## Contributing

If you would like to contribute to the Symfony Pagination Project, please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make changes and test thoroughly.
4. Submit a pull request with a clear description of your changes.

## Issues and Bugs

If you encounter any issues or find bugs, please open an issue on the GitHub repository. Provide detailed information about the problem, including steps to reproduce it.

## Acknowledgments

Special thanks to the Symfony and Doctrine communities for providing powerful tools and resources for web development.

Enjoy using the Symfony Pagination Project! If you have any questions or suggestions, feel free to reach out. Happy coding!
