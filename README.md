# Conference-Fusion: A Conference Schedule Builder

## Placeholder Readme
Conference-Fusion is a session/schedule planner for conferences. This online tool allows you to submit and brainstorm session ideas, create a call for panelists/papers, and arrange the schedule and rooms for the conference. You can also manage presenter information, like bios and contact info, with flexible database fields you can customize to your specific conference.

This is just placeholder readme until the app reaches a full beta. If you are interested in installing or contributing, contact the creator here. Standard install and best-practices for a Laravel App apply here. 

## Installation
- Clone the repo and cd into the project directory
- Create a MySQL DB and grant access to a user for the app
- Requires php-gd, ensure it's installed on server
- Create .env file from .env.example and edit it to include:
  - DB connection info
  - App encryption key (or run `php artisan key:generate`)
  - Mail settings (required for forgot password emails)
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

### To Run Locally with Laravel Sail
Start Docker Desktop, then run these commands from the project directory.
On a fresh checkout, copy `.env.example` to `.env` and install the locked PHP
dependencies using PHP 8.3:

```sh
cp .env.example .env
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$PWD:/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --no-interaction --prefer-dist
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
./vendor/bin/sail artisan storage:link
```

Only generate the key and seed the database on initial setup. Confirm the seed
prompt to create the initial conference and default administrator.

Open http://localhost. The environment example uses `DB_HOST=mysql` to reach the
Sail database container. If ports 80 or 3306 are already in use, set `APP_PORT`
and/or `FORWARD_DB_PORT` in `.env` before starting Sail; when changing `APP_PORT`,
also update `APP_URL` to include that port. Keep `DB_PORT=3306` for communication
between containers.

For subsequent starts, run `./vendor/bin/sail up -d`; stop the containers with
`./vendor/bin/sail down`. This project uses Laravel Mix, so `npm run dev` builds
assets once; use `./vendor/bin/sail npm run watch` when editing frontend assets.
Sail is for local development, not a production deployment. The default database
and administrator passwords must not be used on a public server. Email requires
configuring a reachable mail server; the compose configuration does not include
MailHog.

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

## Public schedule publication

Conferences start with `schedule_published=false`, including existing conferences
when the publication migration runs. Apply schema updates with `php artisan migrate`.
An admin can sync, publish, or unpublish a conference from its Conference Info
page. First click **Sync session schedule** to save the current scheduled events
and their public data, then **Publish session schedule** to make that snapshot
visible. Neither action changes session statuses or the internal planning schedule.

`/api/public-schedule` uses the default conference's publication setting.
Unpublished schedules return empty `events`, `presenters`, and `eventPresenters`
arrays while retaining metadata, sponsors, and announcements.
`/api/public-schedule-test` is an ungated draft preview: it returns all schedule
entries on non-administrative tracks, regardless of session status or publication.
The public endpoint loads its schedule data from the saved JSON snapshot. It
only queries the database to select the default conference and check publication.
Edits to events, presenters, rooms, tracks, sponsors, and announcements become
public only after another sync. Syncing a published conference updates its public
snapshot immediately; syncing an unpublished conference does not publish it.
Unpublishing hides schedule events immediately without deleting the snapshot.
Already-open clients must reload to see these changes.

Snapshots are stored per conference in `storage/app/schedule-snapshots/` on the
local filesystem disk, outside the web root. Preserve this directory across
deployments and share it between web instances if using multiple servers. A sync
replaces the JSON file atomically. Publishing requires an existing snapshot; if
a published snapshot is lost, the public endpoint returns HTTP 503 until synced.

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
