# Book Store Admin

This is a Laravel-based web application for managing a book store. The app allows an admin user to add, edit and delete books, manage book categories, and view book orders.

## Installation

To run this app, follow these basic steps:

1. Clone the repository to your local machine using the command `https://github.com/PeranoXx/Book-store-admin-api.git`.
2. Navigate to the project directory using the command `cd Book-store-admin-api.git`.
3. Install the project dependencies by running `composer install`.
4. Copy the `.env.example` file to `.env` using the command `cp .env.example .env`.
5. Generate a new application key by running the command `php artisan key:generate`.
6. Set up your database by adding your database credentials to the `.env` file.
7. Run the database migrations using the command `php artisan migrate`.
8. Seed the database with sample data using the command `php artisan db:seed`.

### note : add the currunt working port in .env APP_URL
example : If application is runnig on `http:127.0.0.1:8000` then update .env to this
`APP_URL=http://127.0.0.1:8000`

## Filament Package

This app uses the [Filament](https://filament.com/docs/installation/) package, which is a Laravel-based admin panel. Filament provides a user-friendly interface for managing the app's content and users.

## API Endpoints

This app provides the following API endpoints:

- `/api/v1/search` - This endpoint returns a list of books. 
- `/api/v1/home/index` - This endpoint returns popular authors books for home page
- `/api/v1/book` - This endpoint returns a list of all book categories in the database.
    - parameters : `limit, field, dir, author, genre, search, pub_date_from, pub_date_to`
- `/api/v1/book{slug}` - This endpoint returns the details of a specific book category.
