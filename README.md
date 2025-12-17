## About OCR Cube

OCR cube is a demo application showcasing OCR workflows using various OCR libraries and tools.

## Setup
The application is built using the [Laravel framework](https://laravel.com/docs/12.x).

**Installation steps:**

Install the necessary dependencies using:
```
composer install
```

Copy the `env.example` file and save it as `.env` in the root folder. Update the following database connection keys in the`.env` file:
```
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Create the database tables using:
```
php artisan migrate
```

Prepare the storage folder using:
```
php artisan storage:link
```