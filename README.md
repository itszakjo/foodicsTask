
# Foodics Assessment Task

## Intro
This Repo is for Foodics Hiring Task.
The task is building an inventory management system

## Features
1. Simple auth system with Sanctum (for advanced auth system with OAuth2 passport is recommended )
2. API throttling for API rate limits.
3. API versioning.
3. Custom Exception Handler.
4. Validation Through Requests.
5. DB Seeders.
6. Database indexes are used for better efficiency.
7. Repository Pattern is used to decouple hard dependencies of models.
8. Service layer is used to handle the logic.
9. Used interfaces to for better structuring.
10. SOLID principles are applied.  
11. Ingredient Unit Management to handle different units of measurement.
12. DB transactions are applied to handle race conditions.
13. Queues are used to handle email notifications ( for more advanced queue system we can Redis for example)
14. Notifications are configured to queue only after successful db transaction.
15. Using Docker through Laravel Sail to create docker-compose.yaml.
16. Swagger Docs are implemented for better and easier API testing.
17. Unit & Feature Tests are added. 
18. Logging for critical operation and errors. 
19. App is deployed and documented here https://meta.endlessref.com/api/documentation

## potential features for better performance 
1. Events for stock updates and EDA.
2. Redis for caching initial stocks and handling queues.
3. passport for more advanced auth system.



## Prerequisities
 
 You need to have Docker installed on your device.

## Installation & Setup ( docker)

1. ` copy necessary variables from .env.ecample to your .env`

2. Clone the Repository on your machine.
    ```
    git clone https://github.com/itszakjo/foodicsTask
    ```
    ```
    cd foodicsTask
    ```
3. Install project dependencies
    ```
    docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
    ```

4. Configure bash alias for laravel Sail
    ```
    alias sail='bash vendor/bin/sail'
    ```
5. Build and Run the project using detached mode
    ```
    sail up -d
    ```
6. Generate a new key
    ```
    sail artisan key:generate
    ```

7. Migrate & Seed the Database
    ```
    sail artisan migrate --seed
    ```
    
8. To Stop The Containers Insert this command
    ```
    sail stop
    ```
    
9. For testing with sail 
      ```
      sail test
      ```
## Installation ( without docker )

 ```
  
add mysql credentials to .env
 ```
````
$ composer install
$ php artisan key:generate
$ php artisan migrate --seed
$ php artisan serve
```` 

## For Testing 

```
$ php artisan test
```

## API Endpoints
Note: API Endpoints are found with examples on this [Swagger Docs](https://meta.endlessref.com/api/documentation)

#### Auth
```http
X-POST /api/v1/login
```
| Parameters |  |  | example | 
| :--- | :--- | :--- | :---  | 
| `email` | `string` | `required` | user@mail.com |
| `password` | `string` | `required` | password |


```
{
    "access_token": "token"
    "token_type": "bearer",
}
```



#### create order :
```http
x-POST /api/v1/order/create
-H 'Authorization: 'your_token' \

```
| Parameter |  |  |
| :--- | :--- | :--- |
| `produdcts` | `array` |  `required`|
| `products.*.product_id` | `int` | `required` |
| `products.*.quantity` | `int` | `required` |


 
  


### Diagram
![ERD](https://github.com/itszakjo/foodicsTask/blob/master/db_diagram.PNG)