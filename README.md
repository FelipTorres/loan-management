# Here are some tips to start

* This project utilizes Laravel Lumen

* You will need composer, docker and docker compose installed

* You must run the command 'composer install --ignore-platform-reqs' as the first step after you clone the project

* After properly start the containers you will have access to some .sh files

 - composer-install.sh
    - This file will import all the necessary libraries for the proper functioning of the project

 - migrations.sh
    - This file will create the database and seed with an example register

 - run-tests.sh
    - This file executes the automated tests stack

## Application ports

 - api: 8000
 - phpmyadmin: 8080
 - swagger: 82

 > [!TIP]
 > You can find the phpmyadmin access info in the .env.example

## Application .csv

* You can find a `.csv` example inside the folder `example/`

* Copy this file and use it as the base example

* You can also find in the same directory one `.json` file with the API endpoint collection to use with [Insomnia](https://insomnia.rest/download)

> [!IMPORTANT]
> Follow the received instructions to complete the tasks
> To populate the partner_companies and employees tables, Seeders were created. The following are the reference classes: PartnerCompaniesSeeder and EmployeesSeeder.
