# E-Learning Backend API (Laravel + MongoDB)

##  Project Overview

This is a backend API for an **E-Learning platform** built using **Laravel** and **MongoDB**.

It supports:

* Module-based course progression
* Sequential unlocking logic
* User progress tracking
* Leaderboard system
* Latest activity tracking

---

## ️ Tech Stack

* **Backend:** Laravel 12
* **Database:** MongoDB
* **Authentication:** JWT (API आधारित)
* **Architecture:** Repository Pattern
* **Query Handling:** MongoDB Aggregation Pipelines

---

##  Features

### 1. Module Progress Tracking

* Track module status:

  * `not_started`
  * `in_progress`
  * `completed`
* Store user score per module

---

###  2. Sequential Module Unlocking

* Completed modules → unlocked
* First `not_started` after completed → unlocked
* Remaining modules → locked

---

###  3. Course Progress

* Total modules per course
* Completed modules per user
* Progress percentage

```json
{
  "course_id": "1001",
  "total_modules": 10,
  "completed_modules": 6,
  "progress": 60
}
```

---

###  4. Latest Activity Tracking

* Returns last accessed module per user per course
* Based on `updated_at`

---

###  5. Leaderboard (Top Users per Course)

* Ranked by:

  1. Highest completed modules
  2. Highest average score (tie breaker)

---

##  API Endpoints

###  Create / Update Module Progress

```
POST /api/user-modules/createOrUpdate
```

---

###  Get Sequential Modules

```
GET /api/user-modules/{course_id}
```

---

###  Get Course Progress

```
GET /api/user-modules/getCourseProgress/{course_id}
```

---

###  Get Latest Activity

```
GET /api/user-modules/getLatestActivity/{course_id}
```

---

###  Get Leaderboard

```
GET /api/user-modules/top-users/{course_id}
```

---

##  Installation

### 1 Clone the repository

```
git clone https://github.com/kunthavai/laravel_app.git
cd laravel_app
```

### 2Install dependencies

```
composer install
npm install
```

### 3️Setup environment

```
cp .env.example .env
php artisan key:generate
```

### 4️Configure MongoDB

Update `.env`:

```
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=elearning
```

---

##  Run Project

```
php artisan serve
```

---

##  Key Concepts Used

* MongoDB Aggregation (`$lookup`, `$group`, `$setWindowFields`)
* Repository Pattern (Clean Architecture)
* API Design Best Practices
* JWT Authentication
* Data Validation & Error Handling

---

##  Future Improvements

* Role-based access (Admin/User)
* Course enrollment system
* Caching (Redis)
* Pagination & filtering
* UI integration

---

##  Author

**Kunthavai PK**

---

## ⭐ If you like this project

Give it a ⭐ on GitHub!
