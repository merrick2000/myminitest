MyMiniFactory Exercise

This project is a Symfony 4.4 application that allow users to register, login, add products to car and buy them.
Admins can set credits to users and more.

Requirements
PHP 7.1 or higher
Composer
A database (MySQL)
A web server (Apache) with mod_rewrite enabled
Git (optional)
Installation
Clone the repository from GitHub: https://github.com/merrick2000/myminitest/tree/master

Install dependencies
composer install

Edit the .env file and set your environment variables (e.g. database credentials).

Generate the database tables:

php bin/console doctrine:migrations:migrate

Run the server

php bin/console server:start

Once server is running

Create a new admin user with this command

php bin/console app:create-user <your_password>

Copy the email adresse generated with your password to login at /login and go here for you admin panel /dashboard

Generate fake datas for your test using this command

php bin/console app:generate-fake-data

It will automatically create some users, products and variant

Now you are ready to test the app.

Navigate through the shop, view products details, add them to cart, register youself as new user and by things

Dashboard

Open your web browser and navigate to http://localhost:8000/

Contributing
If you would like to contribute to this project, please follow these steps:

Fork the repository on GitHub.
Create a new branch for your feature: git checkout -b feature-name.
Make your changes and commit them: git commit -am 'Add some feature'.
Push your branch to GitHub: git push origin feature-name.
Create a new pull request on GitHub and describe your changes.
License
Open Source.