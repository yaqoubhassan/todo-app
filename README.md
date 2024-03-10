# Laravel Todo App

This is a simple todo API that allows authenticated users to perform all CRUD operations for todo items.

## Project Setup

1. **Create Database**: Create a database and name it exactly as specified in the `.env` file.

2. **Migrate Database**: Run the following command to populate your database with the necessary tables:

    ```bash
    php artisan migrate
    ```

3. **Fetch Users' Data**: Run the following command to fetch users' data from "https://jsonplaceholder.typicode.com/users" API and populate the `users` table in the database:

    ```bash
    php artisan app:fetch-users-data
    ```

    This command makes a request to the "https://jsonplaceholder.typicode.com/users" api, fetches all (ten) users, creates an entry (account) for each of the users into the `users` table in the db with common password of "password". With this, anyone who wants to test, can pick any user's email from the `users` table with password as "password" and make a request to the "login" endpoint to generate a token for accessing endpoints that require authentication.

4. **Run Tests**: Execute the following command to run all unit tests written for all endpoints:

    ```bash
    php artisan test
    ```

    This command will run all unit tests to ensure the functionality of the endpoints.

## API Endpoints

### Authentication

-   **POST /api/login**
    -   Authenticate user and generate token
    -   Request Body:
        ```json
        {
            "email": "user@example.com",
            "password": "password"
        }
        ```
    -   Response:
        ```json
        {
            "token": "generated_token_here",
            "token_type": "Bearer",
            "token_expiration": "2024-02-14T12:00:00Z",
            "email_verified": true,
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "user@example.com"
            }
        }
        ```

### Todos

-   **List all todo items**

    -   Method: GET
    -   Endpoint: `/api/todos`
    -   Description: Retrieve all todo items.

-   **Create a todo item**
    -   Method: POST
    -   Endpoint: `/api/todos`
    -   Description: Create a new todo item.
    -   Request Body:
        ```json
        {
            "title": "string",
            "completed": "boolean"
        }
        ```
-   **Retrieve a todo item**

    -   Method: GET
    -   Endpoint: `/api/todos/{id}`
    -   Description: Retrieve a single todo item by its ID.

-   **Update a todo item**

    -   Method: PUT
    -   Endpoint: `/api/todos/{id}`
    -   Description: Update an existing todo item by its ID.
    -   Request Body:
        ```json
        {
            "title": "string",
            "completed": "boolean"
        }
        ```

-   **Delete a todo item**
    -   Method: DELETE
    -   Endpoint: `/api/todos/{id}`
    -   Description: Delete a todo item by its ID.
