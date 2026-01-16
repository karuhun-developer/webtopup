# Laravel Inertia Vue Starter Kit

A robust, modern starter kit built with **Laravel 12**, **Inertia.js v2**, and **Vue 3**. Designed for rapid development of CMS and management applications with a focus on developer experience and UI quality.

## 🚀 Features

- **Authentication & Security**
    - Powered by **Laravel Fortify** for backend authentication logic.
    - **Laravel Sanctum** for API token management.
    - Secure user management with password updates.

- **Role-Based Access Control (RBAC)**
    - Integrated with **Spatie Laravel Permission**.
    - **Visual Management UI**: create roles, assign extensive permissions, and manage user access with ease.

- **Dynamic Menu Builder**
    - **Menu Management**: Create and reorder sidebar menus.
    - **Nested Sub-Menus**: Support for multi-level navigation structures managed directly from the dashboard.

- **Modern Frontend Stack**
    - **Vue 3** (Composition API) with **TypeScript**.
    - **Inertia.js v2**: Build modern single-page apps using classic server-side routing.
    - **Tailwind CSS v4**: The latest utility-first CSS framework.
    - **Shadcn-vue**: Accessible, customizable UI components built on **Radix Vue**.
    - **Lucide Icons**: Beautiful, consistent icon set.

- **Developer Experience**
    - **Laravel Wayfinder**: Type-safe route generation for Inertia.
    - **Prettier & ESLint**: configured for consistent code style.
    - **Activity Logging**: powered by `spatie/laravel-activitylog`.

## 📦 Tech Stack

- **Backend**: PHP 8.2+, Laravel 12
- **Frontend**: Vue 3, Inertia.js
- **Styling**: Tailwind CSS v4
- **Database**: SQLite (default), MySQL, or PostgreSQL
- **Package Manager**: Composer (PHP), NPM (JS)

## 🛠 Installation

Follow these steps to set up the project locally.

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM

### Steps

1.  **Clone the Repository**

    ```bash
    git clone
    cd template-laravel-inertia
    ```

2.  **Install PHP Dependencies**

    ```bash
    composer install
    ```

3.  **Setup Environment Variables**
    Copy the example entry file and configure your database settings.

    ```bash
    cp .env.example .env
    ```

4.  **Generate Application Key**

    ```bash
    php artisan key:generate
    ```

5.  **Run Database Migrations**

    ```bash
    php artisan migrate --seed
    ```

6.  **Install Node Dependencies**

    ```bash
    npm install
    ```

7.  **Start Development Server**

    ```bash
    php artisan serve
    ```

    ```bash
    npm run dev
    ```

    Access the application at `http://localhost:8000`.

## 📚 Key Packages

### Backend (Composer)

| Package                          | Version | Description                                 |
| :------------------------------- | :------ | :------------------------------------------ |
| `laravel/framework`              | ^12.0   | The core Laravel framework.                 |
| `inertiajs/inertia-laravel`      | ^2.0    | Server-side adapter for Inertia.js.         |
| `laravel/fortify`                | ^1.30   | Backend-only authentication implementation. |
| `laravel/sanctum`                | ^4.0    | API token authentication system.            |
| `spatie/laravel-permission`      | ^6.24   | Associate users with roles and permissions. |
| `spatie/laravel-activitylog`     | ^4.10   | Log activity inside your Laravel app.       |
| `spatie/laravel-medialibrary`    | ^11.17  | Associate files with Eloquent models.       |
| `laravel/wayfinder`              | ^0.1.9  | Helper for generating typed routes for JS.  |
| `rap2hpoutre/laravel-log-viewer` | ^2.5    | UI for viewing Laravel logs.                |

### Frontend (NPM)

| Package                | Version | Description                                 |
| :--------------------- | :------ | :------------------------------------------ |
| `@inertiajs/vue3`      | ^2.1.0  | Vue 3 adapter for Inertia.js.               |
| `vue`                  | ^3.5.13 | The progressive JavaScript framework.       |
| `tailwindcss`          | ^4.1.1  | Utility-first CSS framework.                |
| `reka-ui`              | ^2.6.1  | Unstyled, accessible UI primitives for Vue. |
| `@inertiaui/modal-vue` | ^1.0.0  | Modal handling for Inertia apps.            |
| `lucide-vue-next`      | ^0.468  | Icon library.                               |
| `dayjs`                | ^1.11   | Fast 2kB immutable date time library.       |
| `@tanstack/vue-table`  | ^8.21   | Headless UI for building powerful tables.   |
