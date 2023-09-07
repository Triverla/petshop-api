# Petshop API

## Installation
Step 1:<br>
Clone repo using the command below:
```curl
git clone https://github.com/Triverla/petshop-api.git
```
After successfully cloning repo, Open project in your preferred IDE or text editor and proceed with the following steps <br>

**NB:** This application makes use of **Makefile** to help simplify commands<br>

Step 2: <br>
Follow the steps in Configuration [Go to Configuration section](#configuration)

Step 3: <br>
This application was built using **Dockerfile** and **docker-compose** <br>
You can boot this application using any of the following commands<br>
**NB:** Remember to start your docker app
```bash
make up
```
This commands starts the application and runs the migrations and seeders
or

```bash
docker-compose up
```
Then run migrations & seeders using the make command
```bash 
make migration 
```
or
```bash 
docker exec -t petshop bash -c 'php artisan migrate:fresh --seed' 
```

The app should be accessible via `http://localhost:8000`.

## Configuration
Create `.env` file by running the command below
```bash
make env
```
Then update your `.env` files with the following details
```dotenv
#Jwt
JWT_SECRET=test_secret
JWT_ALG=HS256
JWT_MAX_LIFETIME=60
```
Add the variable below for Order Notification package
```dotenv
ORDERS_WEBHOOK_URL=https://webhook.site/2b63b8dc-ddf4-4d74-8d53-17f0df0911f1
```
Also, update **Database** variables in the env file with your values for docker DB configuration

## JWT
The json web token for this application is generated using **firebase/php-jwt**

## Generate IDE Helper

Because everything's handle by Docker, the process for generating `_ide_helper.php` file
is to run the command via the container using the command below

```bash
make idehelper
```

## Larastan and PHP Insights
You can run the following command to use phpstan and php insights <br>
Memory limit is needed in phpstan to prevent memory exhaustion

```bash
make phpinsights
make phpstan
```

## Swagger Docs
Swagger docs can be generated using the command below:
```bash
make swagger
```

Api documentations can be accessed on 
```curl
http://localhost:8000/api/v1/documentation
```

## Tests

```bash
make test
```

## Level 4 Challenge (Order Status Notification)
The package required for this challenge can be found under `packages/order-notification` directory.

The package is implemented in the **OrderObserver** Class. It sends a notification each time the order status is updated.

You can check the included `README.md` for more info.

