## Laravel Photogram API

This version is built on Laravel 5.3. Boilerplate used for this API:

* Laravel API Boilerplate (JWT Edition) - (https://github.com/francescomalatesta/laravel-api-boilerplate-jwt)

It is built on top of three big guys:

* JWT-Auth - [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)
* Dingo API - [dingo/api](https://github.com/dingo/api)
* Laravel-CORS [barryvdh/laravel-cors](http://github.com/barryvdh/laravel-cors)

## Installation

1. git clone git@github.com:dperkosan/photogram_API.git
2. create .env file
3. set permissions (see set permissions section)
4. composer install
5. run the `php artisan migrate` command to install the required tables.

## Set permissions

* sudo chown -R my-user:www-data /path/to/your/root/directory
* sudo find /path/to/your/root/directory -type f -exec chmod 664 {} \;    
* sudo find /path/to/your/root/directory -type d -exec chmod 775 {} \;
* sudo chgrp -R www-data storage bootstrap/cache
* sudo chmod -R ug+rwx storage bootstrap/cache

## Main Features

### Authentication Controllers

There are four controllers you can find in the `App\Api\V1\Controllers` for authentication and password recovery.

For each controller there's an already setup route in `routes/api.php` file:

* `POST api/auth/login`, to do the login and get your access token;<br>
    Expecting parameters:
```
    {
        "email": "user.mail@example.com",
        "password": "123456789"
    }
```
* `POST api/auth/signup`, to create a new user into your application;<br>
    Expecting parameters:
```
    {
        "username": "username",
        "name": "name",
        "email": "user.mail@example.com",
        "password": "123456789"
    }
```
* `POST api/auth/recovery`, to recover your credentials (sends forgot password mail);<br>
    Expecting parameters:
```
    {
        "email": "user.mail@example.com"
    }
```
* `POST api/auth/reset`, to reset your password after the recovery;<br>
    Expecting parameters:
```
    {
        "email": "user.mail@example.com",
        "password": "123456789",
        "password_confirmation": "123456789",
        "token": "3c9eb6063b0d15eaf6e37ffe5f663b3bc9ee2a4c412218ae46e0777d11d14fba"
    }
```

### A Separate File for Routes

All the API routes can be found in the `routes/api.php` file. This also follow the Laravel 5.3 convention.

## Configuration

Project is based on _dingo/api_ and _tymondesigns/jwt-auth_ packages. So, you can find many informations about configuration <a href="https://github.com/tymondesigns/jwt-auth/wiki/Configuration" target="_blank">here</a> and <a href="https://github.com/dingo/api/wiki/Configuration">here</a>.

However, there are some extra options that I placed in a _config/boilerplate.php_ file:

* `sign_up.release_token`: set it to `true` if you want your app release the token right after the sign up process;
* `reset_password.release_token`: set it to `true` if you want your app release the token right after the password reset process;

There are also the validation rules for every action (login, sign up, recovery and reset). Feel free to customize it for your needs.

## Creating Endpoints

You can create endpoints in the same way you could to with using the single _dingo/api_ package. You can <a href="https://github.com/dingo/api/wiki/Creating-API-Endpoints" target="_blank">read its documentation</a> for details.

## Cross Origin Resource Sharing

If you want to enable CORS for a specific route or routes group, you just have to use the _cors_ middleware on them.

Thanks to the _barryvdh/laravel-cors_ package, you can handle CORS easily. Just check <a href="https://github.com/barryvdh/laravel-cors" target="_blank">the docs at this page</a> for more info.
