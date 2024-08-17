
# 🚧 Under Development 🚧

# Euskoplan API

Backend in REST API format created in Laravel for the [Euskoplan Client](https://github.com/asier-ortiz/euskoplan-client) project.

It allows the creation and management of tourism plans using various catalogs from the [Open Data Euskadi](https://opendata.euskadi.eus/inicio/) website in XML format as data sources. This information is updated in the project's database through a parser and a Cron job.

It uses the Mapbox API to calculate the itinerary route and manages authorization and authentication using [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum) and [Laravel Gates](https://laravel.com/docs/10.x/authorization#gates).

# Instructions

Before starting, make sure you have Docker installed.

- [Docker](https://www.docker.com/)

## 1. Laravel .env Configuration File

- Navigate to the Laravel project directory

```shell
cd euskoplan-api
```

- Copy `.env.example` to `.env`

On Windows

```shell
copy .env.example .env
```

On macOS / Linux

```shell
cp .env.example .env
```

- Modify the application name in the `.env` file

```text
APP_NAME=Euskoplan
```

- Modify the database credentials in the `.env` file

```text
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=api
DB_USERNAME=user
DB_PASSWORD=password
```

- Modify the email server settings in the `.env` file

```text
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=euskoplan@test.com
MAIL_FROM_NAME="${APP_NAME}"
```

- Add the Mapbox token in the `.env` file

```text
MAP_BOX_TOKEN="<YOUR_KEY>"
```

## 2. Start Docker and Launch the Containers

- From the root directory of the project, run the following command and wait for it to finish:

```shell
docker compose up -d
```

## 3. Vendor Directory and Encryption Key Generation

> :warning: This step is only necessary the first time.

- From the root directory of the project, run the following command and wait for the installation to complete:

```shell
docker compose exec php composer install
```

> :warning: This step is only necessary the first time.

- From the root directory of the project, run the following command:

```shell
docker compose exec php php artisan key:generate
```

## 4. Migrations

- To generate the tables, from the root directory of the project, run:

```bash
docker compose exec php php artisan migrate
```

## 5. Seeders

- To load the data into the database, from the root directory of the project, run:

```bash
docker compose exec php php artisan db:seed
```

## 6. Access the Services

- [phpMyAdmin](http://localhost:8081) (credentials --> db / user / password)
- [Mailhog](http://localhost:8025/)
- [Postman](https://www.postman.com/): Download the following [file](https://drive.google.com/file/d/1KtY4w0z94aVRbSv4h-5wdPcGCgjUzA68/view?usp=sharing) and import it to test the endpoints

## 7. Shell Access to the Application Container

```shell
docker compose exec php /bin/bash
```

## 8. Stop the Containers

```shell
docker compose stop
```

## Other

- There is a test user to access the web application with the following credentials:
    - Username: test
    - Email: test@test.com
    - Password: password

- The database can be updated via a Cron job by running the following command:

> :warning: It may take hours to complete.

```bash
docker compose exec php php artisan collections:cron
```

## Screenshots

<img src="screenshots/screenshot-1.png" width="300">
