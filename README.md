Task and User Management API
This application is a simple API for managing users and tasks, built using Yii2 and MongoDB. The API supports CRUD operations on users and tasks, including validation of task status transitions and pagination for retrieving tasks and users.

Prerequisites
Before running the application, make sure you have the following:

PHP 7.4 or higher

Yii2 Framework

MongoDB server installed and running

Composer installed to manage dependencies

Installation
Clone this repository:

bash
Copy
Edit
git clone <repository-url>
cd <repository-directory>
Install dependencies using Composer:

bash
Copy
Edit
composer install
Set up the MongoDB database:

Ensure your MongoDB instance is running.

The application will automatically use the task and user collections in MongoDB.

API Endpoints
User Endpoints
GET /users
Fetch a list of users with pagination.

GET /users/{id}
Retrieve a single user by ID.

POST /users
Create a new user. The request body must include the following fields:

login: Unique username.

password: Password (at least 6 characters, containing letters, digits, and special characters).

first_name: First name (capitalized).

last_name: Last name (capitalized).

email: Unique email address.

PUT /users/{id}
Update the user information by ID.

DELETE /users/{id}
Delete a user by ID.

Task Endpoints
GET /tasks
Fetch a list of tasks for a specific user. The request must include the id parameter to specify the user.

POST /tasks
Create a new task. The request body must include the following fields:

title: Title of the task.

description: Description of the task.

status: Task status (New, In Progress, Finished, Failed).

start_date: Task start date in d-m-Y H:i format.

user_id: User ID associated with the task.

GET /tasks/{task_id}
Retrieve a task by its ID.

PUT /tasks/{task_id}
Update a task's information by task ID.

DELETE /tasks/{task_id}
Delete a task by ID. Only tasks with status New can be deleted.

GET /tasks/{user_id}/stats
Retrieve task statistics for a specific user, grouped by status.

GET /tasks/stats
Retrieve global task statistics, grouped by status.

DELETE /tasks/{user_id}/delete-all
Delete all tasks for a specific user with status New.

Task Status Transitions
Tasks can only transition between the following statuses:

New → In Progress

In Progress → Finished or Failed

Tasks with invalid status transitions will return an error message.

Models
User Model
The User model represents a user in the system. It includes the following fields:

_id: MongoDB Object ID (auto-generated).

login: Unique username.

password: Encrypted password.

first_name: User's first name.

last_name: User's last name.

email: Unique email address.

registration_date: Date and time when the user registered.

The User model has the following relationships:

Tasks: A user can have many tasks.

Task Model
The Task model represents a task in the system. It includes the following fields:

_id: MongoDB Object ID (auto-generated).

title: Task title.

description: Task description.

status: Task status (New, In Progress, Finished, Failed).

start_date: Date and time when the task was created.

user_id: ID of the user associated with the task.

The Task model includes the following methods:

validateStatusOnCreate(): Ensures that new tasks have a New status upon creation.

beforeSave(): Validates task status transitions.

Validation
User model ensures that:

login, email are unique.

password meets security criteria (letters, digits, special characters).

first_name and last_name are capitalized.

Task model ensures:

Valid task status transitions.

start_date is in the correct format.

Errors
The API returns standard HTTP error codes in case of invalid requests:

400 Bad Request: Validation failed or missing required fields.

404 Not Found: Resource not found.

500 Internal Server Error: Server-side error.

Example Requests
Create User
bash
Copy
Edit
POST /users
Content-Type: application/json

{
  "login": "johndoe",
  "password": "password123!",
  "first_name": "John",
  "last_name": "Doe",
  "email": "john.doe@example.com"
}
Create Task
bash
Copy
Edit
POST /tasks
Content-Type: application/json

{
  "title": "Test Task",
  "description": "Description of the task",
  "status": 0,
  "start_date": "30-03-2025 14:00",
  "user_id": "user_id_here"
}
Update Task
bash
Copy
Edit
PUT /tasks/{task_id}
Content-Type: application/json

{
  "status": 1
}
