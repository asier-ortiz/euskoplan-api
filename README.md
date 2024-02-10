# Euskoplan API

Backend en formato API Rest creado en Laravel para el proyecto Euskoplan.  

Permite la creación y gestión de planes turísticos utilizando como fuente de datos diferentes catálogos de la web [Open Data Euskadi](https://opendata.euskadi.eus/inicio/) en formato XML. Esta información se va actualizando en la base de datos del proyecto mediante un parser y una tarea Cron.  

Utiliza la API de Mapbox para calcular la ruta del itinerario y gestióna la autorización y autenticación mediante [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum) y [Laravel Gates](https://laravel.com/docs/10.x/authorization#gates).  

# Instrucciones 

Antes de comenzar asegúrate de tener instalado Docker.

- [Docker](https://www.docker.com/)

## 1. Fichero de configuración .env de Laravel

- Sitúate dentro del directorio del proyecto Laravel

```shell
cd euskoplan-api
```

- Copia `.env.example` a `.env`

En Windows

```shell
copy .env.example .env
```

En macOS / Linux

```shell
cp .env.example .env
```

- Modifica el nombre de la aplicación en el fichero `.env`

```text
APP_NAME=Euskoplan
```

- Modifica las credenciales para la BBDD en el fichero `.env`

```text
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=api
DB_USERNAME=user
DB_PASSWORD=password
```

- Modifica los datos para el servidor de e-mail en el fichero `.env`

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

- Añade el token de Mapbox en el fichero `.env`

```text
MAP_BOX_TOKEN="<YOUR_KEY>"
```

## 2. Inicia Docker y arranca los contenedores

- Desde el directorio raíz del proyecto ejecutar el siguiente comando y esperar a que termine:

```shell
docker-compose up -d
```

## 3. Directorio de dependencias vendor y generación de clave cifrado

> :warning: Este paso solo es necesario la primera vez.

- Desde el directorio raíz del proyecto ejecutar el siguiente commando y esperar a que termine la instalación:

```shell
docker-compose exec php composer install
```  

> :warning: Este paso solo es necesario la primera vez.

- Desde el directorio raíz del proyecto ejecutar el siguiente commando:

```shell
docker-compose exec php php artisan key:generate
```  

## 4. Migraciones

- Para generar las tablas, desde el directorio raíz del proyecto ejecutar:

```bash
docker-compose exec php php artisan migrate
```

## 5. Seeders

- Para cargar los datos a la BBDD, desde el directorio raíz del proyecto ejecutar:

```bash
docker-compose exec php php artisan db:seed
```

## 6. Accede a los servicios

- [phpMyAdmin](http://localhost:8081) (credenciales --> db / user / password)
- [Mailhog](http://localhost:8025/)
- [Postman](https://www.postman.com/): Descargar el siguiente [archivo](https://drive.google.com/file/d/1KtY4w0z94aVRbSv4h-5wdPcGCgjUzA68/view?usp=sharing) e importarlo para probar los endpoints
    

## 7. Acceso shell al contenedor de la aplicación

```shell
docker-compose exec php /bin/bash
```  

## 8. Detén los contenedores

```shell
docker-compose stop
``` 

## Otros

- Existe un usuario de prueba para acceder a la aplicación web con las siguientes credenciales:
    - Username: test
    - Email: test@test.com
    - Password: password


- Se puede actualizar la BB DD mediante una tarea Cron lanzando el siguiente comando:

> :warning: Puede tardar horas en completarse.

```bash
docker-compose exec php php artisan collections:cron
```

## Screenshots

<img src="screenshots/screenshot-1.png" width="300">

