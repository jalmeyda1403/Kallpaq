# Project Overview

This is a **Laravel 10 web application** built with PHP 8.1+. It features a modern frontend stack utilizing **Vue.js 3**, **Vite** for asset bundling, **Bootstrap**, **TailwindCSS**, and the **AdminLTE** theme for its administrative interface. The application incorporates **Livewire 3** for dynamic, server-rendered components, enhancing interactivity without extensive JavaScript.

For authentication and authorization, it uses Laravel's built-in system alongside **Spatie/laravel-permission** for robust role and permission management. Other key features include PDF generation via **barryvdh/laravel-dompdf** and seamless JavaScript routing with **tightenco/ziggy**.

Based on the extensive routes and models (e.g., `Proceso`, `Indicador`, `Hallazgo`, `Documento`, `Riesgo`, `Obligacion`, `Requerimiento`, `OUO`), this application appears to be a comprehensive management system, likely for compliance, quality management, or a similar domain, focusing on processes, indicators, audit findings, actions, risks, documents, and user management.

## Building and Running

To set up and run this project locally, follow these steps:

### Backend (PHP/Laravel)

1.  **Install PHP Dependencies:**
    ```bash
    composer install
    ```
2.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```
3.  **Run Database Migrations:**
    ```bash
    php artisan migrate
    ```
    *If you have seeders to populate the database with initial data, run:*
    ```bash
    php artisan db:seed
    ```
4.  **Start Local Server:**
    ```bash
    php artisan serve
    ```

### Frontend (JavaScript/Vite)

1.  **Install Node.js Dependencies:**
    ```bash
    npm install
    # or
    yarn install
    ```
2.  **Compile Assets for Development (with hot-reloading):**
    ```bash
    npm run dev
    # or
    yarn dev
    ```
3.  **Compile Assets for Production:**
    ```bash
    npm run build
    # or
    yarn build
    ```

The application should then be accessible at the URL provided by `php artisan serve` (typically `http://127.0.0.1:8000`).

## Development Conventions

*   **Framework:** Laravel 10 (PHP).
*   **Frontend:** Vue.js 3, Vite, Bootstrap, TailwindCSS, AdminLTE.
*   **Dynamic Components:** Livewire 3.
*   **State Management (Vue):** Pinia.
*   **Routing (Vue):** Vue Router.
*   **Authentication & Authorization:** Laravel Auth, Spatie/laravel-permission.
*   **Database:** Eloquent ORM, Migrations.
*   **Testing:** PHPUnit for backend tests (Unit and Feature).
*   **Code Style:** Likely follows Laravel's conventions, potentially enforced by `laravel/pint` (a dev dependency).
*   **Helpers:** Custom helper functions are located in `app/Helpers/`.
