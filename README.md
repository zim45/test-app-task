# Task and User Management API

This repository contains the implementation of a RESTful API for managing `Task` and `User` resources, using the Yii2 framework and MongoDB. It includes models, controllers, and API endpoints for creating, reading, updating, and deleting tasks and users.

## Models

### Task Model

The `Task` model represents a task in the system, with the following fields:

- `_id` (ObjectId): The unique identifier of the task.
- `title` (string): The title of the task.
- `description` (string): A detailed description of the task.
- `status` (integer): The current status of the task.
- `start_date` (datetime): The start date and time of the task.
- `user_id` (ObjectId): The ID of the user who owns the task.

#### Status Constants

- `STATUS_NEW = 0`
- `STATUS_IN_PROGRESS = 1`
- `STATUS_FINISHED = 2`
- `STATUS_FAILED = 3`

#### Relationships

- **User**: A task belongs to a user (`user_id`).

### User Model

The `User` model represents a user in the system, with the following fields:

- `_id` (ObjectId): The unique identifier of the user.
- `login` (string): The login name of the user.
- `password` (string): The user's password.
- `first_name` (string): The user's first name.
- `last_name` (string): The user's last name.
- `email` (string): The user's email address.
- `registration_date` (datetime): The registration date and time of the user.

#### Relationships

- **Tasks**: A user can have multiple tasks.

## Controllers

### Task Controller

This controller handles the following actions:

- `index`: Lists all tasks for a given user.
- `view`: Retrieves a task by its ID.
- `task-create`: Creates a new task.
- `updateUserTask`: Updates a task for a specific user.
- `delete`: Deletes a task by ID.
- `deleteAll`: Deletes all tasks with `status` set to `STATUS_NEW` for a specific user.
- `stats`: Retrieves the task status statistics for a specific user.
- `globalStats`: Retrieves global task status statistics.

### User Controller

This controller handles the following actions:

- `index`: Lists all users.
- `view`: Retrieves a user by ID.
- `create`: Creates a new user.
- `update`: Updates a user by ID.
- `delete`: Deletes a user by ID.

## Example API Endpoints

### Task Endpoints

- `GET /tasks/{id}` - Retrieve tasks for a user.
- `POST /task-create` - Create a new task.
- `GET /task/{task_id}/user/{id}` - Retrieve a specific task for a user.
- `PUT /task/{task_id}/user/{id}` - Update a specific task for a user.
- `DELETE /task/{task_id}/user/{id}` - Delete a task by ID.
- `DELETE /tasks/{id}/deleteAll` - Delete all `NEW` tasks for a user.
- `GET /tasks/{id}/stats` - Retrieve statistics of tasks for a user.
- `GET /tasks/globalStats` - Retrieve global task statistics.

### User Endpoints

- `GET /users` - List all users.
- `POST /users` - Create a new user.
- `GET /user/{id}` - Retrieve a user by ID.
- `PUT /user/{id}` - Update a user by ID.
- `DELETE /user/{id}` - Delete a user by ID.

## Installation

### Requirements

- PHP 7.4 or higher
- Yii2 Framework
- MongoDB
- Composer

### Installation Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/task-user-management-api.git
   cd task-user-management-api
