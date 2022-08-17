# Conference-Fusion: A Conference Schedule Builder

## Placeholder Readme
Conference-Fusion is a session/schedule planner for conferences. This online tool allows you to submit and brainstorm session ideas, create a call for panelists/papers, and arrange the schedule and rooms for the conference. You can also manage presenter information, like bios and contact info, with flexible database fields you can customize to your specific conference.

This is just placeholder readme until the app reaches a full beta. If you are interested in installing or contributing, contact the creator here. Standard install and best-practices for a Laravel App apply here. 

## Installation
- Clone the repo and cd into the project directory
- Create a MySQL DB and grant access to a user for the app
- Requires php-gd, ensure it's install on server
- Create .env file from .env.example and edit it to include:
  - DB connection info
  - App encryption key (or run `php artisan key:generate`)
- Run `composer install` from the project directory
- Run `npm install` from the project directory
- Install the DB migrations - Run `php artisan migrate`
- Seed the DB - Run `php artisan db:seed`
- Create a custom view views/custom/welcome.blade.php
- Create a custom view views/custom/terms.blade.php

### To Run Locally
- cd into the project directory
- Run `php artisan serve`
- Run `npm run dev`

### Login
Default User: admin@example.com

Default Password: password

THIS MUST BE CHANGED BEFORE USING THE SITE

## Customizations and Personalizations
- Database seeds and csv files labeled CustomImport will be ignored in git
- Custom images may be uploaded to public/images/design
- Custom Views for terms and conditions and welcome can be added to
  - resources/views/custom/terms.blade.php
  - resources/views/custom/welcome.blade.php

## To Dos
- Make Session/panel/presentaion type names dynamic
  - Create flag to include them in the call
- Make Consignment Sales into an optional module 



## Built in Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
