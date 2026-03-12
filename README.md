# PROG 06
## Installation
```bash
composer install
composer dump-autoload
php artisan migrate
php artisan db:seed
php artisan serve
```

## Project structure (important files only)
```bash
.
├── README.md
├── app
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── AssignmentController.php
│   │   │   ├── AuthController.php
│   │   │   ├── ChallengeController.php
│   │   │   ├── Controller.php
│   │   │   ├── FileController.php
│   │   │   ├── HomeController.php
│   │   │   ├── MessageController.php
│   │   │   └── ProfileController.php
│   │   └── Middleware
│   │       └── CheckConcurrentLogin.php
│   └── Models
│       ├── Assignment.php
│       ├── Challenge.php
│       ├── Message.php
│       ├── Submission.php
│       └── User.php
│       
├── database
│   ├── factories
│   │   └── UserFactory.php
│   ├── migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_03_11_035134_create_assignments_table.php
│   │   ├── 2026_03_11_035138_create_submissions_table.php
│   │   ├── 2026_03_11_035143_create_challenges_table.php
│   │   └── 2026_03_11_035146_create_messages_table.php
│   └── seeders
│       └── DatabaseSeeder.php
├── public
│   ├── assets
│   │   ├── css
│   │   │   └── style.css
│   │   ├── default-avatar.jpg
│   │   └── js
│   │       ├── chall.js
│   │       ├── message-handling.js
│   │       └── script.js
│   ├── favicon.ico
│   ├── index.php
│   └── robots.txt
├── resources
│   ├── css
│   │   └── app.css
│   ├── js
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views
│       ├── assignments.blade.php
│       ├── challenges.blade.php
│       ├── create-student.blade.php
│       ├── home.blade.php
│       ├── layouts
│       │   └── app.blade.php
│       ├── login.blade.php
│       ├── profile.blade.php
│       └── welcome.blade.php
└── routes
    ├── console.php
    └── web.php
```